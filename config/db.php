<?php
// --- PARÁMETROS DE CONEXIÓN AL SGBD MARIADB ---
$host = "localhost";
$db   = "enjambre";
$user = "admin";
$pass = "admin";
$charset = "utf8";          // Conjunto de caracteres para integridad de datos

// Configuración del Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Opciones de configuración de la interfaz PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Lanza excepciones en errores SQL
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Devuelve arrays asociativos por defecto
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Usa sentencias preparadas reales del SGBD
];

try {
    // Instanciación del objeto PDO para su uso global en el stack LAMP
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (\PDOException $e) {
    // Gestión administrativa de errores: Evitamos mostrar rutas internas al usuario final
    die("Fallo crítico en la comunicación con el motor de base de datos: " . $e->getMessage());
}
?>
