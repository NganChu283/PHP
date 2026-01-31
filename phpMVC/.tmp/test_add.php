<?php
require __DIR__ . '/../app/config/config.php';
require APP_ROOT . '/app/libs/DBConnection.php';
require APP_ROOT . '/app/services/PatientService.php';

$svc = new PatientService();
$p = new Patient(null, 'Debug User', 'Male');
$result = $svc->addPatient($p);
var_export($result);
echo "\n";

$db = new DBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn = $db->getConnection();
$stmt = $conn->query("SELECT id,name,gender FROM patients ORDER BY id DESC LIMIT 3");
foreach($stmt as $r){
    echo $r['id'] . '|' . $r['name'] . '|' . $r['gender'] . "\n";
}
