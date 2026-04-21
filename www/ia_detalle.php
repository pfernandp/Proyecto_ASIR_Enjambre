<?php
session_start();
require_once("../config/db.php");
$id_ia = $_GET['id'];
$sql = "SELECT ia.nombre, p.id_prueba, p.nombre as p_nom, p.descripcion FROM IA ia JOIN PRUEBA p ON ia.id_ia = p.id_ia WHERE ia.id_ia = $id_ia AND p.activa = 1";
$res = $conexion->query($sql);
$data = $res->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Intervención - Sector <?php echo $id_ia; ?></title>
    <style type="text/css">
 
        /* RESET */
        * { margin: 0; padding: 0; box-sizing: border-box; }
 
        /* FONDO */
        body {
            background-color: #000;
            font-family: 'Courier New', Courier, monospace;
            color: #0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }
 
        /* PANEL PRINCIPAL */
        .panel-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 640px;
            padding: 20px;
        }
 
        h1 {
            font-size: 1.5em;
            text-transform: uppercase;
            letter-spacing: 4px;
            border-bottom: 2px solid #0f0;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
 
        /* TARJETA DE RETO */
        .card {
            background: rgba(0, 15, 0, 0.95);
            border: 2px solid #0f0;
            padding: 30px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.2);
            margin-bottom: 20px;
        }
 
        .card h3 {
            font-size: 1em;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #fff;
            text-shadow: 0 0 5px #0f0;
            margin-bottom: 15px;
            border-bottom: 1px solid #0f0;
            padding-bottom: 10px;
        }
 
        .card p {
            font-size: 0.85em;
            line-height: 1.6;
            margin-bottom: 20px;
            background: #010;
            border-left: 3px solid #0f0;
            padding: 15px;
            font-style: italic;
            color: #8f8;
        }
 
        /* FORMULARIO */
        .input-terminal {
            display: block;
            width: 100%;
            padding: 12px;
            background: #000;
            border: 1px solid #0f0;
            color: #0f0;
            font-family: 'Courier New', Courier, monospace;
            font-size: 1em;
            outline: none;
            margin-top: 10px;
        }
 
        .input-terminal:focus { box-shadow: 0 0 8px rgba(0, 255, 0, 0.4); }
 
        .btn-terminal {
            display: block;
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
            margin-top: 15px;
        }
 
        .btn-terminal:hover { background: #fff; box-shadow: 0 0 20px #fff; }
 
        /* ENLACE ABORTAR */
        a {
            display: inline-block;
            padding: 8px 16px;
            border: 1px solid #f00;
            color: #f00;
            text-decoration: none;
            font-size: 0.75em;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.3s;
        }
 
        a:hover { background: #f00; color: #000; box-shadow: 0 0 15px #f00; }
 
    </style>
</head>
<body>
 
    <div class="panel-wrapper">
        <h1>SECTOR: <?php echo $data['nombre']; ?></h1>
        <div class="card">
            <h3>RETO: <?php echo $data['p_nom']; ?></h3>
            <p><?php echo $data['descripcion']; ?></p>
            <form action="resolver_prueba.php?id_p=<?php echo $data['id_prueba']; ?>" method="POST">
                <?php if($id_ia == 1): ?>
                    <input type="password" name="respuesta" placeholder="Clave robusta..." class="input-terminal">
                <?php endif; ?>
                <input type="submit" value="EJECUTAR" class="btn-terminal">
            </form>
        </div>
        <br><a href="dashboard.php" style="color:#0ff;">Abortar</a>
    </div>
 
</body>
</html>
