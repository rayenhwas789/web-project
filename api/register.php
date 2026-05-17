<?php
/**
 * NEXUS Esports — Register API
 * POST: { "username": "...", "email": "...", "password": "...", "country": "..." }
 * Returns JSON
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'error' => 'Method not allowed']));
}

require_once '../db.php';

$body     = json_decode(file_get_contents('php://input'), true);
$username = trim($body['username'] ?? '');
$email    = trim($body['email']    ?? '');
$password = trim($body['password'] ?? '');
$country  = trim($body['country']  ?? '');

// Validation
if (!$username || !$email || !$password) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Username, email and password are required']));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Invalid email address']));
}

if (strlen($password) < 6) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Password must be at least 6 characters']));
}

// Check if email already exists
$check = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$check->execute([$email]);
if ($check->fetch()) {
    http_response_code(409);
    die(json_encode(['success' => false, 'error' => 'This email is already registered']));
}

// Hash password and insert
$hash = password_hash($password, PASSWORD_BCRYPT);

$stmt = $pdo->prepare("
    INSERT INTO users (username, email, password, country, role, status)
    VALUES (?, ?, ?, ?, 'user', 'active')
");
$stmt->execute([$username, $email, $hash, $country]);

$newId = $pdo->lastInsertId();

echo json_encode([
    'success' => true,
    'message' => 'Account created successfully',
    'user'    => [
        'id'       => $newId,
        'username' => $username,
        'email'    => $email,
        'country'  => $country,
        'role'     => 'user',
    ]
]);
