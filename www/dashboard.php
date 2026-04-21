<?php
session_start();
if (!isset($_SESSION['usuario_id'])) { header("Location: index.php"); exit(); }
require_once("../config/db.php");
$id_u = $_SESSION['usuario_id'];

// Consulta JOIN para estados de IA
$sql_ias = "SELECT ia.id_ia, ia.nombre, ia.nivel_peligro, e.completada 
            FROM IA ia 
            LEFT JOIN PRUEBA p ON ia.id_ia = p.id_ia
            LEFT JOIN ESTADO_USUARIO_PRUEBA e ON p.id_prueba = e.id_prueba AND e.id_usuario = $id_u";
$ias = $conexion->query($sql_ias);

$sql_avg = "SELECT AVG(nivel_peligro) as media FROM IA";
$media = $conexion->query($sql_avg)->fetch_assoc()['media'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Enjambre</title>
    <style type="text/css">

        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #000;
            font-family: 'Courier New', Courier, monospace;
            color: #0f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* PRINCIPAL */
        .main-wrapper {
            position: relative;
            z-index: 10;
            max-width: 1100px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        /* CABECERA */
        h1 {
            font-size: 1.5em;
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

        p {
            font-size: 0.8em;
            opacity: 0.7;
            margin-bottom: 30px;
        }

        @keyframes blink {
            0%   { opacity: 1; }
            50%  { opacity: 0.3; }
            100% { opacity: 1; }
        }

        /* GRID DE TARJETAS */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .card {
            background: rgba(0, 15, 0, 0.9);
            border: 2px solid #0f0;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.1);
            transition: 0.3s;
        }

        .card:hover { box-shadow: 0 0 25px rgba(0, 255, 0, 0.3); }

        .neutralizada { border-left: 10px solid #0f0; }

        .activa { border-left: 10px solid #f00; }

        .card h3 {
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-bottom: 10px;
            border-bottom: 1px solid #0f0;
            padding-bottom: 8px;
        }

        .card p {
            font-size: 0.8em;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        /* BADGE NEUTRALIZADA */
        .badge-code {
            display: inline-block;
            background: #0f0;
            color: #000;
            padding: 5px 10px;
            font-weight: bold;
            font-size: 0.75em;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* ENLACES DE ACCIÓN */
        a {
            display: inline-block;
            padding: 8px 16px;
            border: 1px solid #0f0;
            color: #0f0;
            text-decoration: none;
            font-size: 0.75em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }

        a:hover { background: #0f0; color: #000; }

        /* BOTÓN LOGOUT */
        .btn-logout {
            border-color: #f00;
            color: #f00;
        }

        .btn-logout:hover { background: #f00; color: #000; box-shadow: 0 0 15px #f00; }

    </style>
</head>
<body>

    <div class="main-wrapper">
        <h1>AMENAZA GLOBAL: <span><?php echo round($media); ?>%</span></h1>
        <p>OPERADOR: <?php echo $_SESSION['usuario_nombre']; ?></p>
        <div class="grid">
            <?php while($ia = $ias->fetch_assoc()): ?>
                <div class="card <?php echo $ia['completada'] ? 'neutralizada' : 'activa'; ?>">
                    <h3><?php echo $ia['nombre']; ?></h3>
                    <p>PELIGRO: <?php echo $ia['nivel_peligro']; ?>%</p>
                    <?php if(!$ia['completada']): ?>
                        <a href="ia_detalle.php?id=<?php echo $ia['id_ia']; ?>" style="color:#0ff;">INTERVENIR</a>
                    <?php else: ?>
                        <span class="badge-code">NEUTRALIZADA</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <br><a href="logout.php" class="btn-logout">Desconexión Segura</a>
    </div>

</body>
</html>
