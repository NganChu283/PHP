<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->deck_id) || empty($data->title)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing deck_id or title"]);
    exit;
}

$deckId = (int)$data->deck_id;
$title = trim($data->title);

$query = "UPDATE decks SET title=:title WHERE id=:deck_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":title", $title);
$stmt->bindParam(":deck_id", $deckId, PDO::PARAM_INT);

try {
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Deck updated"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Cannot update deck"]);
}
