<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Variable para almacenar los clientes
$clientes = [];
$busqueda = '';
$filtro = '';

// Manejar la búsqueda/filtrado
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
   $busqueda = limpiarDatos($_GET['busqueda']);
   $filtro = limpiarDatos($_GET['filtro'] ?? 'nombre_completo');
   
   // Construir la consulta SQL con el filtro
   $sql = "SELECT * FROM clientes WHERE $filtro LIKE '%$busqueda%' ORDER BY nombre_completo";
} else {
   // Consulta por defecto sin filtros
   $sql = "SELECT * FROM clientes ORDER BY nombre_completo";
}

// Obtener los clientes
$clientes = obtenerRegistros($sql);
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
       .tabla-clientes {
           width: 100%;
           border-collapse: collapse;
           margin-top: 20px;
       }
       
       .tabla-clientes th, .tabla-clientes td {
           padding: 10px;
           text-align: left;
           border-bottom: 1px solid var(--border-color);
       }
       
       .tabla-clientes th {
           background-color: var(--primary-color);
           color: var(--light-color);
       }
       
       .tabla-clientes tr:nth-child(even) {
           background-color: rgba(138, 78, 50, 0.05);
       }
       
       .tabla-clientes tr:hover {
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
       
       .acciones {
           display: flex;
           gap: 5px;
       }
       
       .acciones a {
           padding: 5px 10px;
           background-color: var(--accent-color);
           color: var(--light-color);
           border-radius: var(--border-radius);
           text-decoration: none;
           font-size: 0.9em;
           transition: var(--transition);
       }
       
       .acciones a:hover {
           background-color: var(--primary-color);
           transform: translateY(-2px);
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
       <a href="../Clientes.html">Atras</a>
        </div>
   <dialog id="dialog">
       <h1>Listado de clientes registrados</h1>
       <ul>
           <li>filtrar por ID, Nombre, fecha, etc.</li>
           <li>Consultar y modificar información acerca de los clientes registrados</li>
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
 <h1>Listado de clientes</h1>
 
 <form class="busqueda-container" method="GET" action="">
   <select name="filtro">
       <option value="nombre_completo" <?php echo ($filtro === 'nombre_completo') ? 'selected' : ''; ?>>Nombre</option>
       <option value="id_cliente" <?php echo ($filtro === 'id_cliente') ? 'selected' : ''; ?>>ID</option>
       <option value="correo" <?php echo ($filtro === 'correo') ? 'selected' : ''; ?>>Correo</option>
       <option value="telefono" <?php echo ($filtro === 'telefono') ? 'selected' : ''; ?>>Teléfono</option>
       <option value="ciudad" <?php echo ($filtro === 'ciudad') ? 'selected' : ''; ?>>Ciudad</option>
   </select>
   <input type="text" name="busqueda" placeholder="Buscar cliente..." value="<?php echo htmlspecialchars($busqueda); ?>">
   <button type="submit">Buscar</button>
   <button type="button" onclick="window.location.href='ListadoCliente.php'">Mostrar todos</button>
 </form>
 
 <?php if (empty($clientes)): ?>
   <p>No se encontraron clientes registrados.</p>
 <?php else: ?>
   <table class="tabla-clientes">
       <thead>
           <tr>
               <th>ID</th>
               <th>Nombre</th>
               <th>Teléfono</th>
               <th>Correo</th>
               <th>Ciudad</th>
           </tr>
       </thead>
       <tbody>
           <?php foreach ($clientes as $cliente): ?>
               <tr>
                   <td><?php echo $cliente['id_cliente']; ?></td>
                   <td><?php echo $cliente['nombre_completo']; ?></td>
                   <td><?php echo $cliente['telefono']; ?></td>
                   <td><?php echo $cliente['correo']; ?></td>
                   <td><?php echo $cliente['ciudad']; ?></td>
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