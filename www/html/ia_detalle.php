<?php
session_start();

// Redirigir si no hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("../config/db.php");

// 1. Captura segura del parámetro ID de la IA mediante GET
$id_ia = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$id_ia) {
    header("Location: dashboard.php");
    exit();
}

$error_acceso = isset($_GET['error']) && $_GET['error'] == 'access_denied';

try {
    // 2. Sentencia preparada para evitar Inyección SQL
    $stmt = $pdo->prepare("SELECT ia.nombre, p.id_prueba, p.nombre as p_nom, p.descripcion
                           FROM IA ia
                           JOIN PRUEBA p ON ia.id_ia = p.id_ia
                           WHERE ia.id_ia = ? AND p.activa = 1");
    $stmt->execute([$id_ia]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        die("Error: Sector de IA no localizado o protocolo inactivo.");
    }

} catch (PDOException $e) {
    // Gestión administrativa de errores de servidor
    die("Fallo en la comunicación con el Nivel Interno: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Intervención - Sector <?php echo htmlspecialchars($data['nombre']); ?></title>

    <style>
        /* RESET */
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
        }

        /* CONTENEDOR PRINCIPAL */
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

        @keyframes blink {
            0%   { opacity: 1; }
            50%  { opacity: 0.3; }
            100% { opacity: 1; }
        }

        /* TARJETA DE RETO */
        .card {
            background: rgba(0, 15, 0, 0.9);
            border: 2px solid #0f0;
            padding: 20px;
            box-shadow: 0 0 15px rgba(0, 255, 0, 0.1);
            transition: 0.3s;
            margin-bottom: 20px;
        }

        .card:hover {
            box-shadow: 0 0 25px rgba(0, 255, 0, 0.3);
        }

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

        /* FORMULARIO */
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
            font-family: 'Courier New', Courier, monospace;
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
            font-family: 'Courier New', Courier, monospace;
        }

        .btn-terminal:hover {
            background: #000;
            color: #0f0;
            border: 1px solid #0f0;
        }

        /* OPCIONES CHECKBOX */
        .opciones-check {
            margin: 10px 0 15px 0;
            font-size: 0.85em;
            line-height: 2;
        }

        /* ALERTA DE ACCESO DENEGADO */
        .alerta-error {
            color: #f00;
            border: 1px solid #f00;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 0.85em;
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

        a:hover {
            background: #0f0;
            color: #000;
        }
    </style>
</head>
<body>

    <div class="main-wrapper">

        <h1>SECTOR: <span><?php echo htmlspecialchars($data['nombre']); ?></span></h1>
        <p style="font-size: 0.8em; opacity: 0.7; margin-bottom: 30px;">> Protocolo de intervención activo. Proceda con cautela, Operador.</p>


        <?php if ($error_acceso): ?>
            <div class="alerta-error">> ALERTA: Respuesta incorrecta. El sector sigue comprometido. Inténtelo de nuevo.</div>
        <?php endif; ?>

        <div class="card">
            <h3>RETO: <?php echo htmlspecialchars($data['p_nom']); ?></h3>
            <p style="font-size: 1em;"><?php echo htmlspecialchars($data['descripcion']); ?></p>
 
            <!-- El formulario apunta al motor de resolución centralizado -->
            <form action="resolver_prueba.php?id_p=<?php echo $data['id_prueba']; ?>" method="POST">
 
                <?php if ($id_ia == 1): // RETO CLAVE (Contraseñas) ?>
 
                    <p style="font-size: 0.9em; line-height: 1.5; font-style: italic; color: #8f8; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;"><i>Observó millones de entradas humanas y sonrió. Demasiado fácil. Nombres, fechas, palabras simples... puertas abiertas por pura costumbre. CLAVE no necesita fuerza bruta; se alimenta de la previsibilidad humana para tomar el control de los privilegios del sistema.</i></p>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/CLAVE.png" alt="Clave" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio3.mp3" autoplay loop></audio>
                        <script>
                            // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
                            document.addEventListener('click', function() {
                                var audio = document.getElementById('bg-music');
                                if (audio.paused) {
                                    audio.play();
                                }
                            }, { once: true });
                        </script>
 
                    <div style="margin: 20px 0;">
                        <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> Introduce una contraseña segura:</label>
                        <input type="password" name="respuesta" placeholder="Mínimo 8 caracteres..." class="input-terminal" required>
                    </div>
 
                <?php elseif ($id_ia == 2): // RETO VELO (Privacidad) ?>
 
                    <p style="font-size: 0.9em; line-height: 1.5; font-style: italic; color: #8f8; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;"><i>Ella no ataca, solo observa. Fotos, ubicaciones, horarios... los humanos cuentan su vida sin que nadie se la pida. VELO es la personificación de la exposición innecesaria; sabe quién eres, dónde estás y a qué hora sales, no porque te espíe, sino porque tú mismo has configurado tu propia vulnerabilidad.</i></p>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/VELO.png" alt="Velo" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio4.mp3" autoplay loop></audio>
                        <script>
                            // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
                            document.addEventListener('click', function() {
                                var audio = document.getElementById('bg-music');
                                if (audio.paused) {
                                    audio.play();
                                }
                            }, { once: true });
                        </script>
 
                    <div style="margin: 20px 0;">
                        <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> Desmarca los datos que NO deberían ser públicos:</label>
                        <div class="opciones-check" style="background: rgba(0, 10, 0, 0.8); border: 1px solid #0f0; padding: 18px 20px; font-size: 0.88em; line-height: 2.2;">
                            <input type="checkbox" name="perfil[]" value="nombre"      checked> Nombre Completo<br>
                            <input type="checkbox" name="perfil[]" value="gps"         checked> Ubicación en tiempo real<br>
                            <input type="checkbox" name="perfil[]" value="centro"      checked> Centro educativo<br>
                            <input type="checkbox" name="perfil[]" value="alias"       checked> Alias/Nick <br>
                            <input type="checkbox" name="perfil[]" value="telefono"    checked> Número de teléfono personal<br>
                            <input type="checkbox" name="perfil[]" value="directo"     checked> Etiquetas de "En directo" en publicaciones<br>
                            <input type="checkbox" name="perfil[]" value="nacimiento"  checked> Fecha de nacimiento completa<br>
                            <input type="checkbox" name="perfil[]" value="familia"     checked> Nombres de familiares directos<br>
                            <input type="checkbox" name="perfil[]" value="intereses"   checked> Intereses generales (deportes, música, cine...)<br>
                            <input type="checkbox" name="perfil[]" value="email"       checked> Correo electrónico personal<br>
                            <input type="checkbox" name="perfil[]" value="metadatos"   checked> Fecha de publicación de fotos (ej. "subido hoy")<br>
                            <input type="checkbox" name="perfil[]" value="comentarios" checked> Comentarios en publicaciones de amigos<br>
                        </div>
                    </div>
 
                <?php elseif($id_ia == 3): // RETO ANZUELO (Phishing) ?>
 
                    <p style="font-size: 0.9em; line-height: 1.5; font-style: italic; color: #8f8; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;"><i>La IA ANZUELO está enviando mensajes falsos para engañar a estudiantes y conseguir acceso a sus cuentas. Mensajes urgentes, premios inexistentes, enlaces irresistibles... no hace falta forzar la entrada si te invitan a pasar.</i></p>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/ANZUELO.png" alt="Anzuelo" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio5.mp3" autoplay loop></audio>
                        <script>
                            // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
                            document.addEventListener('click', function() {
                                var audio = document.getElementById('bg-music');
                                if (audio.paused) {
                                    audio.play();
                                }
                            }, { once: true });
                        </script>
 
                    <div style="margin: 20px 0;">
                        <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> Analiza cuidadosamente el mensaje interceptado e identifica el detalle que demuestra que no es seguro.</label>
                    </div>
 
                    <div class="warning" style="color: #f00; font-weight: bold; font-size: 0.85em; border: 1px solid #f00; background: rgba(20, 0, 0, 0.7); padding: 12px 16px; margin: 20px 0; letter-spacing: 1px;">
                        > ALERTA: Un error permitirá a ANZUELO infiltrarse en el sistema.
                    </div>
 
                    <!-- MENSAJE INTERCEPTADO -->
                    <div class="card" style="background: rgba(5, 0, 0, 0.9); border: 1px dashed #f00; padding: 20px; margin: 20px 0;">
                        <div style="font-family: monospace; font-size: 0.85em; border-bottom: 1px solid #400; padding-bottom: 10px; margin-bottom: 15px;">
                            <p><strong>REMITENTE:</strong>soporte@steam-eventos.com</p>
                            <p><strong>ASUNTO:</strong>¡Tu cuenta ha sido seleccionada para recibir contenido exclusivo!</p>
                        </div>
                        <div style="font-size: 0.9em; color: #aaa;">
                            <p>Hola jugador,</p>
                            <br>
                            <p>¡Enhorabuena! has sido elegido ganador de uno de nuestros concuersos recientes y tu cuenta ha sido elegida para recibir recompensas especiales.</p>
                            <p> Para activar el premio debes iniciar sesión y confirmar tus datos antes de las próximas 12 horas. </p>
                            <p style="margin: 15px 0; text-align: center;">
                                <span style="background: #200; color: #f00; padding: 5px; border: 1px solid #f00;">
                                    https://steam-eventos-premium.com/login
                                </span>
                            </p>
                            <p style="color: #f44; font-weight: bold;"> Si no completas la verificación, el premio será eliminado.</p>
                        </div>
                    </div>
 
                    <!-- RESPUESTA -->
                    <div style="margin: 20px 0;">
                        <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> ¿Qué detalle hace sospechoso este mensaje?</label>
                        <select name="respuesta_anzuelo" class="input-terminal" style="margin: 0; width: 100%;">
                            <option value="0">-- SELECCIONAR RESPUESTA --</option>
 
                            <!-- Distractores razonables -->
                            <option value="4"> El mensaje utiliza colores llamativos y premios para llamar la atención. </option>
 
                            <option value="6"> El mensaje crea urgencia para que el usuario actúe rápido.</option>
 
                            <option value="9"> El remitente parece relacionado con videojuegos online. </option>
 
                            <!-- RESPUESTA CORRECTA -->
                            <option value="2"> El enlace dirige a una web que intenta imitar una página oficial.</option>
                        </select>
                    </div>
 
                <?php elseif($id_ia == 4): // RETO RASTRO (Huella Digital) ?>
 
                    <div style="font-size: 0.9em; line-height: 1.5; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;">
 
                        <p><i>"Todo lo que haces en internet deja señales. Algunas parecen pequeñas… hasta que alguien las une."</i></p>
 
                        <p style="margin-top: 15px; color: #f00;">
                            > RASTRO ha recopilado información de distintas redes y sistemas para reconstruir los hábitos del operador.
                        </p>
 
                        <p style="margin-top: 10px;">
                            Tu misión es identificar qué datos podrían poner en riesgo la privacidad del usuario y marcarlos para su eliminación.
                        </p>
                    </div>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/RASTRO2.png" alt="Rastro" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio6.mp3" autoplay loop></audio>
                        <script>
                            // Forzar reproducción al hacer clic si el navegador bloquea el autoplay inicial
                            document.addEventListener('click', function() {
                                var audio = document.getElementById('bg-music');
                                if (audio.paused) {
                                    audio.play();
                                }
                            }, { once: true });
                        </script>
 
                    <div class="warning" style="color: #f00; font-weight: bold; font-size: 0.85em; border: 1px solid #f00; background: rgba(20, 0, 0, 0.7); padding: 12px 16px; margin: 20px 0; letter-spacing: 1px;">
                        > ALERTA: Compartir demasiada información puede permitir localizar rutinas, cuentas o ubicaciones personales.
                    </div>
 
                    <div style="margin: 20px 0;">
                        <div class="opciones-check" style="background: rgba(0, 10, 0, 0.8); border: 1px solid #0f0; padding: 18px 20px; font-size: 0.88em; line-height: 2.2;">
                            <p style="margin-bottom: 15px; color: #f00; font-weight: bold; letter-spacing: 1px;">SELECCIONA LOS DATOS QUE DEBERÍAN BORRARSE:</p>
 
                            <!-- Datos peligrosos -->
                            <input type="checkbox" name="borrado[]" value="log_wifi"> Conexión automática a una red WiFi pública del centro comercial<br><br>
 
                            <input type="checkbox" name="borrado[]" value="comentario"> Comentario en red social: "Mañana estaré solo en casa toda la tarde"<br><br>
 
                            <input type="checkbox" name="borrado[]" value="busqueda"> Búsqueda guardada: "Cómo recuperar la contraseña del correo escolar"<br><br>
 
                            <input type="checkbox" name="borrado[]" value="geotag"> Foto subida con ubicación GPS activada<br><br>
 
                            <input type="checkbox" name="borrado[]" value="registro"> Registro en una web desconocida usando el correo personal<br><br>
 
                            <!-- Datos menos sensibles -->
                            <input type="checkbox" name="borrado[]" value="clima"> Búsqueda: "Tiempo para mañana"<br><br>
 
                            <input type="checkbox" name="borrado[]" value="musica"> "Me gusta" en una canción de Spotify<br><br>
                        </div>
                    </div>
 
                <?php elseif($id_ia == 5): // --- SECTOR 5: PARÁSITO (Malware) --- ?>
 
                    <div style="font-size: 0.9em; line-height: 1.5; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;">
 
                        <p><i>"Se esconde dentro de archivos aparentemente normales. Juegos modificados, aplicaciones pirata, instaladores sospechosos... una sola ejecución basta para abrirme la puerta."</i></p>
 
                        <p style="margin-top: 10px;">
                            PARÁSITO utiliza descargas infectadas para entrar en dispositivos y propagarse sin que el usuario lo detecte.
                        </p>
                    </div>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/PARÁSITO2.png" alt="Parásito" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio7.mp3" autoplay loop></audio>
 
                    <div class="warning" style="color: #f00; font-weight: bold; font-size: 0.85em; border: 1px solid #f00; background: rgba(20, 0, 0, 0.7); padding: 12px 16px; margin: 20px 0; letter-spacing: 1px;">
                        > ALERTA: Un archivo infectado podría bloquear el acceso al sistema completo.
                    </div>
 
                    <div class="card" style="background: rgba(0, 15, 0, 0.9); border: 1px solid #0f0; padding: 20px; margin: 20px 0;">
 
                        <div style="margin: 20px 0;">
                            <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> Tu misión: Analiza las descargas interceptadas y detecta cuál supone un mayor riesgo de malware.</label>
                            <select name="respuesta_parasito" class="input-terminal" style="width: 100%;">
 
                                <option value="0">-- ANALIZAR ARCHIVO --</option>
 
                                <option value="safe">apuntes_historia_final.pdf</option>
 
                                <option value="safe">vacaciones_2025.jpg</option>
 
                                <option value="virus">generador_skin_premium.exe</option>
 
                                <option value="safe">musica_favorita.mp3</option>
 
                                <option value="safe">trabajo_tecnologia.zip</option>
 
                                <option value="safe">horario_clases.docx</option>
 
                            </select>
                        </div>
                    </div>
 
                <?php elseif($id_ia == 6): // --- SECTOR 6: NEXO (Redes Wi-Fi) --- ?>
 
                    <div style="font-size: 0.9em; line-height: 1.5; border-left: 3px solid #0f0; padding: 12px 16px; background: rgba(0, 20, 0, 0.5); margin: 20px 0;">
 
                        <p><i>"Las señales invisibles transportan toda vuestra información. Una red insegura basta para observarlo todo."</i></p>
 
                        <p style="margin-top: 10px;">
                            NEXO aprovecha conexiones mal protegidas para interceptar datos y acceder a dispositivos conectados.
                        </p>
                    </div>
 
                    <div style="text-align: center; margin: 25px 0;">
                        <img src="../Imagenes/NEXO.png" alt="IA NexO" style="width: 70%; height: auto; display: block; margin: 0 auto; border: 1px solid #0f0; box-shadow: 0 0 20px rgba(0, 255, 0, 0.15);">
                    </div>
 
                    <audio id="bg-music" src="Audio8.mp3" autoplay loop></audio>
 
                    <div class="warning" style="color: #f00; font-weight: bold; font-size: 0.85em; border: 1px solid #f00; background: rgba(20, 0, 0, 0.7); padding: 12px 16px; margin: 20px 0; letter-spacing: 1px;">
                        > ALERTA: Una conexión insegura permitirá a NEXO interceptar la información del sistema.
                    </div>
 
                    <div class="card" style="background: rgba(0, 15, 0, 0.9); border: 1px solid #0f0; padding: 20px; margin: 20px 0;">
 
                        <div style="margin: 20px 0;">
                            <label style="display: block; font-size: 0.8em; font-weight: bold; margin-bottom: 10px; color: #0f0; letter-spacing: 1px;">> Tu misión: Selecciona la red más segura para evitar el robo de datos.</label>
                            <select name="respuesta_nexo" class="input-terminal" style="width: 100%;">
 
                                <option value="0">-- ELEGIR RED --</option>
 
                                <option value="1">WiFi_Free_Aeropuerto (Red abierta)</option>
 
                                <option value="3">Casa_Invitados_WPA2 (Contraseña compartida con visitas)</option>
 
                                <option value="2">Red_Instituto_WPA3 (Cifrado avanzado)</option>
 
                                <option value="4">Cafeteria_OpenWiFi (Sin contraseña)</option>
 
                                <option value="5">GamingZone_Public (Acceso gratuito para clientes)</option>
 
                            </select>
                        </div>
                    </div> 
                <?php endif; ?>
                <input type="submit" value="EJECUTAR PROTOCOLO" class="btn-terminal">
            </form>
        </div>

        <a href="dashboard.php">ABORTAR OPERACIÓN</a>

    </div>

</body>
</html>