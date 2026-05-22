<?php
/**
 * NEXUS Esports — Admin Bookings API
 * GET    /api/admin_bookings.php          → list all
 * POST   /api/admin_bookings.php          → create
 * PUT    /api/admin_bookings.php?id=N     → update
 * DELETE /api/admin_bookings.php?id=N     → delete
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

    case 'GET':
        $stmt = $pdo->query("SELECT b.*, u.username FROM bookings b LEFT JOIN users u ON b.user_id = u.id ORDER BY b.booked_at DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'POST':
        $body       = json_decode(file_get_contents('php://input'), true);
        $name       = trim($body['name']       ?? '');
        $email      = trim($body['email']      ?? '');
        $tournament = trim($body['tournament'] ?? '');
        $game       = trim($body['game']       ?? '');
        $team       = trim($body['team']       ?? '');
        $user_id    = !empty($body['user_id']) ? (int)$body['user_id'] : null;

        if (!$name || !$email) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'name and email required']); break; }

        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, name, email, tournament, game, team) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$user_id, $name, $email, $tournament, $game, $team]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $body = json_decode(file_get_contents('php://input'), true);

        $fields = []; $params = [];
        if (isset($body['name']))       { $fields[] = 'name=?';       $params[] = trim($body['name']); }
        if (isset($body['email']))      { $fields[] = 'email=?';      $params[] = trim($body['email']); }
        if (isset($body['tournament'])){ $fields[] = 'tournament=?'; $params[] = trim($body['tournament']); }
        if (isset($body['game']))       { $fields[] = 'game=?';       $params[] = trim($body['game']); }
        if (isset($body['team']))       { $fields[] = 'team=?';       $params[] = trim($body['team']); }

        if (!$fields) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'no fields']); break; }

        $params[] = $id;
        $pdo->prepare("UPDATE bookings SET " . implode(',', $fields) . " WHERE id=?")->execute($params);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $pdo->prepare("DELETE FROM bookings WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
