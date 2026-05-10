<?php
session_start();
require_once("../config/db.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_input = $_POST['nombre'];
    $pass_input = $_POST['password'];

    try {
        // Sentencia preparada para evitar Inyección SQL
        $stmt = $pdo->prepare("SELECT id_usuario, nombre, password_hash, rol FROM USUARIO WHERE nombre = ?");
        $stmt->execute([$user_input]);
        $usuario = $stmt->fetch();

        if ($usuario && password_verify($pass_input, $usuario['password_hash'])) {
            // Credenciales correctas: iniciar sesión
            $_SESSION['usuario_id']     = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['rol']            = $usuario['rol'];

            header("Location: dashboard.php");
            exit();
        } else {
            $error = "ALERTA: Credenciales incorrectas. Acceso denegado.";
        }

    } catch (PDOException $e) {
        die("Error en el Nivel Interno: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado - Protocolo Enjambre</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

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
            border: 2px solid #f00;
            padding: 30px;
            box-shadow: 0 0 15px #f00;
            background: rgba(10, 0, 0, 0.9);
            width: 420px;
            text-align: center;
        }

        h2 {
            color: #f00;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .error {
            color: #f00;
            font-size: 0.85em;
            border: 1px solid #f00;
            padding: 10px;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #0f0;
            color: #0f0;
            text-decoration: none;
            font-size: 0.85em;
            text-transform: uppercase;
            transition: 0.3s;
        }

        a:hover {
            background: #0f0;
            color: #000;
        }
    </style>
</head>
<body>

    <div class="terminal-box">
        <h2>> ACCESO DENEGADO <</h2>
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <a href="index.php">Volver al Panel de Acceso</a>
    </div>

</body>
</html>