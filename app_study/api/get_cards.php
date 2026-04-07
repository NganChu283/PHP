
<?php
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$deck_id = isset($_GET['deck_id']) ? (int)$_GET['deck_id'] : 1;

$query = "SELECT * FROM cards WHERE deck_id=:deck_id";
$stmt = $conn->prepare($query);

$stmt->bindParam(":deck_id",$deck_id);
$stmt->execute();

$cards = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($cards);
