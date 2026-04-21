<?php
session_start();
require_once("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $pass = $_POST["password"];

    $sql = "SELECT id_usuario, nombre, password_hash FROM USUARIO WHERE nombre = '$nombre'";
    $resultado = $conexion->query($sql);
    $user = $resultado->fetch_assoc();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['usuario_id'] = $user['id_usuario'];
        $_SESSION['usuario_nombre'] = $user['nombre'];
        header("Location: dashboard.php");
        exit();
    } else {
        header("Location: index.php?error=1");
    }
}
?>
