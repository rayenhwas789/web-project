<?php
/**
 * NEXUS Esports — User Bookings API
 * POST /api/user_bookings.php   → create a new tournament booking (saved to DB)
 * GET  /api/user_bookings.php?email=x  → get bookings for a user email
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $email = trim($_GET['email'] ?? '');
    if (!$email) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'email required']); exit; }
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE email=? ORDER BY booked_at DESC");
    $stmt->execute([$email]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $body       = json_decode(file_get_contents('php://input'), true);
    $name       = trim($body['name']       ?? '');
    $email      = trim($body['email']      ?? '');
    $tournament = trim($body['tournament'] ?? '');
    $game       = trim($body['game']       ?? '');
    $team       = trim($body['team']       ?? 'Solo');

    if (!$name || !$email || !$tournament) {
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>'name, email and tournament are required']);
        exit;
    }

    // Resolve user_id from email if account exists
    $uStmt = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
    $uStmt->execute([$email]);
    $user = $uStmt->fetch();
    $user_id = $user ? (int)$user['id'] : null;

    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, name, email, tournament, game, team) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$user_id, $name, $email, $tournament, $game, $team]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
