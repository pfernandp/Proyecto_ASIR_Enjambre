<?php
session_start();
require_once("../config/db.php");

$mensaje_feedback = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = trim($_POST['usuario']);
    $grupo  = trim($_POST['grupo']);
    $pass   = $_POST['password'];

    if (!empty($nombre) && !empty($pass)) {
        try {
            // 1. SEGURIDAD: Cifrado de contraseña mediante Bcrypt
            $password_hash = password_hash($pass, PASSWORD_BCRYPT);

            // 2. Sentencia preparada (PDO) para evitar Inyección SQL
            $sql  = "INSERT INTO USUARIO (nombre, grupo, password_hash, rol) VALUES (?, ?, ?, 'Alumno')";
            $stmt = $pdo->prepare($sql);

            // 3. Ejecución con gestión de integridad (UNIQUE constraint)
            $stmt->execute([$nombre, $grupo, $password_hash]);

            // Registro automático de sesión tras el éxito
            $_SESSION['usuario_id']     = $pdo->lastInsertId();
            $_SESSION['usuario_nombre'] = $nombre;
            $_SESSION['rol']            = 'Alumno';

            header("Location: dashboard.php?msg=bienvenida_operador");
            exit();

        } catch (PDOException $e) {
            // Error 23000: Entrada duplicada (nombre de usuario ya existe)
            if ($e->getCode() == 23000) {
                $mensaje_feedback = "ERROR: Identificador de operador ya registrado en el Enjambre.";
            } else {
                $mensaje_feedback = "ERROR CRÍTICO: Fallo en el Nivel Interno: " . $e->getMessage();
            }
        }
    } else {
        $mensaje_feedback = "ALERTA: Campos obligatorios incompletos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Protocolo Enjambre</title>
    <audio id="bg-music" src="audio1.mp3" autoplay loop></audio>

    <script>
        // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
        document.addEventListener('click', function() {
            var audio = document.getElementById('bg-music');
            if (audio.paused) {
                audio.play();
            }
        }, { once: true });
    </script>

    <style>
        /* ESTÉTICA RETRO-TERMINAL */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000;
            color: #0f0;
            font-family: 'Courier New', monospace;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .terminal-box {
            border: 2px solid #0f0;
            padding: 30px;
            box-shadow: 0 0 15px #0f0;
            background: rgba(0, 10, 0, 0.9);
            width: 400px;
        }

        h2 {
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        label {
            display: block;
            font-size: 0.8em;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        .input-terminal {
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            font-family: 'Courier New', monospace;
        }

        .btn-terminal {
            background: #0f0;
            color: #000;
            border: none;
            padding: 10px;
            width: 100%;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
            font-family: 'Courier New', monospace;
            display: block;
            text-align: center;
            text-decoration: none;
        }

        .btn-terminal:hover {
            background: #000;
            color: #0f0;
            border: 1px solid #0f0;
        }

        .error {
            color: #f00;
            font-size: 0.8em;
            margin-bottom: 10px;
            border: 1px solid #f00;
            padding: 5px;
        }
    </style>
</head>
<body>

    <div class="terminal-box">

        <h2>ALTA DE NUEVO OPERADOR</h2>
        <p style="font-size: 0.8em; margin-bottom: 20px;">> Identifíquese para acceder al Centro de Contención.</p>

        <?php if ($mensaje_feedback): ?>
            <!-- htmlspecialchars evita XSS en mensajes de error -->
            <div class="error"><?php echo htmlspecialchars($mensaje_feedback); ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>> Usuario:</label>
            <input type="text" name="usuario" class="input-terminal" required autocomplete="off">

            <label>> Grupo/Sector:</label>
            <input type="text" name="grupo" placeholder="Ej: 4º ESO A" class="input-terminal">

            <label>> Código de acceso:</label>
            <input type="password" name="password" class="input-terminal" required>

            <input type="submit" value="INICIAR PROTOCOLO" class="btn-terminal">
        </form>

        <p style="margin-top: 15px; font-size: 0.7em;">
            <a href="index.php" class="btn-terminal">Ya tengo credenciales</a>
        </p>

    </div>

</body>
</html>