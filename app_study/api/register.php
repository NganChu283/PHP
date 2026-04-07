
<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->username) || empty($data->email) || empty($data->password)) {
	http_response_code(400);
	echo json_encode(["status" => "error", "message" => "Missing required fields"]);
	exit;
}

$username = $data->username;
$email = $data->email;
$password = password_hash($data->password, PASSWORD_BCRYPT);

$query = "INSERT INTO users(username,email,password) VALUES(:username,:email,:password)";
$stmt = $conn->prepare($query);

$stmt->bindParam(":username",$username);
$stmt->bindParam(":email",$email);
$stmt->bindParam(":password",$password);

try {
	$stmt->execute();
	$userId = $conn->lastInsertId();

	$statsQuery = "INSERT INTO user_stats(user_id) VALUES(:user_id)";
	$statsStmt = $conn->prepare($statsQuery);
	$statsStmt->bindParam(":user_id", $userId);
	$statsStmt->execute();

	echo json_encode(["status" => "success", "message" => "Register success", "user_id" => (int)$userId]);
} catch (PDOException $e) {
	http_response_code(400);
	echo json_encode(["status" => "error", "message" => "Email already exists or invalid data"]);
}
