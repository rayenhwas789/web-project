<?php
/**
 * NEXUS Esports — Admin Stats API
 * GET /api/admin_stats.php → aggregated dashboard numbers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once '../db.php';

$stats = [];

$stats['total_users']    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$stats['total_bookings'] = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$stats['total_orders']   = (int)$pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$stats['total_revenue']  = (float)$pdo->query("SELECT COALESCE(SUM(price * qty),0) FROM orders")->fetchColumn();
$stats['total_products'] = (int)$pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$stats['avg_order']      = $stats['total_orders'] > 0
    ? round($stats['total_revenue'] / $stats['total_orders'], 2)
    : 0;

// Order status breakdown
$statusRows = $pdo->query("SELECT status, COUNT(*) AS cnt FROM orders GROUP BY status ORDER BY cnt DESC")->fetchAll();
$stats['orders_by_status'] = $statusRows;

// Games popularity from bookings
$gameRows = $pdo->query("SELECT game, COUNT(*) AS cnt FROM bookings WHERE game != '' GROUP BY game ORDER BY cnt DESC")->fetchAll();
$stats['games'] = $gameRows;

// Revenue by product (top 6)
$revenueRows = $pdo->query("SELECT item, SUM(price*qty) AS total FROM orders GROUP BY item ORDER BY total DESC LIMIT 6")->fetchAll();
$stats['revenue_by_product'] = $revenueRows;

// Users by country
$countryRows = $pdo->query("SELECT country, COUNT(*) AS cnt FROM users WHERE country != '' GROUP BY country ORDER BY cnt DESC")->fetchAll();
$stats['users_by_country'] = $countryRows;

// Active vs banned vs pending users
$userStatusRows = $pdo->query("SELECT status, COUNT(*) AS cnt FROM users GROUP BY status")->fetchAll();
$stats['users_by_status'] = $userStatusRows;

// Most popular tournaments
$tournamentRows = $pdo->query("SELECT tournament, COUNT(*) AS cnt FROM bookings WHERE tournament != '' GROUP BY tournament ORDER BY cnt DESC")->fetchAll();
$stats['tournaments'] = $tournamentRows;

// Low stock products (stock < 20)
$lowStockRows = $pdo->query("SELECT name, stock FROM products WHERE stock < 20 ORDER BY stock ASC")->fetchAll();
$stats['low_stock'] = $lowStockRows;

echo json_encode(['success' => true, 'data' => $stats]);
