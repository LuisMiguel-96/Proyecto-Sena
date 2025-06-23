<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Inicializar mensaje
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Recoger los datos del formulario
   $tipo_devolucion = limpiarDatos($_POST['tipo_devolucion'] ?? '');
   $numero_pedido = limpiarDatos($_POST['numero_pedido'] ?? '');
   $codigo_producto = limpiarDatos($_POST['codigo_producto'] ?? '');
   $lote = limpiarDatos($_POST['lote'] ?? '');
   $cantidad = limpiarDatos($_POST['cantidad'] ?? '');
   $unidad_medida = limpiarDatos($_POST['unidad_medida'] ?? '');
   $novedades = limpiarDatos($_POST['novedades'] ?? '');
   
   // Convertir los valores del select a texto para almacenar en la base de datos
   if ($tipo_devolucion === '1') {
       $tipo_devolucion = 'Interna';
   } elseif ($tipo_devolucion === '2') {
       $tipo_devolucion = 'Externa';
   }
   
   if ($unidad_medida === '1') {
       $unidad_medida = 'Unidades';
   } elseif ($unidad_medida === '2') {
       $unidad_medida = 'Kilogramo';
   }
   
   // Preparar los datos para insertar en la base de datos
   $datos = [
       'tipo_devolucion' => $tipo_devolucion,
       'numero_pedido_factura' => $numero_pedido,
       'codigo_producto' => $codigo_producto,
       'lote' => $lote,
       'cantidad' => $cantidad,
       'unidad_medida' => $unidad_medida,
       'novedades' => $novedades
   ];
   
   // Insertar en la base de datos
   $resultado = insertarRegistro('devoluciones', $datos);
   
   if ($resultado) {
       // Actualizar el inventario si es una devolución que afecta al stock
       if ($tipo_devolucion === 'Interna') {
           // Es una devolución interna, debemos reducir el stock
           $sql = "SELECT * FROM inventario WHERE codigo = '$codigo_producto'";
           $producto = obtenerRegistro($sql);
           
           if (!empty($producto)) {
               // El producto existe, actualizamos el stock
               $nuevo_stock = $producto['stock_actual'] - $cantidad;
               if ($nuevo_stock < 0) $nuevo_stock = 0; // Evitar stock negativo
               
               actualizarRegistro('inventario', ['stock_actual' => $nuevo_stock], "codigo = '$codigo_producto'");
           }
       }
       
       $mensaje = "Devolución registrada correctamente.";
       $tipo_mensaje = "success";
   } else {
       $mensaje = "Error al registrar la devolución: " . mysqli_error($conexion);
       $tipo_mensaje = "error";
   }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <link rel="icon" type="images.jpg" href="Ama.jpeg">
   <meta charset="utf-8">
   <meta name="description" content="Pagina Web de AMA Chocolates una Mypime que apoya a los desmovilizados que buscan una nueva oportunidad">
   <meta properity="og:title" content="Ama Chocolates el color del cacao en zonas de conflicto">
   <link rel="stylesheet" href="../sttlecss.css">
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
   </style>
</head>
<body>
<section id="container">
<header class="Logo">
           <a href="../index.html">
               <img
               src="Logo.Amay.jpg"
               title="Logo corporativo de Ama Chocolates"
               alt="Logo corporativo de Ama Chocolates"
               width=800px
               height=125px>
       </a>
</header>
<aside>
<div class="Atras">
   <a href="../Devoluciones.html">Atras</a>
    </div>
   <dialog id="dialog">
       <h1>
           Crear Devoluciones
       </h1>
       <ul>
           <li>ingresar correctamente los items a eliminar.</li>
           <li>proceso unicamente asignado al supervisor o Administrador.</li>
       </ul>
       <button id="hide">Cerrar</button>
   </dialog>
   <button id="show">Ver información</button>
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
 <h1>Crear Devoluciones</h1>
 
 <?php if (!empty($mensaje)): ?>
   <div class="mensaje <?php echo $tipo_mensaje; ?>">
       <?php echo $mensaje; ?>
   </div>
 <?php endif; ?>
 
 <form method="post" action="">
   <fieldset>
      <legend><h2>Devoluciones</h2></legend>
      <div>
       <label for="tipo_devolucion">Tipo de devolución:</label>
       <select 
       class="control"
       name="tipo_devolucion" id="tipo_devolucion" required>
               <option value=""></option>
               <option value="1">Interna</option>
               <option value="2">Externa</option>
       </select>
       </div> 
      <div>
      <label>
       Número de pedido/Factura: 
       <input 
       class="control"
       type="number" 
       name="numero_pedido" 
       placeholder="Digite el Número de Pedido/factura"
       required>
      </label>
       </div>
      <div>
      <label>
       Código del producto: 
       <input 
       class="control"
       type="number"
       name="codigo_producto"
       placeholder="Código del producto"
       required>
      </label>
       </div>
      <div>
      <label>
       Lote: 
       <input 
       class="control"
       type="text"
       name="lote"
       placeholder="Ingrese Lote"
       required>
      </label>
       </div>
      <div>
      <label>
       Cantidad: 
       <input 
       class="control"
       type="number"
       name="cantidad"
       placeholder="Cantidad"
       required>
      </label>
       </div>
       <div>
       <label for="unidad_medida">Unidad de medida:</label>
       <select class="control"
       name="unidad_medida" id="unidad_medida" required>
               <option value=""></option>
               <option value="1">Unidades</option>
               <option value="2">Kilogramo</option>
       </select>
       </div> 
      </label>
      <div class="novedades-container">
       <div class="novedades-title">Novedades</div>
       <textarea class="novedades" name="novedades" placeholder="Escribe aquí las últimas novedades..."></textarea>
       </div>
       <button type="submit" class="btn">Enviar</button>
       <button type="reset">Refrescar</button>
   </fieldset>
 </form>
 
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