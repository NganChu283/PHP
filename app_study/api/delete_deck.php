<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->deck_id)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing deck_id"]);
    exit;
}

$deckId = (int)$data->deck_id;

$query = "DELETE FROM decks WHERE id=:deck_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":deck_id", $deckId, PDO::PARAM_INT);

try {
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Deck deleted"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Cannot delete deck"]);
}
