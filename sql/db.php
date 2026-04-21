<?php 
// --- PARÁMETROS DE CONEXIÓN AL SGBD MARIADB ---
$servidor = "localhost";
$usuario = "root";
$password = "";
$bd = "enjambre";

// --- INICIALIZACIÓN DE LA CONEXIÓN ---
$conexion = new mysqli($servidor, $usuario, $password, $bd);

// --- GESTIÓN DE ERRORES ---
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
