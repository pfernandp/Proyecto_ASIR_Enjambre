<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>El Enjambre - Centro de Contención</title>
    <style>
        
        *{ 
            margin: 0px; 
            padding: 0px; 
            box-sizing: border-box; 
        }
        
        body{ 
            background-color: #000;
            font-family: 'Courier New', Courier, monospace; 
            color: #0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        /* PANEL DE ACCESO CENTRAL */
        .login-panel {
            background: rgba(0, 15, 0, 0.95);
            border: 2px solid #0f0;
            padding: 40px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.2);
            width: 420px;
            text-align: center;
        }

        .logo-enjambre {
            width: 180px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 0 5px #0f0);
        }

        h1 { 
            margin-bottom: 25px; 
            font-size: 1.2em; 
            text-transform: uppercase; 
            letter-spacing: 4px; 
            border-bottom: 1px solid #0f0; 
            padding-bottom: 15px; 
        }

        /* Estilo de formulario */
        .form-group { 
            margin-bottom: 20px; 
            text-align: left; 
        }
        
        label { 
            display: block; 
            margin-bottom: 10px; 
            font-size: 0.75em; 
            font-weight: bold; 
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px;
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            outline: none;
            font-family: 'Courier New', Courier, monospace;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background: #0f0;
            border: none;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: 0.4s;
            text-transform: uppercase;
        }

        input[type="submit"]:hover { 
            background: #fff; 
            box-shadow: 0 0 20px #fff; 
        }

        
        .links-container { 
            margin-top: 25px; 
        }
        
        .toggle-link { 
            font-size: 0.7em; 
            color: #0f0; 
            text-decoration: none; 
            display: block; 
            margin-bottom: 15px;
            opacity: 0.8;
        }
        .toggle-link:hover { 
            text-decoration: underline; 
            opacity: 1; 
        }

        .btn-manual {
            display: inline-block;
            padding: 10px 20px;
            border: 1px solid #0f0;
            color: #0f0;
            text-decoration: none;
            font-size: 0.8em;
            letter-spacing: 1px;
            transition: 0.3s;
        }
        .btn-manual:hover { 
            background: #0f0; 
            color: #000; 
        }
    </style>
</head>
<body>

    <!-- Interfaz de Acceso Única -->
    <div class="login-panel">
        <!-- Imagen corporativa (Referenciada mediante ruta relativa) -->
        <img src="logo.png" alt="Logotipo El Enjambre" class="logo-enjambre">
        
        <h1>Protocolo de Acceso</h1>
        
        <!-- Formulario configurado con método POST por seguridad de credenciales -->
        <form action="login.php" method="post">
            <div class="form-group">
                <label>IDENTIFICADOR DE OPERADOR</label>
                <input type="text" name="nombre" placeholder="ID Usuario" required>
            </div>
            <div class="form-group">
                <label>CLAVE DE DESBLOQUEO</label>
                <input type="password" name="password" placeholder="Contraseña" required>
            </div>
            <input type="submit" value="INICIAR CONTENCIÓN">
        </form>

        <div class="links-container">
            <a href="registro.php" class="toggle-link">Solicitar nuevas credenciales de operador</a>
            <hr style="border: 0.5px solid #0f0; margin-bottom: 20px; opacity: 0.2;">
            <a href="manual.php" class="btn-manual">LEER BRIEFING DE LA MISIÓN</a>
        </div>
    </div>

</body>
</html>
