<?php
session_start();
// Control de Acceso: Solo operadores autenticados
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}
require_once("../config/db.php");

$id_u = $_SESSION['usuario_id'];
$error_final = isset($_GET['error']);

try {
    // CONSULTA JOIN: Recuperamos los fragmentos de las pruebas que el usuario ha completado
    $stmt = $pdo->prepare("
        SELECT p.nombre, p.fragmento_codigo 
        FROM PRUEBA p
        JOIN ESTADO_USUARIO_PRUEBA e ON p.id_prueba = e.id_prueba
        WHERE e.id_usuario = ? AND e.completada = 1
        ORDER BY p.orden ASC
    ");
    $stmt->execute([$id_u]);
    $fragmentos = $stmt->fetchAll();

    // Verificamos si realmente tiene los 6 fragmentos para estar aquí
    if (count($fragmentos) < 6) {
        header("Location: dashboard.php?msg=faltan_fragmentos");
        exit();
    }
} catch (PDOException $e) {
    die("Fallo en la comunicación con el Nivel Interno: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Protocolo de Restauración Final</title>
    <style>
        .fragmento-box {
            display: inline-block;
            border: 1px dashed #0f0;
            padding: 10px;
            margin: 5px;
            font-weight: bold;
            background: rgba(0, 50, 0, 0.3);
        }
        .final-input {
            width: 100%;
            background: #000;
            border: 2px solid #0f0;
            color: #0f0;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 1.2em;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body style="background-color: #000; color: #0f0; font-family: 'Courier New', monospace; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">

    <div style="max-width: 800px; width: 100%; border: 1px solid #0f0; padding: 40px; box-shadow: 0 0 20px #080;">
        <header style="text-align: center; margin-bottom: 30px;">
            <h1>> NODO CENTRAL: RESTAURACIÓN <</h1>
            <p style="opacity: 0.7;">ESTADO: TODOS LOS SECTORES NEUTRALIZADOS</p>
        </header>

        <!-- Audio de victoria -->
        <audio id="bg-music" src="audio1.mp3" autoplay loop></audio>

        <div style="margin-bottom: 30px; font-style: italic; color: #8f8; line-height: 1.6;">
            <p>"El Enjambre ha retrocedido a las sombras. Las seis conciencias han sido aisladas en contenedores de cuarentena. Ahora, Operador, introduce la secuencia maestra de restauración para devolver el control a la red humana."</p>
        </div>

        <?php if ($error_final): ?>
            <div style="color: #f00; border: 1px solid #f00; padding: 10px; margin-bottom: 20px; text-align: center;">
                > ERROR: SECUENCIA DE RESTAURACIÓN INVÁLIDA. INTÉNTELO DE NUEVO.
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-bottom: 30px;">
            <p style="margin-bottom: 15px;">FRAGMENTOS DE CÓDIGO RECUPERADOS:</p>
            <div style="display: flex; flex-wrap: wrap; justify-content: center;">
                <?php foreach ($fragmentos as $f): ?>
                    <div class="fragmento-box">
                        <small style="display:block; font-size: 0.6em; opacity: 0.6;"><?php echo $f['nombre']; ?></small>
                        <?php echo $f['fragmento_codigo']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Formulario final -->
        <form action="verificar_final.php" method="POST">
            <label style="font-size: 0.8em;">> INTRODUCE LA SECUENCIA COMPLETA (Sin espacios):</label>
            <input type="text" name="codigo_maestro" class="final-input" placeholder="Ej: FRAG-C1-PASSFRAG-V2-PRIV..." required autocomplete="off">
            
            <input type="submit" value="EJECUTAR CIERRE DE EMERGENCIA" class="btn-terminal" style="margin-top: 20px; width: 100%; background: #0f0; color: #000; padding: 15px; cursor: pointer; font-weight: bold; border: none;">
        </form>

        <div style="text-align: center; margin-top: 30px;">
            <a href="dashboard.php" style="color: #0f0; font-size: 0.8em; text-decoration: none;">[ VOLVER AL CENTRO DE CONTENCIÓN ]</a>
        </div>
    </div>

</body>
</html>
