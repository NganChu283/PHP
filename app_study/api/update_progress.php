<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));

if (!$data || empty($data->user_id)) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Missing user_id"]);
    exit;
}

$userId = (int)$data->user_id;
$xpGain = isset($data->xp_gain) ? (int)$data->xp_gain : 0;
$answeredGain = isset($data->answered_gain) ? (int)$data->answered_gain : 0;
$correctGain = isset($data->correct_gain) ? (int)$data->correct_gain : 0;
$currentStreak = isset($data->current_streak) ? (int)$data->current_streak : 0;
$quizPlayedGain = isset($data->quiz_played_gain) ? (int)$data->quiz_played_gain : 0;

$ensureStmt = $conn->prepare("INSERT INTO user_stats (user_id) VALUES (:user_id) ON DUPLICATE KEY UPDATE user_id = user_id");
$ensureStmt->bindParam(':user_id', $userId);
$ensureStmt->execute();

$updateQuery = "UPDATE user_stats
                SET xp = GREATEST(0, xp + :xp_gain),
                    total_answers = GREATEST(0, total_answers + :answered_gain),
                    correct_answers = GREATEST(0, correct_answers + :correct_gain),
                    current_streak = :current_streak,
                    best_streak = GREATEST(best_streak, :current_streak),
                    quiz_played = GREATEST(0, quiz_played + :quiz_played_gain)
                WHERE user_id = :user_id";
$updateStmt = $conn->prepare($updateQuery);
$updateStmt->bindParam(':xp_gain', $xpGain, PDO::PARAM_INT);
$updateStmt->bindParam(':answered_gain', $answeredGain, PDO::PARAM_INT);
$updateStmt->bindParam(':correct_gain', $correctGain, PDO::PARAM_INT);
$updateStmt->bindParam(':current_streak', $currentStreak, PDO::PARAM_INT);
$updateStmt->bindParam(':quiz_played_gain', $quizPlayedGain, PDO::PARAM_INT);
$updateStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$updateStmt->execute();

$levelStmt = $conn->prepare("UPDATE user_stats SET level = FLOOR(xp / 100) + 1 WHERE user_id = :user_id");
$levelStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$levelStmt->execute();

$selectStmt = $conn->prepare("SELECT xp, level, quiz_played, correct_answers, total_answers, current_streak, best_streak FROM user_stats WHERE user_id = :user_id");
$selectStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$selectStmt->execute();
$stats = $selectStmt->fetch(PDO::FETCH_ASSOC);

echo json_encode(["status" => "success", "stats" => $stats]);
