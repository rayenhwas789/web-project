<?php
/**
 * NEXUS Esports — Admin Orders API
 * GET    /api/admin_orders.php          → list all
 * POST   /api/admin_orders.php          → create
 * PUT    /api/admin_orders.php?id=N     → update (status, qty, price…)
 * DELETE /api/admin_orders.php?id=N     → delete
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
        $stmt = $pdo->query("SELECT o.*, p.name AS product_name FROM orders o LEFT JOIN products p ON o.product_id = p.id ORDER BY o.ordered_at DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'POST':
        $body       = json_decode(file_get_contents('php://input'), true);
        $user_email = trim($body['user_email'] ?? '');
        $item       = trim($body['item']       ?? '');
        $qty        = (int)($body['qty']       ?? 1);
        $price      = (float)($body['price']   ?? 0);
        $product_id = !empty($body['product_id']) ? (int)$body['product_id'] : null;
        $status     = in_array($body['status'] ?? '', ['processing','shipped','delivered','cancelled']) ? $body['status'] : 'processing';

        if (!$user_email || !$item) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'user_email and item required']); break; }

        $stmt = $pdo->prepare("INSERT INTO orders (user_email, product_id, item, qty, price, status) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$user_email, $product_id, $item, $qty, $price, $status]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $body = json_decode(file_get_contents('php://input'), true);

        $fields = []; $params = [];
        if (isset($body['user_email'])){ $fields[] = 'user_email=?'; $params[] = trim($body['user_email']); }
        if (isset($body['item']))      { $fields[] = 'item=?';       $params[] = trim($body['item']); }
        if (isset($body['qty']))       { $fields[] = 'qty=?';        $params[] = (int)$body['qty']; }
        if (isset($body['price']))     { $fields[] = 'price=?';      $params[] = (float)$body['price']; }
        if (isset($body['status']) && in_array($body['status'], ['processing','shipped','delivered','cancelled'])) {
            $fields[] = 'status=?'; $params[] = $body['status'];
        }

        if (!$fields) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'no fields']); break; }

        $params[] = $id;
        $pdo->prepare("UPDATE orders SET " . implode(',', $fields) . " WHERE id=?")->execute($params);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $pdo->prepare("DELETE FROM orders WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
