<?php
// Incluir archivo de conexión
require_once('../conexion.php');

// Inicializar mensaje
$mensaje = '';
$tipo_mensaje = '';

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   // Recoger los datos del formulario
   $nombre_completo = limpiarDatos($_POST['nombre_completo'] ?? '');
   $fecha_nacimiento = limpiarDatos($_POST['fecha_nacimiento'] ?? '');
   $genero = limpiarDatos($_POST['genero'] ?? '');
   $telefono = limpiarDatos($_POST['telefono'] ?? '');
   $correo = limpiarDatos($_POST['correo'] ?? '');
   $direccion = limpiarDatos($_POST['direccion'] ?? '');
   $ciudad = limpiarDatos($_POST['ciudad'] ?? '');
   $municipio = limpiarDatos($_POST['municipio'] ?? '');
   $codigo_postal = limpiarDatos($_POST['codigo_postal'] ?? '');
   $razon_social = limpiarDatos($_POST['razon_social'] ?? '');
   $identificacion_fiscal = limpiarDatos($_POST['identificacion_fiscal'] ?? '');
   $cargo = limpiarDatos($_POST['cargo'] ?? '');
   
   // Preparar los datos para insertar en la base de datos
   $datos = [
       'nombre_completo' => $nombre_completo,
       'fecha_nacimiento' => $fecha_nacimiento,
       'genero' => $genero,
       'telefono' => $telefono,
       'correo' => $correo,
       'direccion' => $direccion,
       'ciudad' => $ciudad,
       'municipio' => $municipio,
       'codigo_postal' => $codigo_postal,
       'razon_social' => $razon_social,
       'identificacion_fiscal' => $identificacion_fiscal,
       'cargo' => $cargo
   ];
   
   // Insertar en la base de datos
   $resultado = insertarRegistro('clientes', $datos);
   
   if ($resultado) {
       $mensaje = "Cliente registrado correctamente.";
       $tipo_mensaje = "success";
   } else {
       $mensaje = "Error al registrar el cliente: " . mysqli_error($conexion);
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
       <a href="../Clientes.html">Atras</a>
        </div>
   <dialog id="dialog">
       <h1>Crear nuevo cliente</h1>
       <ul>
           <li>Verificar la informacion a ingresar</li>
           <li>Tener en cuenta una consulta previa en listado clientes.</li>
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
   <?php if (!empty($mensaje)): ?>
       <div class="mensaje <?php echo $tipo_mensaje; ?>">
           <?php echo $mensaje; ?>
       </div>
   <?php endif; ?>
   
   <article>
       <section>
           <h2>Formulario para un nuevo cliente</h2>
           <form method="post" action="">
               <fieldset>
                   <legend>
                       <h3>Información General</h3>
                   </legend>
                   <div>
                   <label>Nombre completo: 
                       <input 
                       class="control"
                       type="text"
                       name="nombre_completo"
                       placeholder="Nombres y Apellidos" required>
                   </label>
                   </div>
                   <div>
                     <label>Fecha de nacimiento: 
                           <input 
                           class="control"
                           type="date" 
                           name="fecha_nacimiento"
                           required>
                   </label>
                   </div>
                   <div>
                   <label for="genero">Género:</label>
                   <select class="control" name="genero" id="genero">
                       <option value=""></option>
                       <option value="Masculino">Masculino</option>
                       <option value="Femenino">Femenino</option>
                       <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                   </select>
                   </div> 
               </fieldset>
               <fieldset>
                   <legend>
                       <h3>Información de Contacto</h3>
                   </legend>
                   <div>
                       <label>Teléfono: 
                           <input 
                           class="control"
                           type="tel"
                           name="telefono" 
                           placeholder="Digite número de teléfono" required>
                          </label>
                       </div>
                       <div>
                       <label>Correo:
                           <input 
                           class="control"
                           type="email"
                           name="correo"
                           placeholder="Correo electrónico"
                           required>
                       </label>
                       </div>
                          <div>   
                           <label>Dirección: 
                                <input 
                                class="control"
                                type="text"
                                name="direccion" 
                                placeholder="Dirección" required>
                               </label>
                               <section>
                                <label>
                                 Ciudad <input 
                                 class="control"
                                 type="text"
                                 name="ciudad" 
                                 placeholder="Ciudad" required>
                               </label>
                               </section>
                               <section>
                               <label>
                                 Municipio <input
                                 class="control" 
                                 type="text"
                                 name="municipio" 
                                 placeholder="Municipio" required>
                               </section>
                               </label>
                           </div> 
                       <div>
                           <label>Código postal:
                              <input 
                              class="control"
                              type="number"
                              name="codigo_postal"
                              placeholder="Ingrese código postal"
                              required> 
                           </label>
                       </div>
               </fieldset>
               <fieldset>
                   <legend><h3>Datos Comerciales (si aplica)</h3></legend>
                       <div>
                           <label>Razón social
                               <input 
                               class="control"
                               type="text"
                               name="razon_social"
                               placeholder="Razón social">
                           </label>
                       </div>

                       <div>
                           <label>Número de identificación fiscal: 
                               <input 
                               class="control"
                               type="text"
                               name="identificacion_fiscal"
                               placeholder="Digite ID">
                           </label>
                       </div>

                       <div>
                           <label>Cargo o puesto:
                               <input 
                               class="control"
                               type="text"
                               name="cargo"
                               placeholder="Cargo o puesto">
                           </label>
                       </div>
               </fieldset>
               <button type="submit" class="btn">Confirmar</button>
               <button type="reset">Refrescar</button>
           </form>
       </section>
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
</body>
</html>