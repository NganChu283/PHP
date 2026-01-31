<?php
require __DIR__ . '/../app/config/config.php';
require APP_ROOT . '/app/libs/DBConnection.php';
$db = new DBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn = $db->getConnection();
$row = $conn->query("SELECT id,name,gender FROM patients ORDER BY id DESC LIMIT 1")->fetch(PDO::FETCH_ASSOC);
if ($row) { echo $row['id'].'|'.$row['name'].'|'.$row['gender']."\n"; }
