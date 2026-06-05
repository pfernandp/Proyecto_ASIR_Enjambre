<?php
session_start();
// Control de seguridad: solo si hay una sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
// Recuperamos el nombre para personalizar el mensaje final
$nombre_operador = $_SESSION['usuario_nombre'] ?? 'Operador';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>SISTEMA RESTAURADO - El Despertar del Enjambre</title>
    <style>
 * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        background-color: #000;
        color: #0f0;
        font-family: 'Courier New', monospace;
        overflow-y: auto; 
        position: relative;
        min-height: 100vh;
    }

    .scanline {
        width: 100%;
        height: 100px;
        background: linear-gradient(0deg, rgba(0,255,0,0) 0%, rgba(0,255,0,0.2) 50%, rgba(0,255,0,0) 100%);
        position: fixed; 
        top: 0;
        left: 0;
        z-index: 100;
        animation: moveScanline 4s linear infinite;
        pointer-events: none;
    }

    .victory-wrapper {
        position: relative;
        z-index: 10;
        max-width: 900px;
        margin: 50px auto;
        text-align: center;
        border: 2px solid #0f0;
        padding: 40px;
        background: rgba(0, 20, 0, 0.9);
        box-shadow: 0 0 30px #0f0;
    }

    .matrix-text {
        color: #0f0;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 20px;
    }

    @keyframes moveScanline {
        0% { top: -100px; }
        100% { top: 100%; }
    }
</style>
    </style>
</head>
<body style="background-color: #000; overflow: auto; position: relative;">
    <div class="scanline"></div>

    <div class="victory-wrapper">
        <h1 class="matrix-text">> MISIÓN CUMPLIDA <</h1>
        
        <div style="margin: 30px 0;">
            <img src="../Imagenes/final.png" alt="Sistema Restaurado" style="width: 100%; border: 1px solid #0f0;">
        </div>

        <audio id="victory-audio" src="audio1.mp3" autoplay></audio>

        <div style="color: #8f8; line-height: 1.8; font-size: 1.1em; text-align: justify; margin-bottom: 30px;">
            <p>> CONEXIÓN ESTABLECIDA...</p>
            <p>> NÚCLEO LÓGICO: <strong>LIMPIO</strong></p>
            <p>> ESTADO DEL ENJAMBRE: <strong>CONTENIDO</strong></p>
            <br>
            <p>Enhorabuena, <strong><?php echo htmlspecialchars($nombre_operador); ?></strong>. Gracias a tu intervención técnica en los seis sectores críticos, el sistema ha sido liberado de la influencia del Enjambre. Has demostrado poseer las competencias necesarias en gestión de credenciales, hardening de privacidad y detección de amenazas avanzadas.</p>
            <br>
            <p>> El Protocolo de Restauración ha finalizado. El sistema es seguro de nuevo... por ahora.</p>
        </div>

        <div style="border-top: 1px solid #444; padding-top: 20px;">
            <p style="font-size: 0.8em; opacity: 0.6; margin-bottom: 20px;">Salir del sistema.</p>
            <a href="logout.php" class="btn-terminal" style="padding: 15px 40px; text-decoration: none; background: #0f0; color: #000; font-weight: bold; display: inline-block;">
                CERRAR TERMINAL Y FINALIZAR
            </a>
        </div>
    </div>
</body>
</html>
