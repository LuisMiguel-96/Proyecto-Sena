<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Variable para almacenar la mercancía
$mercancia = [];
$busqueda = '';
$filtro = '';

// Manejar la búsqueda/filtrado
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
   $busqueda = limpiarDatos($_GET['busqueda']);
   $filtro = limpiarDatos($_GET['filtro'] ?? 'codigo_producto');
   
   // Construir la consulta SQL con el filtro
   $sql = "SELECT * FROM ingresos_mercancia WHERE $filtro LIKE '%$busqueda%' ORDER BY fecha_ingreso DESC";
} else {
   // Consulta por defecto sin filtros
   $sql = "SELECT * FROM ingresos_mercancia ORDER BY fecha_ingreso DESC";
}

// Obtener la mercancía
$mercancia = obtenerRegistros($sql);
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
       .tabla-mercancia {
           width: 100%;
           border-collapse: collapse;
           margin-top: 20px;
       }
       
       .tabla-mercancia th, .tabla-mercancia td {
           padding: 10px;
           text-align: left;
           border-bottom: 1px solid var(--border-color);
       }
       
       .tabla-mercancia th {
           background-color: var(--primary-color);
           color: var(--light-color);
       }
       
       .tabla-mercancia tr:nth-child(even) {
           background-color: rgba(138, 78, 50, 0.05);
       }
       
       .tabla-mercancia tr:hover {
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
   <dialog id="dialog">
       <h1>Mercancia Ingresada</h1>
       <ul>
           <li>Ver toda la mercancia Ingresada</li>
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
</aside>
<main>
 <h1>Mercancia ingresada</h1>
 
 <form class="busqueda-container" method="GET" action="">
   <select name="filtro">
       <option value="codigo_producto" <?php echo ($filtro === 'codigo_producto') ? 'selected' : ''; ?>>Código</option>
       <option value="lote" <?php echo ($filtro === 'lote') ? 'selected' : ''; ?>>Lote</option>
       <option value="numero_pedido" <?php echo ($filtro === 'numero_pedido') ? 'selected' : ''; ?>>Número Pedido</option>
       <option value="nit_proveedor" <?php echo ($filtro === 'nit_proveedor') ? 'selected' : ''; ?>>NIT Proveedor</option>
       <option value="categoria" <?php echo ($filtro === 'categoria') ? 'selected' : ''; ?>>Categoría</option>
   </select>
   <input type="text" name="busqueda" placeholder="Buscar mercancía..." value="<?php echo htmlspecialchars($busqueda); ?>">
   <button type="submit">Buscar</button>
   <button type="button" onclick="window.location.href='MercanciaIngresada.php'">Mostrar todo</button>
 </form>
 
 <?php if (empty($mercancia)): ?>
   <p>No se encontraron registros de mercancía ingresada.</p>
 <?php else: ?>
   <table class="tabla-mercancia">
       <thead>
           <tr>
               <th>ID</th>
               <th>Número Pedido</th>
               <th>NIT Proveedor</th>
               <th>Código Producto</th>
               <th>Cantidad</th>
               <th>Lote</th>
               <th>Categoría</th>
               <th>Fecha Ingreso</th>
           </tr>
       </thead>
       <tbody>
           <?php foreach ($mercancia as $item): ?>
               <tr>
                   <td><?php echo $item['id_ingreso']; ?></td>
                   <td><?php echo $item['numero_pedido']; ?></td>
                   <td><?php echo $item['nit_proveedor']; ?></td>
                   <td><?php echo $item['codigo_producto']; ?></td>
                   <td><?php echo $item['cantidad']; ?></td>
                   <td><?php echo $item['lote']; ?></td>
                   <td><?php echo $item['categoria']; ?></td>
                   <td><?php echo date('d/m/Y H:i', strtotime($item['fecha_ingreso'])); ?></td>
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