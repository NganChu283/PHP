
<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->email) || empty($data->password)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing email or password"]);
    exit;
}

$email = $data->email;
$password = $data->password;

$query = "SELECT * FROM users WHERE email=:email";
$stmt = $conn->prepare($query);
$stmt->bindParam(":email",$email);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password,$user['password'])){
    $statsQuery = "SELECT xp, level, quiz_played, correct_answers, total_answers, current_streak, best_streak FROM user_stats WHERE user_id=:user_id";
    $statsStmt = $conn->prepare($statsQuery);
    $statsStmt->bindParam(":user_id", $user['id']);
    $statsStmt->execute();
    $stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

    unset($user['password']);

    echo json_encode(["status"=>"success","user"=>$user, "stats" => $stats]);
}else{
    http_response_code(401);
    echo json_encode(["status"=>"error", "message" => "Invalid credentials"]);
}
