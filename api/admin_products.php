<?php
/**
 * NEXUS Esports — Admin Products API
 * GET    /api/admin_products.php          → list all
 * POST   /api/admin_products.php          → create
 * PUT    /api/admin_products.php?id=N     → update
 * DELETE /api/admin_products.php?id=N     → delete
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
        $stmt = $pdo->query("SELECT * FROM products ORDER BY created_at DESC");
        echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
        break;

    case 'POST':
        $body      = json_decode(file_get_contents('php://input'), true);
        $name      = trim($body['name']      ?? '');
        $category  = trim($body['category']  ?? '');
        $price     = (float)($body['price']  ?? 0);
        $stock     = (int)($body['stock']    ?? 0);
        $image_url = trim($body['image_url'] ?? '');

        if (!$name) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'name required']); break; }

        $stmt = $pdo->prepare("INSERT INTO products (name, category, price, stock, image_url) VALUES (?,?,?,?,?)");
        $stmt->execute([$name, $category, $price, $stock, $image_url]);
        echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        break;

    case 'PUT':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $body = json_decode(file_get_contents('php://input'), true);

        $fields = []; $params = [];
        if (isset($body['name']))      { $fields[] = 'name=?';      $params[] = trim($body['name']); }
        if (isset($body['category']))  { $fields[] = 'category=?';  $params[] = trim($body['category']); }
        if (isset($body['price']))     { $fields[] = 'price=?';     $params[] = (float)$body['price']; }
        if (isset($body['stock']))     { $fields[] = 'stock=?';     $params[] = (int)$body['stock']; }
        if (isset($body['image_url'])){ $fields[] = 'image_url=?'; $params[] = trim($body['image_url']); }

        if (!$fields) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'no fields']); break; }

        $params[] = $id;
        $pdo->prepare("UPDATE products SET " . implode(',', $fields) . " WHERE id=?")->execute($params);
        echo json_encode(['success' => true]);
        break;

    case 'DELETE':
        if (!$id) { http_response_code(400); echo json_encode(['success'=>false,'error'=>'id required']); break; }
        $pdo->prepare("DELETE FROM products WHERE id=?")->execute([$id]);
        echo json_encode(['success' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
}
