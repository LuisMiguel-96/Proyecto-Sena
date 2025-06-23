<?php
$host = "localhost";     // Servidor de base de datos
$usuario = "root";       // Usuario de MySQL
$password = "";          // Contraseña (vacía como solicitado)
$base_datos = "ama_chocolates"; // Nombre de la base de datos

// Crear conexión
$conexion = mysqli_connect($host, $usuario, $password, $base_datos);

// Verificar la conexión
if (!$conexion) {
   die("Error de conexión: " . mysqli_connect_error());
}

// Establecer el conjunto de caracteres a utf8
mysqli_set_charset($conexion, "utf8");

function ejecutarConsulta($sql) {
   global $conexion;
   $resultado = mysqli_query($conexion, $sql);
   return $resultado;
}

function obtenerRegistro($sql) {
   $resultado = ejecutarConsulta($sql);
   if ($resultado && mysqli_num_rows($resultado) > 0) {
       return mysqli_fetch_assoc($resultado);
   }
   return [];
}

function obtenerRegistros($sql) {
   $resultado = ejecutarConsulta($sql);
   $registros = [];
   
   if ($resultado && mysqli_num_rows($resultado) > 0) {
       while ($fila = mysqli_fetch_assoc($resultado)) {
           $registros[] = $fila;
       }
   }
   
   return $registros;
}

function limpiarDatos($datos) {
   global $conexion;
   $datos = trim($datos);
   $datos = stripslashes($datos);
   $datos = htmlspecialchars($datos);
   $datos = mysqli_real_escape_string($conexion, $datos);
   return $datos;
}

function insertarRegistro($tabla, $datos) {
   global $conexion;
   
   $campos = array_keys($datos);
   $valores = array_values($datos);
   
   // Limpiar y preparar valores
   $valoresLimpios = array_map(function($valor) {
       global $conexion;
       if ($valor === NULL) {
           return "NULL";
       }
       return "'" . mysqli_real_escape_string($conexion, $valor) . "'";
   }, $valores);
   
   $sql = "INSERT INTO " . $tabla . " (" . implode(", ", $campos) . ") VALUES (" . implode(", ", $valoresLimpios) . ")";
   
   if (ejecutarConsulta($sql)) {
       return mysqli_insert_id($conexion);
   }
   
   return false;
}

function actualizarRegistro($tabla, $datos, $condicion) {
   global $conexion;
   
   $actualizaciones = [];
   
   foreach ($datos as $campo => $valor) {
       if ($valor === NULL) {
           $actualizaciones[] = $campo . " = NULL";
       } else {
           $actualizaciones[] = $campo . " = '" . mysqli_real_escape_string($conexion, $valor) . "'";
       }
   }
   
   $sql = "UPDATE " . $tabla . " SET " . implode(", ", $actualizaciones) . " WHERE " . $condicion;
   
   return ejecutarConsulta($sql) ? true : false;
}

function eliminarRegistro($tabla, $condicion) {
   $sql = "DELETE FROM " . $tabla . " WHERE " . $condicion;
   return ejecutarConsulta($sql) ? true : false;
}
?>