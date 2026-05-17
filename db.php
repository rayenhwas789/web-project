<?php
/**
 * NEXUS Esports — Database Configuration
 * Edit DB_USER and DB_PASS to match your XAMPP setup
 * Default XAMPP: user = root, pass = (empty string)
 */

define('DB_HOST',    'localhost');
define('DB_USER',    'root');       // change if needed
define('DB_PASS',    '');           // change if needed
define('DB_NAME',    'nexus_esports');
define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]));
}
