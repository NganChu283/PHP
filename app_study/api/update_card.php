<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->card_id) || empty($data->question) || empty($data->answer)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing card_id, question or answer"]);
    exit;
}

$cardId = (int)$data->card_id;
$question = trim($data->question);
$answer = trim($data->answer);

$query = "UPDATE cards SET question=:question, answer=:answer WHERE id=:card_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(":question", $question);
$stmt->bindParam(":answer", $answer);
$stmt->bindParam(":card_id", $cardId, PDO::PARAM_INT);

try {
    $stmt->execute();
    echo json_encode(["status" => "success", "message" => "Card updated"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Cannot update card"]);
}
