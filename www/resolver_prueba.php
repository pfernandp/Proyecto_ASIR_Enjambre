<?php
session_start();
require_once("../config/db.php");

// 1. CAPTURA DE PARÁMETROS Y SEGURIDAD INICIAL
$id_p = isset($_GET['id_p']) ? intval($_GET['id_p']) : null;
$id_u = $_SESSION['usuario_id'] ?? null;
$valida = false;

if (!$id_p || !$id_u) {
    header("Location: dashboard.php?msg=sector_neutralizado");
    exit();
}

// 2. LÓGICA DE VALIDACIÓN POR SECTOR DE IA
try {
 
    if ($id_p == 1) {
        // --- SECTOR CLAVE (Contraseñas) ---
        $resp = $_POST['respuesta'];
 
        // Validación mediante RegEx: mínimo 8 caracteres, una mayúscula, un número y un símbolo
        $patron = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])(?=.{8,})/';
        if (preg_match($patron, $resp)) {
            $valida = true;
        }
 
    } elseif ($id_p == 2) {
        // --- SECTOR VELO (Privacidad) ---
        $perfil = isset($_POST['perfil']) ? $_POST['perfil'] : [];
 
        // Definimos qué valores consideramos "Seguros" y permitidos
        $valores_seguros   = ['alias', 'intereses', 'comentarios'];
 
        // Definimos qué valores son "Inseguros" y NO deben estar en el array
        $valores_inseguros = ['nombre', 'gps', 'centro', 'telefono', 'directo', 'nacimiento', 'familia', 'email', 'metadatos'];
 
        // Lógica de éxito:
        // 1. Que el usuario haya marcado al menos los datos seguros básicos.
        // 2. Que NO haya ningún valor inseguro en el array recibido.
        $tiene_inseguros = !empty(array_intersect($perfil, $valores_inseguros));
        $tiene_seguros   = in_array('alias', $perfil); // Obligamos al menos al alias
 
        if (!$tiene_inseguros && $tiene_seguros) {
            $valida = true;
        }
 
    } elseif ($id_p == 3) {
        // --- SECTOR ANZUELO (Phishing) ---
        $resp_anzuelo = $_POST['respuesta_anzuelo'];
 
        // La opción "2" identifica correctamente el dominio fraudulento
        if ($resp_anzuelo == "2") {
            $valida = true;
        }
 
    } elseif ($id_p == 4) {
        // --- SECTOR RASTRO (Huella Digital) ---
        $borrado = isset($_POST['borrado']) ? $_POST['borrado'] : [];
 
        // Valores que representan un riesgo real para la privacidad y deben marcarse
        $datos_peligrosos = ['log_wifi', 'comentario', 'busqueda', 'geotag', 'registro'];
 
        // Valores que NO representan un riesgo real y NO deben marcarse
        $datos_seguros = ['clima', 'musica'];
 
        // Lógica de éxito:
        // 1. Que todos los datos peligrosos estén marcados para borrar.
        // 2. Que ningún dato seguro haya sido marcado por error.
        $marca_todos_peligrosos = empty(array_diff($datos_peligrosos, $borrado));
        $marca_algun_seguro     = !empty(array_intersect($borrado, $datos_seguros));
 
        if ($marca_todos_peligrosos && !$marca_algun_seguro) {
            $valida = true;
        }
 
    } elseif ($id_p == 5) {
        // --- SECTOR PARÁSITO (Malware) ---
        $resp_parasito = $_POST['respuesta_parasito'];
 
        // El valor "virus" corresponde a generador_skin_premium.exe, el único archivo infectado
        if ($resp_parasito == "virus") {
            $valida = true;
        }
 
    } elseif ($id_p == 6) {
        // --- SECTOR NEXO (Redes Wi-Fi) ---
        $resp_nexo = $_POST['respuesta_nexo'];
 
        // La opción "2" corresponde a Red_Instituto_WPA3, la red con cifrado avanzado
        if ($resp_nexo == "2") {
            $valida = true;
        }
    }
 
    // 3. PERSISTENCIA DE DATOS Y AUDITORÍA
    if ($valida) {
        // REGISTRO DE ÉXITO: Actualizamos el progreso del alumno
        // ON DUPLICATE KEY UPDATE mantiene la integridad si repite la prueba
        $stmt = $pdo->prepare("INSERT INTO ESTADO_USUARIO_PRUEBA (id_usuario, id_prueba, completada, tiempo_fin)
                               VALUES (?, ?, 1, NOW())
                               ON DUPLICATE KEY UPDATE completada = 1, tiempo_fin = NOW()");
        $stmt->execute([$id_u, $id_p]);
 
        header("Location: dashboard.php?msg=sector_neutralizado");
        exit();
 
    } else {
        // REGISTRO DE FALLO (AUDITORÍA): El sistema detecta el intento fallido
        $mensaje = "Alerta: Intento fallido de bypass en Sector IA ID: " . $id_p;
        $alerta  = 'Medio'; // Nivel de criticidad para el DBA
 
        $stmt_log = $pdo->prepare("INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES (?, ?, ?)");
        $stmt_log->execute([$id_u, $mensaje, $alerta]);
 
        header("Location: ia_detalle.php?id=" . $id_p . "&error=access_denied");
        exit();
    }

    // 4. LÓGICA DE CIERRE
    // Consultamos el Nivel Interno mediante una función de agregación para auditar cuántos sectores ha neutralizado el operador.
        // Esta consulta sobre la tabla asociativa ESTADO_USUARIO_PRUEBA garantiza la integridad del progreso.
        $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM ESTADO_USUARIO_PRUEBA WHERE id_usuario = ? AND completada = 1");
        $stmt_check->execute([$id_u]);
        $sectores_neutralizados = $stmt_check->fetchColumn();

        // Implementamos el control de flujo de la aplicación basado en el estado de la base de datos.
        if ($sectores_neutralizados == 6) {
            // Si el contador es igual a 6, el sistema habilita automáticamente el Protocolo de Restauración Final.
            // La instrucción header() realiza una redirección de servidor segura, ocultando la lógica interna al cliente.
            header("Location: protocolo_final.php");
            exit(); // Finalizamos la ejecución del script para liberar recursos del servidor Apache.
        } else {
            // Si faltan sectores por intervenir, el operador regresa al Centro de Contención.
            header("Location: dashboard.php?msg=sector_neutralizado");
            exit();
        }
 
} catch (PDOException $e) {
    // Gestión administrativa de excepciones del SGBD
    die("Error crítico en el Nivel Interno: " . $e->getMessage());
}
?>