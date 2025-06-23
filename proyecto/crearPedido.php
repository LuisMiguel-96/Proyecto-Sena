<?php
// Iniciar sesión 
session_start();

// Incluir archivo de conexión
require_once('conexion.php');

// Inicializar variables
$mensaje = '';
$tipo_mensaje = '';
$clientes = [];
$productos = [];
$items_pedido = [];
$total_pedido = 0;

// Obtener lista de clientes para el selector
$sql_clientes = "SELECT id_cliente, nombre_completo FROM clientes ORDER BY nombre_completo";
$clientes = obtenerRegistros($sql_clientes);

// Obtener productos disponibles
$sql_productos = "SELECT * FROM inventario WHERE stock_actual > 0 ORDER BY codigo";
$productos = obtenerRegistros($sql_productos);

// Si se inicia el formulario
if (isset($_SESSION['pedido_actual'])) {
   $items_pedido = $_SESSION['pedido_actual'];
   
   // Calcular total
   foreach ($items_pedido as $item) {
       $total_pedido += $item['subtotal'];
   }
}

// Agregar producto al pedido
if (isset($_POST['agregar_producto'])) {
   $codigo_producto = limpiarDatos($_POST['codigo_producto']);
   $cantidad = intval($_POST['cantidad']);
   $precio_unitario = floatval($_POST['precio_unitario']);
   
   // Validar datos
   if ($cantidad <= 0 || $precio_unitario <= 0) {
       $mensaje = "La cantidad y el precio deben ser mayores a cero.";
       $tipo_mensaje = "error";
   } else {
       // Buscar producto
       $sql_producto = "SELECT * FROM inventario WHERE codigo = '$codigo_producto'";
       $producto = obtenerRegistro($sql_producto);
       
       if (!empty($producto)) {
           // Verificar stock disponible
           if ($producto['stock_actual'] >= $cantidad) {
               // Verificar si ya existe en el pedido
               $existe = false;
               if (!isset($_SESSION['pedido_actual'])) {
                   $_SESSION['pedido_actual'] = [];
               }
               
               foreach ($_SESSION['pedido_actual'] as $key => $item) {
                   if ($item['codigo_producto'] === $codigo_producto) {
                       // Actualizar cantidad e importe
                       $_SESSION['pedido_actual'][$key]['cantidad'] += $cantidad;
                       $_SESSION['pedido_actual'][$key]['subtotal'] = $_SESSION['pedido_actual'][$key]['cantidad'] * $precio_unitario;
                       $existe = true;
                       break;
                   }
               }
               
               // Si no existe, agregar nuevo
               if (!$existe) {
                   $_SESSION['pedido_actual'][] = [
                       'codigo_producto' => $codigo_producto,
                       'descripcion' => $producto['descripcion'],
                       'cantidad' => $cantidad,
                       'precio_unitario' => $precio_unitario,
                       'subtotal' => $cantidad * $precio_unitario
                   ];
               }
               
               $mensaje = "Producto agregado al pedido.";
               $tipo_mensaje = "success";
               
               // Actualizar variables
               $items_pedido = $_SESSION['pedido_actual'];
               $total_pedido = 0;
               foreach ($items_pedido as $item) {
                   $total_pedido += $item['subtotal'];
               }
           } else {
               $mensaje = "Stock insuficiente. Disponible: " . $producto['stock_actual'];
               $tipo_mensaje = "error";
           }
       } else {
           $mensaje = "Producto no encontrado.";
           $tipo_mensaje = "error";
       }
   }
}

// Eliminar item del pedido
if (isset($_POST['eliminar_item'])) {
   $index = intval($_POST['item_index']);
   
   if (isset($_SESSION['pedido_actual'][$index])) {
       unset($_SESSION['pedido_actual'][$index]);
       $_SESSION['pedido_actual'] = array_values($_SESSION['pedido_actual']); // Reindexar
       $items_pedido = $_SESSION['pedido_actual'];
       
       // Actualizar total
       $total_pedido = 0;
       foreach ($items_pedido as $item) {
           $total_pedido += $item['subtotal'];
       }
       
       $mensaje = "Producto eliminado del pedido.";
       $tipo_mensaje = "success";
   }
}

// Finalizar pedido
if (isset($_POST['finalizar_pedido'])) {
   $id_cliente = limpiarDatos($_POST['id_cliente']);
   
   if (empty($id_cliente)) {
       $mensaje = "Por favor seleccione un cliente.";
       $tipo_mensaje = "error";
   } elseif (empty($_SESSION['pedido_actual'])) {
       $mensaje = "El pedido está vacío.";
       $tipo_mensaje = "error";
   } else {
       // Generar número de factura (con formato FF-AÑOMES-SECUENCIAL)
       $anio_mes = date('Ym');
       $sql_ultimo = "SELECT MAX(id_factura) as ultimo FROM mercancia_facturada";
       $ultimo = obtenerRegistro($sql_ultimo);
       $secuencial = isset($ultimo['ultimo']) ? intval($ultimo['ultimo']) + 1 : 1;
       $numero_factura = "FF-" . $anio_mes . "-" . str_pad($secuencial, 4, '0', STR_PAD_LEFT);
       
       // Insertar en la tabla de facturas
       $datos_factura = [
           'numero_factura' => $numero_factura,
           'id_cliente' => $id_cliente,
           'estado' => 'Completo'
       ];
       
       $id_factura = insertarRegistro('mercancia_facturada', $datos_factura);
       
       if ($id_factura) {
           // Insertar detalles y actualizar inventario
           $exito = true;
           
           foreach ($_SESSION['pedido_actual'] as $item) {
               $datos_detalle = [
                   'id_factura' => $id_factura,
                   'codigo_producto' => $item['codigo_producto'],
                   'cantidad' => $item['cantidad'],
                   'precio_unitario' => $item['precio_unitario']
               ];
               
               $resultado = insertarRegistro('detalles_factura', $datos_detalle);
               
               if ($resultado) {
                   // Actualizar inventario
                   $sql_stock = "SELECT stock_actual FROM inventario WHERE codigo = '{$item['codigo_producto']}'";
                   $stock_info = obtenerRegistro($sql_stock);
                   
                   if (!empty($stock_info)) {
                       $nuevo_stock = $stock_info['stock_actual'] - $item['cantidad'];
                       if ($nuevo_stock < 0) $nuevo_stock = 0;
                       
                       actualizarRegistro('inventario', 
                           ['stock_actual' => $nuevo_stock], 
                           "codigo = '{$item['codigo_producto']}'"
                       );
                   }
               } else {
                   $exito = false;
                   break;
               }
           }
           
           if ($exito) {
               $mensaje = "Pedido finalizado con éxito. Factura: " . $numero_factura;
               $tipo_mensaje = "success";
               // Limpiar sesión
               unset($_SESSION['pedido_actual']);
               $items_pedido = [];
               $total_pedido = 0;
           } else {
               $mensaje = "Error al registrar detalles del pedido.";
               $tipo_mensaje = "error";
           }
       } else {
           $mensaje = "Error al crear la factura: " . mysqli_error($conexion);
           $tipo_mensaje = "error";
       }
   }
}

// Cancelar pedido
if (isset($_POST['cancelar_pedido'])) {
   unset($_SESSION['pedido_actual']);
   $items_pedido = [];
   $total_pedido = 0;
   $mensaje = "Pedido cancelado.";
   $tipo_mensaje = "success";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Crear Pedido - AMA CHOCOLATES</title>
   <link rel="stylesheet" href="sttlecss.css">
   <link rel="icon" type="images.jpg" href="./images/Ama.jpeg">
   <style>
       .mensaje {
           padding: 10px;
           border-radius: 5px;
           margin-bottom: 15px;
           font-weight: bold;
       }
       .success {
           background-color: rgba(134, 172, 65, 0.2);
           color: #86ac41;
           border: 1px solid #86ac41;
       }
       .error {
           background-color: rgba(192, 41, 79, 0.2);
           color: #c02950;
           border: 1px solid #c02950;
       }
       
       .pedido-container {
           display: grid;
           grid-template-columns: 1fr 1fr;
           gap: 20px;
       }
       
       @media (max-width: 768px) {
           .pedido-container {
               grid-template-columns: 1fr;
           }
       }
       
       .pedido-form, .resumen-pedido {
           background-color: var(--light-color);
           border-radius: var(--border-radius);
           padding: 20px;
           box-shadow: var(--shadow);
       }
       
       .resumen-pedido h3 {
           margin-top: 0;
           border-bottom: 1px solid var(--border-color);
           padding-bottom: 10px;
       }
       
       .tabla-pedido {
           width: 100%;
           border-collapse: collapse;
           margin-top: 10px;
           margin-bottom: 20px;
       }
       
       .tabla-pedido th, .tabla-pedido td {
           padding: 8px;
           text-align: left;
           border-bottom: 1px solid var(--border-color);
       }
       
       .tabla-pedido th {
           background-color: var(--primary-color);
           color: var(--light-color);
       }
       
       .total-pedido {
           text-align: right;
           font-weight: bold;
           margin-top: 20px;
           font-size: 1.2em;
       }
       
       .producto-selector {
           background-color: rgba(138, 78, 50, 0.05);
           padding: 15px;
           border-radius: var(--border-radius);
           margin-bottom: 20px;
       }
       
       .finalizar-form {
           margin-top: 20px;
           padding-top: 20px;
           border-top: 1px solid var(--border-color);
       }
       
       .finalizar-form .btn {
           margin-top: 10px;
       }
       
       .eliminar-btn {
           background-color: #c02950;
           color: white;
           border: none;
           padding: 5px 8px;
           border-radius: 4px;
           cursor: pointer;
       }
       
       .eliminar-btn:hover {
           background-color: #a0233f;
       }
   </style>
</head>
<body>
<section id="container">
<header class="Logo">
           <a href="index.html">
               <img
               src="images/Logo.Amay.jpg"
               title="Logo corporativo de Ama Chocolates"
               alt="Logo corporativo de Ama Chocolates"
               width=800px
               height=125px>
       </a>
</header>
<aside>
    <div class="Atras">
        <a href="index.html">Atras</a>
         </div>
    <dialog id="dialog">
        <h1>Crear Pedido</h1>
        <p>En esta sección puede crear nuevos pedidos de productos para los clientes.</p>
        <p>Seleccione los productos, indique cantidades y precios, y finalice el pedido para generar una factura.</p>
        <button id="hide">Cerrar</button>
    </dialog>
    <button id="show">información</button>
    <script>
        window.show.addEventListener('click',()=>{
            window.dialog.showModal()
        })
        window.hide.addEventListener('click', ()=>{
            window.dialog.close()
        })
    </script>
</aside>
<main>
   <h1>Crear Pedido</h1>
   
   <?php if (!empty($mensaje)): ?>
       <div class="mensaje <?php echo $tipo_mensaje; ?>">
           <?php echo $mensaje; ?>
       </div>
   <?php endif; ?>
   
   <div class="pedido-container">
       <!-- Formulario para añadir productos -->
       <div class="pedido-form">
           <h2>Agregar Productos</h2>
           
           <div class="producto-selector">
               <form method="post" action="">
                   <div>
                       <label for="codigo_producto">Producto:</label>
                       <select class="control" name="codigo_producto" id="codigo_producto" required>
                           <option value="">Seleccione un producto</option>
                           <?php foreach ($productos as $producto): ?>
                               <option value="<?php echo $producto['codigo']; ?>">
                                   <?php echo $producto['codigo'] . ' - ' . $producto['descripcion'] . ' (Stock: ' . $producto['stock_actual'] . ')'; ?>
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   
                   <div style="margin-top: 10px;">
                       <label for="cantidad">Cantidad:</label>
                       <input class="control" type="number" name="cantidad" id="cantidad" min="1" value="1" required>
                   </div>
                   
                   <div style="margin-top: 10px;">
                       <label for="precio_unitario">Precio Unitario:</label>
                       <input class="control" type="number" step="0.01" name="precio_unitario" id="precio_unitario" min="0.01" required>
                   </div>
                   
                   <div style="margin-top: 15px;">
                       <button type="submit" name="agregar_producto" class="btn">Agregar al Pedido</button>
                   </div>
               </form>
           </div>
           
           <!-- Finalizar pedido -->
           <div class="finalizar-form">
               <h2>Finalizar Pedido</h2>
               <form method="post" action="">
                   <div>
                       <label for="id_cliente">Cliente:</label>
                       <select class="control" name="id_cliente" id="id_cliente" required>
                           <option value="">Seleccione un cliente</option>
                           <?php foreach ($clientes as $cliente): ?>
                               <option value="<?php echo $cliente['id_cliente']; ?>">
                                   <?php echo $cliente['nombre_completo']; ?>
                               </option>
                           <?php endforeach; ?>
                       </select>
                   </div>
                   
                   <div style="margin-top: 15px; display: flex; gap: 10px;">
                       <button type="submit" name="finalizar_pedido" class="btn">Finalizar Pedido</button>
                       <button type="submit" name="cancelar_pedido" style="background-color: #c02950;">Cancelar</button>
                   </div>
               </form>
           </div>
       </div>
       
       <!-- Resumen del pedido -->
       <div class="resumen-pedido">
           <h3>Resumen del Pedido</h3>
           
           <?php if (empty($items_pedido)): ?>
               <p>No hay productos en el pedido.</p>
           <?php else: ?>
               <table class="tabla-pedido">
                   <thead>
                       <tr>
                           <th>Código</th>
                           <th>Descripción</th>
                           <th>Cantidad</th>
                           <th>Precio</th>
                           <th>Subtotal</th>
                           <th>Acción</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($items_pedido as $index => $item): ?>
                           <tr>
                               <td><?php echo $item['codigo_producto']; ?></td>
                               <td><?php echo $item['descripcion']; ?></td>
                               <td><?php echo $item['cantidad']; ?></td>
                               <td>$<?php echo number_format($item['precio_unitario'], 2); ?></td>
                               <td>$<?php echo number_format($item['subtotal'], 2); ?></td>
                               <td>
                                   <form method="post" action="">
                                       <input type="hidden" name="item_index" value="<?php echo $index; ?>">
                                       <button type="submit" name="eliminar_item" class="eliminar-btn">X</button>
                                   </form>
                               </td>
                           </tr>
                       <?php endforeach; ?>
                   </tbody>
               </table>
               
               <div class="total-pedido">
                   Total: $<?php echo number_format($total_pedido, 2); ?>
               </div>
           <?php endif; ?>
       </div>
   </div>
</main>
<footer>
           <ul>
               <a href="mailto: aiacontable@gmail.com"
               target="_blank"
               rel="noreferrer">Enviame un correo</a>
               <a href="Cel:+573223120271"
               target="_blank"
               rel="noreferrer">Llamar por telefono</a>
               <a href ="https://www.facebook.com/share/aB2vzVfSP7ZEB8zv/"
               target="_blank"
               rel="noreferrer">Facebook</a>
               <a href="https://www.instagram.com/amay_chocolates?igsh=d3Y2NG1rdmF5bDRt"
               target="_blank"
               rel="noreferrer">Instagram</a>
               <li>Direccion Oficina: Cra 50 #99SUR 221 SEGUNDO PISO, La Tablaza, La Estrella, Antioquia</li>
               <li>Telefono <contacto:>3223120271</contacto:></li>    
               </ul>
</footer>
</section>
</body>
</html>