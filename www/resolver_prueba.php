<?php
session_start();
require_once("../config/db.php");


$id_p = $_GET['id_p'];
$resp = $_POST['respuesta'];
$id_u = $_SESSION['usuario_id'];
$valida = false;

if ($id_p == 1 && strlen($resp) >= 8) {
    $valida = true;
}

if ($valida) {

    $sql = "INSERT INTO ESTADO_USUARIO_PRUEBA (id_usuario, id_prueba, completada, tiempo_fin) 
            VALUES ($id_u, $id_p, 1, NOW()) 
            ON DUPLICATE KEY UPDATE completada = 1, tiempo_fin = NOW()";
    $conexion->query($sql);

    header("Location: dashboard.php?msg=success");
    exit();
} else {

    $mensaje = "Intento fallido de neutralización de IA CLAVE";
    $alerta = 'Medio';

    $sql_log = "INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES ($id_u, '$mensaje', '$alerta')";
    $conexion->query($sql_log); 

    header("Location: ia_detalle.php?id=1&error=1");
    exit();
}
?>
