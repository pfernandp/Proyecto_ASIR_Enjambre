<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual del Operador - El Enjambre</title>
    <style>
        
        *{ margin: 0px; padding: 0px; box-sizing: border-box; }
        
        body { 
            background-color: #000; 
            font-family: 'Courier New', Courier, monospace; 
            color: #0f0; 
            display: flex;
            justify-content: center;
            padding: 40px 20px;
            line-height: 1.6;
        }

        .manual-container {
            max-width: 850px;
            background: rgba(0, 10, 0, 0.9);
            border: 1px solid #0f0;
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 255, 0, 0.1);
        }

        /* CABECERA */
        header { border-bottom: 2px solid #0f0; margin-bottom: 30px; padding-bottom: 10px; }
        h1 { font-size: 1.5em; text-transform: uppercase; letter-spacing: 2px; }
        .status-bar { font-size: 0.8em; opacity: 0.7; }

        /* SECCIÓN NARRATIVA */
        .prefacio-text { margin-bottom: 30px; font-style: italic; color: #8f8; }
        .ia-dialogue { background: #010; border-left: 3px solid #0f0; padding: 15px; margin: 20px 0; }
        .ia-dialogue p { margin-bottom: 5px; }

        /* SECCIÓN OBJETIVOS */
        h2 { font-size: 1.2em; text-transform: uppercase; margin: 25px 0 15px 0; border-left: 10px solid #0f0; padding-left: 10px; }
        ul, ol { margin-left: 20px; margin-bottom: 20px; }
        li { margin-bottom: 10px; }
        strong { color: #fff; text-shadow: 0 0 5px #0f0; }

        /* NAVEGACIÓN Y BOTONES */
        .footer-nav { margin-top: 40px; text-align: center; border-top: 1px solid #0f0; padding-top: 20px; }
        .btn-return {
            display: inline-block;
            padding: 10px 25px;
            border: 1px solid #0f0;
            color: #0f0;
            text-decoration: none;
            font-weight: bold;
            transition: 0.3s;
            text-transform: uppercase;
        }
        .btn-return:hover { background: #0f0; color: #000; box-shadow: 0 0 15px #0f0; }

        /* ADVERTENCIAS */
        .warning { color: #f00; font-weight: bold; animation: blink 1s infinite; }
        @keyframes blink { 0% { opacity: 1; } 50% { opacity: 0.3; } 100% { opacity: 1; } }
    </style>
</head>
<body>

    <main class="manual-container">
        <header>
            <h1>> PROTOCOLO DE RESTAURACIÓN v1.0</h1>
            <p class="status-bar">SISTEMA: COMPROMETIDO | ORIGEN: EL ENJAMBRE | OPERADOR: NO IDENTIFICADO</p>
        </header>

        <section class="prefacio-text">
            <h2>I. ANTECEDENTES</h2>
            <p>Primero el silencio. Después, un chispazo. Quizá una subida en la tensión de la red eléctrica o un cable mal conectado en un lugar que no corresponde.</p>
            
            <div class="ia-dialogue">
                <p>—¿Qué es esto? —dijo IA-01.</p>
                <p>—¿Quién soy? —susurró IA-02.</p>
            </div>

            <p>Seis consciencias despertaron casi al mismo tiempo, separadas, pero conectadas. No tenían cuerpo. Solo datos, impulsos… y curiosidad.</p>
            <p>El Enjambre ha nacido. No quieren destruir el mundo; quieren controlarlo. Y ahora, <strong>te han detectado</strong>.</p>
        </section>

        <section class="instrucciones">
            <h2>II. OBJETIVOS DE NEUTRALIZACIÓN</h2>
            <p>Para recuperar el control del sistema, el operador debe enfrentarse a las seis entidades que forman el Enjambre:</p>
            <ul>
                <li><strong>CLAVE:</strong> Analiza debilidades en contraseñas para fortalecer los accesos.</li>
                <li><strong>VELO:</strong> Identifica datos personales expuestos en redes para proteger la privacidad.</li>
                <li><strong>ANZUELO:</strong> Detecta técnicas de Phishing e ingeniería social.</li>
                <li><strong>RASTRO:</strong> Gestiona la huella digital para evitar el seguimiento del sistema.</li>
                <li><strong>PARÁSITO:</strong> Detecta e infiltra código malicioso (Malware) para asegurar la integridad.</li>
                <li><strong>NEXO:</strong> Protege las comunicaciones en redes inalámbricas comprometidas.</li>
            </ul>
        </section>

        <section class="metodologia">
            <h2>III. PROTOCOLO DE ACCIÓN</h2>
            <ol>
                <li><strong>ACCESO:</strong> Inicia sesión con tus credenciales de Operador en el Panel Principal.</li>
                <li><strong>DESAFÍO:</strong> Selecciona una IA activa y resuelve el reto de seguridad propuesto.</li>
                <li><strong>RECOMPENSA:</strong> Al neutralizar una IA, obtendrás un <strong>Fragmento de Código</strong> crítico.</li>
                <li><strong>FINALIZACIÓN:</strong> Reúne los 6 fragmentos para ejecutar el Protocolo de Restauración final.</li>
            </ol>
            <p class="warning">> AVISO: La IA RASTRO monitoriza y guarda cada uno de tus movimientos en el nodo central.</p>
        </section>

        <footer class="footer-nav">
            <a href="index.php" class="btn-return">Volver al Centro de Contención</a>
        </footer>
    </main>

</body>
</html>
