<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Variable para almacenar facturas
$facturas = [];
$busqueda = '';
$filtro = '';

// Manejar la búsqueda/filtrado
if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
    $busqueda = limpiarDatos($_GET['busqueda']);
    $filtro = limpiarDatos($_GET['filtro'] ?? 'f.numero_factura');
    
    // Construir la consulta SQL con el filtro
    $sql = "SELECT f.*, c.nombre_completo 
            FROM mercancia_facturada f 
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente 
            WHERE $filtro LIKE '%$busqueda%' 
            ORDER BY f.fecha_factura DESC";
} else {
    // Consulta por defecto sin filtros
    $sql = "SELECT f.*, c.nombre_completo 
            FROM mercancia_facturada f 
            LEFT JOIN clientes c ON f.id_cliente = c.id_cliente 
            ORDER BY f.fecha_factura DESC";
}

// Obtener las facturas
$facturas = obtenerRegistros($sql);

// Obtener totales de facturas
$totales_factura = [];
if (!empty($facturas)) {
    foreach ($facturas as $factura) {
        $id_factura = $factura['id_factura'];
        $sql_total = "SELECT SUM(cantidad * precio_unitario) as total 
                      FROM detalles_factura 
                      WHERE id_factura = $id_factura";
        $resultado = obtenerRegistro($sql_total);
        $totales_factura[$id_factura] = isset($resultado['total']) ? $resultado['total'] : 0;
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
        .tabla-pedidos {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .tabla-pedidos th, .tabla-pedidos td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        
        .tabla-pedidos th {
            background-color: var(--primary-color);
            color: var(--light-color);
        }
        
        .tabla-pedidos tr:nth-child(even) {
            background-color: rgba(138, 78, 50, 0.05);
        }
        
        .tabla-pedidos tr:hover {
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
        
        .estado {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .completado {
            background-color: rgba(134, 172, 65, 0.2);
            color: #86ac41;
        }
        
        .pendiente {
            background-color: rgba(255, 193, 7, 0.2);
            color: #856404;
        }
        
        .acciones {
            display: flex;
            gap: 5px;
        }
        
        .accion-btn {
            padding: 5px 10px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
            font-size: 0.9em;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .accion-btn:hover {
            transform: translateY(-2px);
        }
        
        .ver-btn {
            background-color: var(--accent-color);
        }
        
        .crear-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: var(--accent-color);
            color: var(--light-color);
            border-radius: var(--border-radius);
            text-decoration: none;
            margin-bottom: 15px;
            transition: background-color 0.3s, transform 0.2s;
        }
        
        .crear-btn:hover {
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
        <h1>Pedidos de clientes</h1>
        <ul>
            <li>Filtrar por número de factura, cliente, fecha, etc.</li>
            <li>Consultar información acerca de los pedidos facturados a los clientes</li>
            <li>Acceder a los detalles de cada pedido</li>
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
  <h1>Pedidos de clientes</h1>
  
  <a href="../CrearPedido.php" class="crear-btn">Crear Nuevo Pedido</a>
  
  <form class="busqueda-container" method="GET" action="">
    <select name="filtro">
        <option value="f.numero_factura" <?php echo ($filtro === 'f.numero_factura') ? 'selected' : ''; ?>>Número Factura</option>
        <option value="c.nombre_completo" <?php echo ($filtro === 'c.nombre_completo') ? 'selected' : ''; ?>>Nombre Cliente</option>
        <option value="f.fecha_factura" <?php echo ($filtro === 'f.fecha_factura') ? 'selected' : ''; ?>>Fecha</option>
        <option value="f.estado" <?php echo ($filtro === 'f.estado') ? 'selected' : ''; ?>>Estado</option>
    </select>
    <input type="text" name="busqueda" placeholder="Buscar pedido..." value="<?php echo htmlspecialchars($busqueda); ?>">
    <button type="submit">Buscar</button>
    <button type="button" onclick="window.location.href='PedidosCliente.php'">Mostrar todos</button>
  </form>
  
  <?php if (empty($facturas)): ?>
    <p>No se encontraron pedidos facturados.</p>
  <?php else: ?>
    <table class="tabla-pedidos">
        <thead>
            <tr>
                <th>Número Factura</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($facturas as $factura): 
                // Determinar clase CSS para el estado
                $estadoClass = ($factura['estado'] === 'Completo') ? 'completado' : 'pendiente';
            ?>
                <tr>
                    <td><?php echo $factura['numero_factura']; ?></td>
                    <td><?php echo $factura['nombre_completo']; ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($factura['fecha_factura'])); ?></td>
                    <td>$<?php echo number_format($totales_factura[$factura['id_factura']] ?? 0, 2); ?></td>
                    <td>
                        <span class="estado <?php echo $estadoClass; ?>">
                            <?php echo $factura['estado']; ?>
                        </span>
                    </td>
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