<?php
require __DIR__ . '/../app/config/config.php';
require APP_ROOT . '/app/libs/DBConnection.php';

$db = new DBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn = $db->getConnection();

if (!$conn) {
    echo "NO_CONN\n";
    exit;
}

$found = false;
foreach (array('patient', 'patients') as $table) {
    $stmt = $conn->prepare('SHOW TABLES LIKE :tableName');
    $stmt->execute(array('tableName' => $table));
    if ($stmt->fetchColumn() !== false) {
        $found = true;
        echo "TABLE={$table}\n";
        $cols = $conn->query("SHOW COLUMNS FROM {$table}");
        foreach ($cols as $col) {
            $def = is_null($col['Default']) ? 'NULL' : $col['Default'];
            echo $col['Field'] . '|' . $col['Type'] . '|' . $col['Null'] . '|' . $def . "\n";
        }
        break;
    }
}

if (!$found) {
    echo "NO_TABLE\n";
}
