<?php
/**
 * NEXUS Esports — Login API
 * POST: { "email": "...", "password": "..." }
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

$body = json_decode(file_get_contents('php://input'), true);
$email    = trim($body['email']    ?? '');
$password = trim($body['password'] ?? '');

if (!$email || !$password) {
    http_response_code(400);
    die(json_encode(['success' => false, 'error' => 'Email and password are required']));
}

// Fetch user by email
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Invalid email or password']));
}

// Support both plain-text passwords (manually added in phpMyAdmin)
// and bcrypt hashes (registered via the site)
$passwordOk = false;

if (password_get_info($user['password'])['algo'] !== null && password_get_info($user['password'])['algo'] !== 0) {
    // It's a bcrypt hash
    $passwordOk = password_verify($password, $user['password']);
} else {
    // Plain text (added manually via phpMyAdmin)
    $passwordOk = ($password === $user['password']);

    // Upgrade to bcrypt now that user is logging in
    if ($passwordOk) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $upd  = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $upd->execute([$hash, $user['id']]);
    }
}

if (!$passwordOk) {
    http_response_code(401);
    die(json_encode(['success' => false, 'error' => 'Invalid email or password']));
}

if ($user['status'] === 'banned') {
    http_response_code(403);
    die(json_encode(['success' => false, 'error' => 'This account has been banned']));
}

// Return safe user data (no password)
$safe = [
    'id'          => $user['id'],
    'username'    => $user['username'],
    'email'       => $user['email'],
    'country'     => $user['country'],
    'bio'         => $user['bio'],
    'skill_level' => $user['skill_level'],
    'role'        => $user['role'],
    'status'      => $user['status'],
];

echo json_encode(['success' => true, 'user' => $safe]);
