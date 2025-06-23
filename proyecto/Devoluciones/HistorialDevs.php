<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Variable para almacenar devoluciones
$devoluciones = [];
$busqueda = '';
$filtro = '';

// Manejar la búsqueda/filtrado
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
   $busqueda = limpiarDatos($_GET['busqueda']);
   $filtro = limpiarDatos($_GET['filtro'] ?? 'numero_pedido_factura');
   
   // Construir la consulta SQL con el filtro
   $sql = "SELECT * FROM devoluciones WHERE $filtro LIKE '%$busqueda%' ORDER BY fecha_devolucion DESC";
} else {
   // Consulta por defecto sin filtros
   $sql = "SELECT * FROM devoluciones ORDER BY fecha_devolucion DESC";
}

// Obtener las devoluciones
$devoluciones = obtenerRegistros($sql);
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
       
       .busqueda-container {
           margin-bottom: 20px;
           display: flex;
           gap: 10px;
           flex-wrap: wrap;
       }
       
       .busqueda-container select, 
       .busqueda-container input[type="text"],
       .busqueda-container button {
           padding: 8px;
           border: 1px solid var(--border-color);
           border-radius: var(--border-radius);
       }
       
       .busqueda-container button {
           background-color: var(--accent-color);
           color: var(--light-color);
           cursor: pointer;
           transition: var(--transition);
       }
       
       .busqueda-container button:hover {
           background-color: var(--primary-color);
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
       
       .ver-detalle {
           cursor: pointer;
           color: var(--accent-color);
           text-decoration: underline;
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
       <h1>Historial de devoluciones</h1>
       <ul>
           <li>Filtrar por ID, número de pedido, fecha, etc.</li>
           <li>Consultar información acerca de las devoluciones realizadas</li>
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
 <h1>Historial de devoluciones</h1>
 
 <form class="busqueda-container" method="GET" action="">
   <select name="filtro">
       <option value="numero_pedido_factura" <?php echo ($filtro === 'numero_pedido_factura') ? 'selected' : ''; ?>>Número Pedido/Factura</option>
       <option value="codigo_producto" <?php echo ($filtro === 'codigo_producto') ? 'selected' : ''; ?>>Código Producto</option>
       <option value="tipo_devolucion" <?php echo ($filtro === 'tipo_devolucion') ? 'selected' : ''; ?>>Tipo</option>
       <option value="lote" <?php echo ($filtro === 'lote') ? 'selected' : ''; ?>>Lote</option>
       <option value="fecha_devolucion" <?php echo ($filtro === 'fecha_devolucion') ? 'selected' : ''; ?>>Fecha</option>
   </select>
   <input type="text" name="busqueda" placeholder="Buscar devolución..." value="<?php echo htmlspecialchars($busqueda); ?>">
   <button type="submit">Buscar</button>
   <button type="button" onclick="window.location.href='HistorialDevs.php'">Mostrar todo</button>
 </form>
 
 <?php if (empty($devoluciones)): ?>
   <p>No se encontraron registros de devoluciones.</p>
 <?php else: ?>
   <table class="tabla-devoluciones">
       <thead>
           <tr>
               <th>ID</th>
               <th>Tipo</th>
               <th>Número Pedido/Factura</th>
               <th>Código Producto</th>
               <th>Lote</th>
               <th>Cantidad</th>
               <th>Fecha</th>
               <th>Novedades</th>
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
                   <td><?php echo $devolucion['lote']; ?></td>
                   <td><?php echo $devolucion['cantidad'] . ' ' . $devolucion['unidad_medida']; ?></td>
                   <td><?php echo date('d/m/Y H:i', strtotime($devolucion['fecha_devolucion'])); ?></td>
                   <td>
                       <?php if (!empty($devolucion['novedades'])): ?>
                           <span class="ver-detalle" onclick="mostrarNovedades('<?php echo addslashes($devolucion['novedades']); ?>')">Ver detalles</span>
                       <?php else: ?>
                           <em>Sin novedades</em>
                       <?php endif; ?>
                   </td>
               </tr>
           <?php endforeach; ?>
       </tbody>
   </table>
 <?php endif; ?>

 <!-- Diálogo para mostrar novedades -->
 <dialog id="dialogNovedades">
   <h2>Novedades de la devolución</h2>
   <p id="textoNovedades"></p>
   <button id="cerrarNovedades">Cerrar</button>
 </dialog>

 <script>
   // Función para mostrar las novedades en un diálogo
   function mostrarNovedades(texto) {
       document.getElementById('textoNovedades').textContent = texto;
       document.getElementById('dialogNovedades').showModal();
   }

   // Cerrar el diálogo de novedades
   document.getElementById('cerrarNovedades').addEventListener('click', () => {
       document.getElementById('dialogNovedades').close();
   });
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