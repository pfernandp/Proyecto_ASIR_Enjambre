<?php
session_start();
// Control de Acceso: Verificamos identidad del operador
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit();
}

require_once("../config/db.php"); // Parámetros de conexión al SGBD MariaDB

$id_u = $_SESSION['usuario_id'];
$codigo_recibido = trim($_POST['codigo_maestro'] ?? '');

try {
    // 1. RECUPERACIÓN DE LA SECUENCIA MAESTRA DESDE EL NIVEL INTERNO
    // Consultamos los fragmentos asignados al usuario en el orden correcto definido en el diccionario de datos
    $stmt = $pdo->prepare("
        SELECT p.fragmento_codigo 
        FROM PRUEBA p
        JOIN ESTADO_USUARIO_PRUEBA e ON p.id_prueba = e.id_prueba
        WHERE e.id_usuario = ? AND e.completada = 1
        ORDER BY p.orden ASC
    ");
    $stmt->execute([$id_u]);
    
    // Obtenemos solo la columna de fragmentos para facilitar la concatenación
    $fragmentos = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Verificamos que el operador tenga los 6 sectores neutralizados en MariaDB
    if (count($fragmentos) < 6) {
        header("Location: dashboard.php?msg=acceso_no_autorizado");
        exit();
    }

    // Construimos la secuencia esperada concatenando los 6 fragmentos (p.ej. FRAG-C1...FRAG-N6)
    $secuencia_esperada = implode('', $fragmentos);

    // 2. VALIDACIÓN Y AUDITORÍA DE SEGURIDAD
    if ($codigo_recibido === $secuencia_esperada) {
        // --- ESCENARIO DE ÉXITO TOTAL ---
        // Auditoría: Registramos el evento crítico de restauración en LOG_IA
        $mensaje_log = "PROTOCOLO DE RESTAURACIÓN EJECUTADO CON ÉXITO. El Enjambre ha sido contenido por el operador.";
        $stmt_log = $pdo->prepare("INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES (?, ?, 'Bajo')");
        $stmt_log->execute([$id_u, $mensaje_log]);

        // Redirigimos a la pantalla de victoria final
        header("Location: victoria.php");
        exit();
    } else {
        // --- ESCENARIO DE FALLO EN LA SECUENCIA ---
        // Auditoría: Registramos el intento fallido como alerta media para monitorización
        $mensaje_error = "ALERTA: Error en la secuencia de restauración maestra. Posible desincronización de fragmentos.";
        $stmt_log = $pdo->prepare("INSERT INTO LOG_IA (id_usuario, mensaje, nivel_alerta) VALUES (?, ?, 'Medio')");
        $stmt_log->execute([$id_u, $mensaje_error]);

        // Devolvemos al operador al protocolo final con el aviso de error por GET
        header("Location: protocolo_final.php?error=1");
        exit();
    }

} catch (PDOException $e) {
    // Gestión administrativa de excepciones: preservamos la seguridad del sistema sin exponer rutas internas
    die("Fallo crítico en el Nivel Interno durante el protocolo de restauración: " . $e->getMessage());
}
?>
