<?php
/**
 * NEXUS Esports — Public Products API (user-facing)
 * GET /api/user_products.php   → list all products from DB
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$stmt = $pdo->query("SELECT id, name, category, price, stock, image_url FROM products WHERE stock > 0 ORDER BY category, name");
echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
