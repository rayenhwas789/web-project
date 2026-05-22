<?php
/**
 * NEXUS Esports — Admin Login API
 * POST: { "username": "...", "password": "..." }
 * Returns JSON — only allows users with role = 'admin'
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../db.php';

$body     = json_decode(file_get_contents('php://input'), true);
$username = trim($body['username'] ?? '');
$password = trim($body['password'] ?? '');

if (!$username || !$password) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Username and password required']));
}

// Look up by username OR email, must be admin role
$stmt = $pdo->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND role = 'admin' LIMIT 1");
$stmt->execute([$username, $username]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Invalid admin credentials']));
}

// Verify password (bcrypt or plain text)
$ok = false;
if (password_get_info($user['password'])['algo'] !== null && password_get_info($user['password'])['algo'] !== 0) {
    $ok = password_verify($password, $user['password']);
} else {
    $ok = ($password === $user['password']);
    if ($ok) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $user['id']]);
    }
}

if (!$ok) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Invalid admin credentials']));
}

echo json_encode(['success' => true, 'username' => $user['username']]);
