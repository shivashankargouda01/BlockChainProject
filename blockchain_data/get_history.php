<?php
header('Content-Type: application/json');

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    echo json_encode(["error" => "Product ID is missing."]);
    exit;
}

$chain = json_decode(file_get_contents("../blockchain/chain.json"), true);
$history = [];

foreach ($chain as $block) {
    if ($block['product_id'] == $product_id) {
        $history[] = $block;
    }
}

if (count($history) > 0) {
    echo json_encode(["history" => $history], JSON_PRETTY_PRINT);
} else {
    echo json_encode(["error" => "❌ Product not found."]);
}
?>
