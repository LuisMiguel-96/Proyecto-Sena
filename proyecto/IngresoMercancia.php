<?php
// Incluir archivo de conexión
require_once('conexion.php');

// Inicializar mensaje
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Recoger los datos del formulario
   $numero_pedido = limpiarDatos($_POST['numero_pedido'] ?? '');
   $nit_proveedor = limpiarDatos($_POST['nit_proveedor'] ?? '');
   $codigo_producto = limpiarDatos($_POST['codigo_producto'] ?? '');
   $cantidad = limpiarDatos($_POST['cantidad'] ?? '');
   $lote = limpiarDatos($_POST['lote'] ?? '');
   $categoria = limpiarDatos($_POST['categoria'] ?? '');
   
   // Preparar los datos para insertar en la base de datos
   $datos = [
       'numero_pedido' => $numero_pedido,
       'nit_proveedor' => $nit_proveedor,
       'codigo_producto' => $codigo_producto,
       'cantidad' => $cantidad,
       'lote' => $lote,
       'categoria' => $categoria
   ];
   
   // Insertar en la base de datos
   $resultado = insertarRegistro('ingresos_mercancia', $datos);
   
   if ($resultado) {
       // Actualizar el inventario
       // Primero verificamos si el producto existe en el inventario
       $sql = "SELECT * FROM inventario WHERE codigo = '$codigo_producto'";
       $producto = obtenerRegistro($sql);
       
       if (!empty($producto)) {
           // El producto existe, actualizamos el stock
           $nuevo_stock = $producto['stock_actual'] + $cantidad;
           actualizarRegistro('inventario', ['stock_actual' => $nuevo_stock], "codigo = '$codigo_producto'");
       } else {
           // El producto no existe, lo insertamos
           $datos_producto = [
               'codigo' => $codigo_producto,
               'descripcion' => 'Producto con código ' . $codigo_producto,
               'categoria' => $categoria,
               'stock_actual' => $cantidad
           ];
           insertarRegistro('inventario', $datos_producto);
       }
       
       $mensaje = "Mercancía registrada correctamente en el sistema.";
       $tipo_mensaje = "success";
   } else {
       $mensaje = "Error al registrar la mercancía: " . mysqli_error($conexion);
       $tipo_mensaje = "error";
   }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Ingreso de Mercancía - AMA Chocolates</title>
   <link rel="stylesheet" href="sttlecss.css">
   <link rel="icon" type="images.jpg" href="./images/Ama.jpeg">
   <style>
       .mensaje {
           padding: 10px;
           margin-bottom: 15px;
           border-radius: 5px;
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
       <h1>Ingreso de Mercancía</h1>
       <p>En esta sección puede registrar los nuevos productos que ingresan al inventario.</p>
       <small>Todos los campos son obligatorios para un correcto registro.</small>
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
       <?php if (!empty($mensaje)): ?>
           <div id="mensajeRespuesta" class="mensaje <?php echo $tipo_mensaje; ?>">
               <?php echo $mensaje; ?>
           </div>
       <?php endif; ?>
      
       <article>
           <article>
               <section>
                   <h2>Formulario de ingreso de mercancia</h2>
                   <form method="post" action="">
                       <fieldset>
                           <legend>Información del producto</legend>
                           <div>
                           <label>Número de pedido: 
                               <input 
                               class="control"
                               type="number"
                               name="numero_pedido"
                               placeholder="Digite el número de pedido" required>
                           </label>
                           </div>
                           <div>
                             <label>NIT proveedor
                                   <input 
                                   class="control"
                                   type="number" 
                                   name="nit_proveedor"
                                   placeholder="Digite NIT del proveedor" 
                                   required>
                           </label>
                           </div>
                           <div>
                           <label>Código: 
                                <input 
                                class="control"
                                type="number"
                                name="codigo_producto" 
                                placeholder="Digite Código" required>
                               </label>
                           </div> 
                           <div>
                               <label>Cantidad: 
                                   <input 
                                   class="control"
                                   type="number"
                                   name="cantidad" 
                                   placeholder="Digite la cantidad" required>
                                  </label>
                               </div>
                               <div>
                               <label>Lote:
                                   <input 
                                   class="control"
                                   type="text"
                                   name="lote"
                                   placeholder="Ingrese lote"
                                   required>
                               </label>
                               </div>
                                  <div>
                                   <label>Categoría: 
                                        <input 
                                        class="control"
                                        type="text"
                                        name="categoria" 
                                        placeholder="Categoría" required>
                                   </label>
                                   </div> 
                       </fieldset>
                      <button type="submit" class="btn">Confirmar</button>
                       <button type="reset">Refrescar</button>
                   </form>
               </section>
           </article>
       </article>
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

<script>
   // Script para ocultar el mensaje después de 5 segundos
   document.addEventListener('DOMContentLoaded', function() {
       let mensajeElement = document.getElementById('mensajeRespuesta');
       if (mensajeElement) {
           setTimeout(() => {
               mensajeElement.style.display = 'none';
           }, 5000);
       }
   });
</script>
</body>
</html>