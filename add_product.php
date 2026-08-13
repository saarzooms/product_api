<?php

require_once "config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Only POST method is allowed"
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

$name = trim($data["name"] ?? "");
$price = $data["price"] ?? null;
$description = trim($data["description"] ?? "");

if ($name === "" || $price === null) {

    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Name and price are required"
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

    $sql = "INSERT INTO products
            (name, price, description)
            VALUES (:name, :price, :description)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":price" => $price,
        ":description" => $description
    ]);

    $id = $pdo->lastInsertId();

    $stmt = $pdo->prepare(
        "SELECT * FROM products WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $product = $stmt->fetch();

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Product added successfully",
        "data" => $product
    ]);

} catch (PDOException $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to add product"
    ]);
}