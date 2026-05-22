<?php
/**
 * NEXUS Esports — Admin Users API
 * GET    /api/admin_users.php          → list all users
 * POST   /api/admin_users.php          → add user
 * PUT    /api/admin_users.php?id=N     → update user
 * DELETE /api/admin_users.php?id=N     → delete user
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../db.php';

$method = $_SERVER['REQUEST_METHOD'];
$id     = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($method) {

    // ── LIST ──────────────────────────────────────────────
    case 'GET':
        $stmt = $pdo->query("SELECT id, username, email, country, bio, skill_level, role, status, joined_at FROM users ORDER BY joined_at DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    // ── CREATE ────────────────────────────────────────────
    case 'POST':
        $body = json_decode(file_get_contents('php://input'), true);
        $username    = trim($body['username']    ?? '');
        $email       = trim($body['email']       ?? '');
        $password    = trim($body['password']    ?? '');
        $country     = trim($body['country']     ?? '');
        $bio         = trim($body['bio']         ?? '');
        $skill_level = (int)($body['skill_level'] ?? 0);
        $role        = in_array($body['role'] ?? '', ['user','admin']) ? $body['role'] : 'user';
        $status      = in_array($body['status'] ?? '', ['active','banned','pending']) ? $body['status'] : 'active';

        if (!$username || !$email || !$password) {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'username, email and password are required']);
            break;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, country, bio, skill_level, role, status) VALUES (?,?,?,?,?,?,?,?)");
        $stmt->execute([$username, $email, $hash, $country, $bio, $skill_level, $role, $status]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    // ── UPDATE ────────────────────────────────────────────
    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $body = json_decode(file_get_contents('php://input'), true);

        $fields = [];
        $params = [];

        if (isset($body['username']))    { $fields[] = 'username=?';    $params[] = trim($body['username']); }
        if (isset($body['email']))       { $fields[] = 'email=?';       $params[] = trim($body['email']); }
        if (isset($body['country']))     { $fields[] = 'country=?';     $params[] = trim($body['country']); }
        if (isset($body['bio']))         { $fields[] = 'bio=?';         $params[] = trim($body['bio']); }
        if (isset($body['skill_level'])){ $fields[] = 'skill_level=?'; $params[] = (int)$body['skill_level']; }
        if (isset($body['role'])   && in_array($body['role'],   ['user','admin']))                    { $fields[] = 'role=?';   $params[] = $body['role']; }
        if (isset($body['status']) && in_array($body['status'], ['active','banned','pending']))       { $fields[] = 'status=?'; $params[] = $body['status']; }
        if (!empty($body['password']))  { $fields[] = 'password=?';    $params[] = password_hash($body['password'], PASSWORD_BCRYPT); }

        if (!$fields) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'no fields to update']); break; }

        $params[] = $id;
        $pdo->prepare("UPDATE users SET " . implode(',', $fields) . " WHERE id=?")->execute($params);
        echo json_encode(['success' => true]);
        break;

    // ── DELETE ────────────────────────────────────────────
    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
