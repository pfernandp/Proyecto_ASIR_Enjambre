<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("../config/db.php");

$id_u = $_SESSION['usuario_id'];

try {
    // Consulta JOIN para obtener estados de IA del operador actual
    $sql_ias = "SELECT ia.id_ia, ia.nombre, ia.nivel_peligro, e.completada, p.fragmento_codigo
                FROM IA ia
                LEFT JOIN PRUEBA p ON ia.id_ia = p.id_ia
                LEFT JOIN ESTADO_USUARIO_PRUEBA e ON p.id_prueba = e.id_prueba AND e.id_usuario = ?";
    $stmt_ias = $pdo->prepare($sql_ias);
    $stmt_ias->execute([$id_u]);
    $ias = $stmt_ias->fetchAll();

    // Cálculo de media del peligro de las IAs
    $sql_avg = "SELECT AVG(nivel_peligro) as media FROM IA";
    $res_avg  = $pdo->query($sql_avg)->fetch();
    $media    = $res_avg['media'] ?? 0;

    // --- AUDITORÍA DE PROGRESO GLOBAL PARA EL CIERRE ---
    // Consultamos el número de sectores neutralizados por el operador actual
    $stmt_progreso = $pdo->prepare("SELECT COUNT(*) FROM ESTADO_USUARIO_PRUEBA WHERE id_usuario = ? AND completada = 1");
    $stmt_progreso->execute([$_SESSION['usuario_id']]);
    $sectores_listos = $stmt_progreso->fetchColumn();

} catch (PDOException $e) {
    // Gestión administrativa de excepciones del SGBD
    die("Error crítico en el Nivel Interno: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Centro de Contención - Protocolo Enjambre</title>
    <audio id="bg-music" src="audio2.mp3" autoplay loop></audio>

    <script>
        // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
        document.addEventListener('click', function() {
            var audio = document.getElementById('bg-music');
            if (audio.paused) {
                audio.play();
            }
        }, { once: true });
    </script>

    <!-- LÓGICA DE CIERRE: Solo visible si se han recuperado los 6 fragmentos -->
    <?php if ($sectores_listos == 6): ?>
        <div class="card" style="border: 2px solid #0f0; background: rgba(0, 40, 0, 0.8); margin-bottom: 30px; text-align: center; box-shadow: 0 0 20px #0f0;">
            <h2 style="color: #0f0; letter-spacing: 2px;">> TERMINAL CENTRAL DE RESTAURACIÓN DISPONIBLE <</h2>
            <p style="margin: 15px 0; font-size: 0.9em; color: #8f8;">
                El Enjambre ha sido contenido en todos los sectores. Los 6 fragmentos de código están listos para la secuencia maestra de restauración.
            </p>
            
            <!-- Enlace crítico hacia el Protocolo Final -->
            <a href="protocolo_final.php" class="btn-action" style="background: #0f0; color: #000; padding: 20px; font-size: 1.2em; display: inline-block; width: auto; min-width: 300px;">
                EJECUTAR PROTOCOLO DE RESTAURACIÓN
            </a>
        </div>
    <?php endif; ?>

    <style>
        /* ESTÉTICA RETRO-TERMINAL CRT */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000;
            font-family: 'Courier New', Courier, monospace;
            color: #0f0;
            min-height: 100vh;
            overflow-x: hidden;
            text-shadow: 0 0 5px #0f0;
        }

        /* Efecto barrido de monitor antiguo */
        body::before {
            content: " ";
            display: block;
            position: absolute;
            top: 0; left: 0; bottom: 0; right: 0;
            background: linear-gradient(rgba(18, 16, 16, 0) 50%, rgba(0, 0, 0, 0.2) 50%),
                        linear-gradient(90deg, rgba(255, 0, 0, 0.03), rgba(0, 255, 0, 0.01), rgba(0, 0, 255, 0.03));
            z-index: 2;
            background-size: 100% 3px, 2px 100%;
            pointer-events: none;
        }

        .main-wrapper {
            position: relative;
            z-index: 10;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        h1 {
            font-size: 1.8em;
            text-transform: uppercase;
            letter-spacing: 4px;
            border-bottom: 2px solid #0f0;
            padding-bottom: 15px;
            margin-bottom: 10px;
        }

        h1 span {
            color: #f00;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%   { opacity: 1; }
            50%  { opacity: 0.3; }
            100% { opacity: 1; }
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-top: 30px;
        }

        .card {
            background: rgba(0, 20, 0, 0.8);
            border: 1px solid #0f0;
            padding: 20px;
            transition: 0.3s;
            position: relative;
        }

        .card:hover {
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.4);
            transform: scale(1.02);
        }

        .neutralizada {
            border-left: 10px solid #0f0;
            background: rgba(0, 40, 0, 0.4);
        }

        .activa {
            border-left: 10px solid #f00;
        }

        .badge-status {
            font-weight: bold;
            font-size: 0.7em;
            padding: 4px 8px;
            margin-top: 10px;
            display: inline-block;
        }

        .status-ok    { background: #0f0; color: #000; }
        .status-alert { background: #f00; color: #fff; }

        .btn-action {
            display: block;
            width: 100%;
            text-align: center;
            background: #0f0;
            color: #000;
            padding: 10px;
            margin-top: 15px;
            text-decoration: none;
            font-weight: bold;
        }

        .btn-action:hover {
            background: #000;
            color: #0f0;
            border: 1px solid #0f0;
        }

        .fragmento {
            color: #fff;
            font-size: 0.75em;
            border: 1px dashed #0f0;
            padding: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="main-wrapper">

        <header>
            <h1>AMENAZA GLOBAL: <span><?php echo round($media); ?>%</span></h1>
            <p>> OPERADOR AUTENTICADO: <?php echo htmlspecialchars($_SESSION['usuario_nombre'] ?? 'OPERADOR_DESCONOCIDO'); ?></p>
        </header>

        <div class="grid">
            <?php foreach ($ias as $ia): ?>
                <div class="card <?php echo $ia['completada'] ? 'neutralizada' : 'activa'; ?>">

                    <h3><?php echo htmlspecialchars($ia['nombre']); ?></h3>
                    <p style="font-size: 0.8em; margin: 10px 0;">PELIGRO: <?php echo $ia['nivel_peligro']; ?>%</p>

                    <?php if ($ia['completada']): ?>
                        <span class="badge-status status-ok">IA NEUTRALIZADA</span>
                        <div class="fragmento">
                            CÓDIGO RECUPERADO:<br>
                            <strong><?php echo htmlspecialchars($ia['fragmento_codigo']); ?></strong>
                        </div>
                    <?php else: ?>
                        <span class="badge-status status-alert">AMENAZA ACTIVA</span>
                        <a href="ia_detalle.php?id=<?php echo $ia['id_ia']; ?>" class="btn-action">INTERVENIR</a>
                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        </div>

        <img src="../Imagenes/Enjambre3.png" alt="Enjambre" style="width: 100%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
        <footer style="margin-top: 50px; text-align: right;">
            <a href="logout.php" style="color: #0f0; font-size: 0.8em;">DESCONEXIÓN SEGURA</a>
        </footer>

    </div>

</body>
</html>