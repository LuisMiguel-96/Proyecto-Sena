<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Variable para almacenar el inventario
$inventario = [];
$busqueda = '';
$filtro = '';

// Manejar la búsqueda/filtrado
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
   $busqueda = limpiarDatos($_GET['busqueda']);
   $filtro = limpiarDatos($_GET['filtro'] ?? 'codigo');
   
   // Construir la consulta SQL con el filtro
   $sql = "SELECT * FROM inventario WHERE $filtro LIKE '%$busqueda%' ORDER BY codigo";
} else {
   // Consulta por defecto sin filtros
   $sql = "SELECT * FROM inventario ORDER BY codigo";
}

// Obtener el inventario
$inventario = obtenerRegistros($sql);
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
       .tabla-inventario {
           width: 100%;
           border-collapse: collapse;
           margin-top: 20px;
       }
       
       .tabla-inventario th, .tabla-inventario td {
           padding: 10px;
           text-align: left;
           border-bottom: 1px solid var(--border-color);
       }
       
       .tabla-inventario th {
           background-color: var(--primary-color);
           color: var(--light-color);
       }
       
       .tabla-inventario tr:nth-child(even) {
           background-color: rgba(138, 78, 50, 0.05);
       }
       
       .tabla-inventario tr:hover {
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
       
       .alerta {
           padding: 5px 10px;
           border-radius: 4px;
           font-weight: bold;
           display: inline-block;
       }
       
       .stock-bajo {
           background-color: rgba(192, 41, 79, 0.2);
           color: #c02950;
       }
       
       .stock-medio {
           background-color: rgba(255, 193, 7, 0.2);
           color: #856404;
       }
       
       .stock-alto {
           background-color: rgba(134, 172, 65, 0.2);
           color: #86ac41;
       }
   </style>
</head>
<body>
<section id="container">
<header class="Logo">
           <a href="../index.html">
               <img
               src="../images/Logo.Amay.jpg"
               title="Logo corporativo de Ama Chocolates"
               alt="Logo corporativo de Ama Chocolates"
               width=800px
               height=125px>
       </a>
</header>
<aside>
<div class="Atras">
   <a href="../inventario.html">Atras</a>
    </div>
    <nav>  
       <dialog id="dialog">
           <h1>Stock</h1>
           <ul>
               <li>Ver toda la mercancia en existencia</li>
               <li>Filtrar por codigo, lote, descripción, etc.</li>
               <li>Ingresar correctamente la descripción que desea buscar</li>
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
   </nav>
</aside>
<main>
   <h1>Stock Total</h1>
   
   <form class="busqueda-container" method="GET" action="">
       <select name="filtro">
           <option value="codigo" <?php echo ($filtro === 'codigo') ? 'selected' : ''; ?>>Código</option>
           <option value="descripcion" <?php echo ($filtro === 'descripcion') ? 'selected' : ''; ?>>Descripción</option>
           <option value="categoria" <?php echo ($filtro === 'categoria') ? 'selected' : ''; ?>>Categoría</option>
       </select>
       <input type="text" name="busqueda" placeholder="Buscar en inventario..." value="<?php echo htmlspecialchars($busqueda); ?>">
       <button type="submit">Buscar</button>
       <button type="button" onclick="window.location.href='StockTotal.php'">Mostrar todo</button>
   </form>
   
   <?php if (empty($inventario)): ?>
       <p>No se encontraron productos en el inventario.</p>
   <?php else: ?>
       <table class="tabla-inventario">
           <thead>
               <tr>
                   <th>Código</th>
                   <th>Descripción</th>
                   <th>Categoría</th>
                   <th>Stock Actual</th>
                   <th>Última Actualización</th>
               </tr>
           </thead>
           <tbody>
               <?php foreach ($inventario as $producto): 
                   // Determinar clase CSS para el stock
                   $stockClass = '';
                   $stockActual = intval($producto['stock_actual']);
                   
                   if ($stockActual <= 10) {
                       $stockClass = 'stock-bajo';
                   } elseif ($stockActual <= 30) {
                       $stockClass = 'stock-medio';
                   } else {
                       $stockClass = 'stock-alto';
                   }
               ?>
                   <tr>
                       <td><?php echo $producto['codigo']; ?></td>
                       <td><?php echo $producto['descripcion']; ?></td>
                       <td><?php echo $producto['categoria']; ?></td>
                       <td>
                           <span class="alerta <?php echo $stockClass; ?>">
                               <?php echo $producto['stock_actual']; ?>
                           </span>
                       </td>
                       <td><?php echo date('d/m/Y H:i', strtotime($producto['fecha_actualizacion'])); ?></td>
                   </tr>
               <?php endforeach; ?>
           </tbody>
       </table>
   <?php endif; ?>
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