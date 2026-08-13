<?php

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only GET method is allowed"
    ]);

    exit;
}

try {

    $sql = "SELECT
                id,
                name,
                price,
                description,
                status,
                created_at,
                updated_at
            FROM products
            WHERE status = 1
            ORDER BY id DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $products = $stmt->fetchAll();

    echo json_encode([
        "success" => true,
        "message" => "Products fetched successfully",
        "count" => count($products),
        "data" => $products
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to fetch products"
    ]);
}