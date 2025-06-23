<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Inicializar mensaje
$mensaje = '';
$tipo_mensaje = '';
$devoluciones = [];

// Obtener listado de devoluciones pendientes
$sql = "SELECT * FROM devoluciones ORDER BY fecha_devolucion DESC";
$devoluciones = obtenerRegistros($sql);

// Procesar cancelación de devolución
if (isset($_POST['cancelar_devolucion'])) {
   $id_devolucion = limpiarDatos($_POST['id_devolucion'] ?? '');
   
   // Obtener información de la devolución
   $sql = "SELECT * FROM devoluciones WHERE id_devolucion = '$id_devolucion'";
   $devolucion = obtenerRegistro($sql);
   
   if (!empty($devolucion)) {
       // Si es una devolución interna, debemos restaurar el inventario
       if ($devolucion['tipo_devolucion'] === 'Interna') {
           $codigo_producto = $devolucion['codigo_producto'];
           $cantidad = $devolucion['cantidad'];
           
           // Obtener producto del inventario
           $sql = "SELECT * FROM inventario WHERE codigo = '$codigo_producto'";
           $producto = obtenerRegistro($sql);
           
           if (!empty($producto)) {
               // Restaurar el stock
               $nuevo_stock = $producto['stock_actual'] + $cantidad;
               actualizarRegistro('inventario', ['stock_actual' => $nuevo_stock], "codigo = '$codigo_producto'");
           }
       }
       
       // Eliminar la devolución
       $resultado = eliminarRegistro('devoluciones', "id_devolucion = '$id_devolucion'");
       
       if ($resultado) {
           $mensaje = "Devolución cancelada correctamente.";
           $tipo_mensaje = "success";
           // Actualizar la lista de devoluciones
           $devoluciones = obtenerRegistros($sql);
       } else {
           $mensaje = "Error al cancelar la devolución: " . mysqli_error($conexion);
           $tipo_mensaje = "error";
       }
   } else {
       $mensaje = "Devolución no encontrada.";
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
       
       .tabla-devoluciones {
           width: 100%;
           border-collapse: collapse;
           margin-top: 20px;
       }
       
       .tabla-devoluciones th, .tabla-devoluciones td {
           padding: 10px;
           text-align: left;
           border-bottom: 1px solid var(--border-color);
       }
       
       .tabla-devoluciones th {
           background-color: var(--primary-color);
           color: var(--light-color);
       }
       
       .tabla-devoluciones tr:nth-child(even) {
           background-color: rgba(138, 78, 50, 0.05);
       }
       
       .tabla-devoluciones tr:hover {
           background-color: rgba(138, 78, 50, 0.1);
       }
       
       .tipo {
           display: inline-block;
           padding: 4px 8px;
           border-radius: 4px;
           font-weight: bold;
           font-size: 0.9em;
       }
       
       .tipo-interna {
           background-color: rgba(192, 41, 79, 0.2);
           color: #c02950;
       }
       
       .tipo-externa {
           background-color: rgba(134, 172, 65, 0.2);
           color: #86ac41;
       }
       
       .btn-cancelar {
           background-color: #c02950;
           color: white;
           border: none;
           padding: 5px 10px;
           border-radius: 5px;
           cursor: pointer;
           transition: background-color 0.3s;
       }
       
       .btn-cancelar:hover {
           background-color: #a0233f;
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
       <h1>Cancelar devoluciones</h1>
       <ul>
           <li>Seleccione la devolución que desea cancelar</li>
           <li>Confirme la acción</li>
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
 <h1>Cancelar Devoluciones</h1>
 
 <?php if (!empty($mensaje)): ?>
   <div class="mensaje <?php echo $tipo_mensaje; ?>">
       <?php echo $mensaje; ?>
   </div>
 <?php endif; ?>
 
 <?php if (empty($devoluciones)): ?>
   <p>No hay devoluciones registradas para cancelar.</p>
 <?php else: ?>
   <table class="tabla-devoluciones">
       <thead>
           <tr>
               <th>ID</th>
               <th>Tipo</th>
               <th>Número Pedido/Factura</th>
               <th>Código Producto</th>
               <th>Cantidad</th>
               <th>Fecha</th>
               <th>Acción</th>
           </tr>
       </thead>
       <tbody>
           <?php foreach ($devoluciones as $devolucion): 
               // Determinar clase CSS para el tipo de devolución
               $tipoClass = ($devolucion['tipo_devolucion'] === 'Interna') ? 'tipo-interna' : 'tipo-externa';
           ?>
               <tr>
                   <td><?php echo $devolucion['id_devolucion']; ?></td>
                   <td>
                       <span class="tipo <?php echo $tipoClass; ?>">
                           <?php echo $devolucion['tipo_devolucion']; ?>
                       </span>
                   </td>
                   <td><?php echo $devolucion['numero_pedido_factura']; ?></td>
                   <td><?php echo $devolucion['codigo_producto']; ?></td>
                   <td><?php echo $devolucion['cantidad'] . ' ' . $devolucion['unidad_medida']; ?></td>
                   <td><?php echo date('d/m/Y H:i', strtotime($devolucion['fecha_devolucion'])); ?></td>
                   <td>
                       <form method="post" action="" onsubmit="return confirmarCancelacion()">
                           <input type="hidden" name="id_devolucion" value="<?php echo $devolucion['id_devolucion']; ?>">
                           <button type="submit" name="cancelar_devolucion" class="btn-cancelar">Cancelar</button>
                       </form>
                   </td>
               </tr>
           <?php endforeach; ?>
       </tbody>
   </table>
 <?php endif; ?>
 
 <script>
   function confirmarCancelacion() {
       return confirm("¿Está seguro que desea cancelar esta devolución? Esta acción no se puede deshacer.");
   }
 </script>
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