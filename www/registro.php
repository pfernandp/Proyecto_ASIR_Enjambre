<?php
require_once("../config/db.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $grupo = $_POST["grupo"];
    $hash = password_hash($_POST["password"], PASSWORD_BCRYPT);
    
    $sql = "INSERT INTO USUARIO (nombre, grupo, password_hash) VALUES ('$nombre', '$grupo', '$hash')";
    if ($conexion->query($sql)) {
        header("Location: index.php?reg=ok");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registro - Enjambre</title>
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
    <style type="text/css">

        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background-color: #000;
            font-family: 'Courier New', Courier, monospace;
            color: #0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }

        /* PANEL PRINCIPAL */
        .panel {
            position: relative;
            z-index: 10;
            background: rgba(0, 15, 0, 0.95);
            border: 2px solid #0f0;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.2);
            width: 420px;
            text-align: center;
        }

        img {
            width: 150px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 5px #0f0);
        }

        h1 {
            font-size: 1.2em;
            text-transform: uppercase;
            letter-spacing: 4px;
            border-bottom: 1px solid #0f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        /* FORMULARIO */
        fieldset {
            border: 1px solid #0f0;
            padding: 20px;
            margin-bottom: 20px;
            text-align: left;
        }

        fieldset p {
            margin-bottom: 15px;
            font-size: 0.75em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-terminal {
            display: block;
            width: 100%;
            padding: 12px;
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            outline: none;
            margin-top: 6px;
        }

        .input-terminal:focus { box-shadow: 0 0 8px rgba(0, 255, 0, 0.4); }

        .btn-terminal {
            width: 100%;
            padding: 15px;
            background: #0f0;
            border: none;
            color: #000;
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 1em;
            text-transform: uppercase;
            cursor: pointer;
            transition: 0.4s;
            margin-top: 5px;
        }

        .btn-terminal:hover { background: #fff; box-shadow: 0 0 20px #fff; }

        /* LINK DE RETORNO */
        a {
            display: inline-block;
            margin-top: 20px;
            font-size: 0.7em;
            color: #0f0;
            text-decoration: none;
            opacity: 0.8;
        }

        a:hover { text-decoration: underline; opacity: 1; }

    </style>
</head>
<body>

    <div style="text-align:center;">
        <img src="logo.png" style="width:150px;">
        <h1>ALTA DE OPERADOR</h1>
        <form method="POST">
            <fieldset>
                <p>NOMBRE: <input type="text" name="nombre" required class="input-terminal"></p>
                <p>GRUPO: <input type="text" name="grupo" class="input-terminal"></p>
                <p>CLAVE: <input type="password" name="password" required class="input-terminal"></p>
                <input type="submit" value="DAR DE ALTA" class="btn-terminal">
            </fieldset>
        </form>
        <p><a href="index.php" class="btn-manual">Volver</a></p>
    </div>
</body>
</html>
