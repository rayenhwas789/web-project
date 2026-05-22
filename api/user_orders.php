<?php
/**
 * NEXUS Esports — User Orders API
 * POST /api/user_orders.php   → place an order (saved to DB, decrements stock)
 * GET  /api/user_orders.php?email=x  → get orders for a user email
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
    $stmt = $pdo->prepare("SELECT o.*, p.image_url FROM orders o LEFT JOIN products p ON o.product_id=p.id WHERE o.user_email=? ORDER BY o.ordered_at DESC");
    $stmt->execute([$email]);
    echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
    exit;
}

if ($method === 'POST') {
    $body  = json_decode(file_get_contents('php://input'), true);
    $email = trim($body['email'] ?? '');
    $items = $body['items'] ?? []; // [{product_id, item, qty, price}, ...]

    if (!$email || empty($items)) {
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>'email and items are required']);
        exit;
    }

    $inserted = [];
    $errors   = [];

    $pdo->beginTransaction();
    try {
        foreach ($items as $c) {
            $product_id = (int)($c['product_id'] ?? 0);
            $item       = trim($c['item']  ?? '');
            $qty        = max(1, (int)($c['qty']  ?? 1));
            $price      = (float)($c['price'] ?? 0);

            if (!$item || !$price) { $errors[] = "Invalid item: $item"; continue; }

            // Check & decrement stock
            if ($product_id) {
                $chk = $pdo->prepare("SELECT stock FROM products WHERE id=? FOR UPDATE");
                $chk->execute([$product_id]);
                $row = $chk->fetch();
                if (!$row || $row['stock'] < $qty) {
                    $errors[] = "Not enough stock for: $item";
                    continue;
                }
                $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id=?")->execute([$qty, $product_id]);
            }

            $stmt = $pdo->prepare("INSERT INTO orders (user_email, product_id, item, qty, price, status) VALUES (?,?,?,?,?,'processing')");
            $stmt->execute([$email, $product_id ?: null, $item, $qty, $price]);
            $inserted[] = $pdo->lastInsertId();
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo json_encode(['success'=>false,'error'=>'Order failed: '.$e->getMessage()]);
        exit;
    }

    if (empty($inserted)) {
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>implode('; ', $errors)]);
        exit;
    }

    echo json_encode(['success'=>true,'order_ids'=>$inserted,'errors'=>$errors]);
    exit;
}

http_response_code(405);
echo json_encode(['success' => false, 'error' => 'Method not allowed']);
