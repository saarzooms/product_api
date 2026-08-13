<?php

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only DELETE method is allowed"
    ]);

    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid JSON"
    ]);

    exit;
}

$id = $data["id"] ?? null;

if (!$id) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Product ID is required"
    ]);

    exit;
}

try {

    // Check product
    $stmt = $pdo->prepare(
        "SELECT id FROM products
         WHERE id = :id AND status = 1"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    if (!$stmt->fetch()) {

        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Product not found"
        ]);

        exit;
    }

    // Soft delete
    $stmt = $pdo->prepare(
        "UPDATE products
         SET status = 0
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Product deleted successfully"
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete product"
    ]);
}