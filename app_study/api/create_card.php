
<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->deck_id) || empty($data->question) || empty($data->answer)) {
	http_response_code(400);
	echo json_encode(["status" => "error", "message" => "Missing deck_id, question or answer"]);
	exit;
}

$deck_id = $data->deck_id;
$question = $data->question;
$answer = $data->answer;

$query = "INSERT INTO cards(deck_id,question,answer)
VALUES(:deck_id,:question,:answer)";

$stmt = $conn->prepare($query);

$stmt->bindParam(":deck_id",$deck_id);
$stmt->bindParam(":question",$question);
$stmt->bindParam(":answer",$answer);

try {
	$stmt->execute();
} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(["status" => "error", "message" => "Cannot add card"]);
	exit;
}

echo json_encode(["status" => "success", "message"=>"Card added"]);
