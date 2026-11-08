<?php
require_once('includes/load.php');
header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'search') {
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    if ($q === '') { echo json_encode([]); exit; }
    $results = search_products($q);
    echo json_encode($results);
    exit;
}

if ($action === 'info') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $p  = find_product_by_id($id);
    echo json_encode($p);
    exit;
}

echo json_encode([]);
