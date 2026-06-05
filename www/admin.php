<?php
session_start();
// Verificación de privilegios (ACL)
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'Administrador') {
    require_once("../config/db.php");
    $u_id = $_SESSION['usuario_id'] ?? 1;
    $msg  = "ACCESO DENEGADO: Intento de entrada anónima en Panel Admin desde IP: " . $_SERVER['REMOTE_ADDR'];
    
    $stmt_log = $pdo->prepare("INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES (?, ?, 'Alto')");
    $stmt_log->execute([$u_id, $msg]);
    
    header("Location: index.php");
    exit();
}

require_once("../config/db.php");

// --- Lógica de gestión (CRUD) ---
if (isset($_POST['reset_usuario'])) {
    $id_res = intval($_POST['id_u']);
    $pdo->prepare("DELETE FROM ESTADO_USUARIO_PRUEBA WHERE id_usuario = ?")->execute([$id_res]);
    $pdo->prepare("INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES (?, 'Progreso reseteado por Admin', 'Bajo')")->execute([$id_res]);
}

if (isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $stmt = $pdo->prepare('DELETE FROM USUARIO WHERE id_usuario = ? AND rol = ?');
    $stmt->execute([intval($_POST['id_usuario']), 'Alumno']);
}

if (isset($_GET['toggle_ia'])) {
    $id_ia = intval($_GET['toggle_ia']);
    $pdo->prepare("UPDATE PRUEBA SET activa = NOT activa WHERE id_ia = ?")->execute([$id_ia]);
}

// --- Recopilación de datos para vistas ---
$usuarios = $pdo->query("SELECT id_usuario, nombre, grupo, rol, fecha_creacion FROM USUARIO")->fetchAll();
$ias      = $pdo->query("SELECT IA.id_ia, IA.nombre, P.activa FROM IA JOIN PRUEBA P ON IA.id_ia = P.id_ia")->fetchAll();
$logs     = $pdo->query("SELECT L.*, U.nombre FROM LOG_IA L JOIN USUARIO U ON L.id_usuario = U.id_usuario ORDER BY L.fecha DESC LIMIT 10")->fetchAll();
$stats    = $pdo->query("SELECT COUNT(*) as total FROM USUARIO WHERE rol = 'Alumno'")->fetch();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administrador</title>
    <audio id="bg-music" src="audio1.mp3" autoplay loop></audio>

    <script>
        document.addEventListener('click', function() {
            var audio = document.getElementById('bg-music');
            if (audio.paused) {
                audio.play();
            }
        }, { once: true });
    </script>
    <style>
        
        body { background-color: #000; color: #0f0; font-family: 'Courier New', Courier, monospace; padding: 20px; }
        .admin-wrapper { max-width: 1200px; margin: 0 auto; border: 1px solid #0f0; padding: 20px; box-shadow: 0 0 15px #0f0; }
        h1, h2 { border-bottom: 2px solid #0f0; padding-bottom: 10px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 0.85em; }
        th, td { border: 1px solid #040; padding: 10px; text-align: left; }
        th { background: #020; color: #fff; }
        .btn { background: #0f0; color: #000; padding: 5px 10px; text-decoration: none; border: none; cursor: pointer; font-family: inherit; font-weight: bold; }
        .btn:hover { background: #000; color: #0f0; border: 1px solid #0f0; }
        .btn-danger { background: #f00; color: #fff; }
        .log-alto { color: #f00; font-weight: bold; }
        .scanline { position: fixed; top: 0; left: 0; width: 100%; height: 2px; background: rgba(0, 255, 0, 0.1); z-index: 100; animation: move 8s linear infinite; pointer-events: none; }
        @keyframes move { from { top: 0; } to { top: 100%; } }
    </style>
</head>
<body>
    <div class="scanline"></div>
    <div class="admin-wrapper">
        <header>
            <h1>> PANEL DE ADMINISTRACIÓN CENTRAL <</h1>
            <p>> OPERADOR MAESTRO: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>
            <p>> TOTAL DE SUJETOS DE PRUEBA: <?php echo $stats['total']; ?></p>
        </header>

        <section>
            <h2>1. GESTIÓN DE OPERADORES (USUARIOS)</h2>
            <table>
                <tr><th>ID</th><th>NOMBRE</th><th>GRUPO</th><th>REGISTRO</th><th>ACCIONES</th></tr>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?php echo $u['id_usuario']; ?></td>
                    <td><?php echo htmlspecialchars($u['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($u['grupo']); ?></td>
                    <td><?php echo $u['fecha_creacion']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id_u" value="<?php echo $u['id_usuario']; ?>">
                            <input type="submit" name="reset_usuario" value="RESET PROGRESO" class="btn">
                        </form>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('¿Confirmas la eliminación del usuario <?php echo htmlspecialchars($u['nombre'], ENT_QUOTES); ?>? Esta acción no se puede deshacer.');">
                            <input type="hidden" name="accion" value="eliminar">
                            <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                            <input type="submit" value="ELIMINAR USUARIO" class="btn btn-danger">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <section>
            <h2>2. CONFIGURACIÓN DEL ENJAMBRE (IAs)</h2>
            <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                <?php foreach ($ias as $ia): ?>
                <div style="border: 1px solid #0f0; padding: 10px; width: 200px;">
                    <strong><?php echo $ia['nombre']; ?></strong><br>
                    Estado: <?php echo $ia['activa'] ? '<span style="color:#0f0;">ACTIVA</span>' : '<span style="color:#f00;">INACTIVA</span>'; ?><br>
                    <a href="?toggle_ia=<?php echo $ia['id_ia']; ?>" class="btn" style="display:block; margin-top:10px; text-align:center;">CAMBIAR ESTADO</a>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section>
            <h2>3. AUDITORÍA DE SEGURIDAD (LOGS)</h2>
            <table>
                <tr><th>FECHA</th><th>USUARIO</th><th>MENSAJE</th><th>NIVEL</th></tr>
                <?php foreach ($logs as $l): ?>
                <tr class="log-<?php echo strtolower($l['nivel_alerta']); ?>">
                    <td><?php echo $l['fecha']; ?></td>
                    <td><?php echo htmlspecialchars($l['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($l['mensaje']); ?></td>
                    <td><?php echo $l['nivel_alerta']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </section>

        <footer style="margin-top: 30px; text-align: right;">
            <a href="logout.php" class="btn" style="background:#400; color:#f00;">CERRAR TERMINAL</a>
        </footer>
    </div>
</body>
</html>