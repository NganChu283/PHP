<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

// Keep API backward compatible with older schemas that don't have is_ai_generated.
$checkStmt = $conn->prepare("SELECT COUNT(*) AS c
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'decks'
    AND COLUMN_NAME = 'is_ai_generated'");
$checkStmt->execute();
$hasAiGenerated = ((int)$checkStmt->fetch(PDO::FETCH_ASSOC)['c']) > 0;

if ($hasAiGenerated) {
    $query = "SELECT d.id, d.title, d.user_id, d.is_ai_generated, COUNT(c.id) AS card_count
              FROM decks d
              LEFT JOIN cards c ON c.deck_id = d.id
              GROUP BY d.id
              ORDER BY d.created_at DESC";
} else {
    $query = "SELECT d.id, d.title, d.user_id, 0 AS is_ai_generated, COUNT(c.id) AS card_count
              FROM decks d
              LEFT JOIN cards c ON c.deck_id = d.id
              GROUP BY d.id
              ORDER BY d.created_at DESC";
}

$stmt = $conn->prepare($query);
$stmt->execute();

$decks = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status" => "success", "decks" => $decks]);
