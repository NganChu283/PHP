<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$limit = isset($_GET['limit']) ? max(1, min((int)$_GET['limit'], 50)) : 10;

$query = "SELECT u.id, u.username, s.xp, s.level, s.quiz_played, s.correct_answers, s.total_answers, s.best_streak
          FROM user_stats s
          INNER JOIN users u ON u.id = s.user_id
          ORDER BY s.xp DESC, s.best_streak DESC, s.updated_at ASC
          LIMIT :limit";
$stmt = $conn->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $idx => $row) {
    $rows[$idx]['rank'] = $idx + 1;
    $total = (int)$row['total_answers'];
    $rows[$idx]['accuracy'] = $total > 0 ? round(((int)$row['correct_answers'] / $total) * 100, 1) : 0;
}

echo json_encode(["status" => "success", "leaderboard" => $rows]);
