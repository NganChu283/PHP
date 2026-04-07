
<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->title)) {
	http_response_code(400);
	echo json_encode(["status" => "error", "message" => "Missing title"]);
	exit;
}

$title = $data->title;
$user_id = isset($data->user_id) ? (int)$data->user_id : null;

if ($user_id !== null && $user_id > 0) {
	$checkUserStmt = $conn->prepare("SELECT id FROM users WHERE id = :user_id LIMIT 1");
	$checkUserStmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
	$checkUserStmt->execute();
	$exists = $checkUserStmt->fetch(PDO::FETCH_ASSOC);
	if (!$exists) {
		$user_id = null;
	}
} else {
	$user_id = null;
}

$query = "INSERT INTO decks(title,user_id) VALUES(:title,:user_id)";
$stmt = $conn->prepare($query);

$stmt->bindParam(":title",$title);

if ($user_id === null) {
	$stmt->bindValue(":user_id", null, PDO::PARAM_NULL);
} else {
	$stmt->bindValue(":user_id", $user_id, PDO::PARAM_INT);
}

try {
	$stmt->execute();
} catch (PDOException $e) {
	http_response_code(500);
	echo json_encode(["status" => "error", "message" => "Cannot create deck"]);
	exit;
}

$deckId = (int)$conn->lastInsertId();

echo json_encode(["status" => "success", "message"=>"Deck created", "deck_id" => $deckId]);
