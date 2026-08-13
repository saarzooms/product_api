<?php

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only PUT method is allowed"
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
$name = trim($data["name"] ?? "");
$price = $data["price"] ?? null;
$description = trim($data["description"] ?? "");

if (!$id || $name === "" || $price === null) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "ID, name and price are required"
    ]);

    exit;
}

if (!is_numeric($price) || $price < 0) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Price must be a valid positive number"
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

    // Update product
    $sql = "UPDATE products
            SET
                name = :name,
                price = :price,
                description = :description
            WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":description" => $description,
        ":id" => $id
    ]);

    // Get updated product
    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $product = $stmt->fetch();

    echo json_encode([
        "success" => true,
        "message" => "Product updated successfully",
        "data" => $product
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update product"
    ]);
}