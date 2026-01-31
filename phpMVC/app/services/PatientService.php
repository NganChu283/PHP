
<?php
require_once APP_ROOT . "/app/models/Patient.php";

class PatientService {
    private function getExistingTableName($conn) {
        $tableCandidates = ['patient', 'patients'];

        foreach ($tableCandidates as $candidate) {
            $checkStmt = $conn->prepare("SHOW TABLES LIKE :tableName");
            $checkStmt->execute(['tableName' => $candidate]);
            if ($checkStmt->fetchColumn() !== false) {
                return $candidate;
            }
        }

        return null;
    }

    private function getExistingNameColumn($conn, $tableName) {
        $nameCandidates = ['fullName', 'name'];

        foreach ($nameCandidates as $candidate) {
            $colStmt = $conn->prepare("SHOW COLUMNS FROM {$tableName} LIKE :columnName");
            $colStmt->execute(['columnName' => $candidate]);
            if ($colStmt->fetchColumn() !== false) {
                return $candidate;
            }
        }

        return null;
    }

    public function getAllPatients () {
        $patients = [];

        
        $dbConnection = new DBConnection("localhost", "root", "", "testdb");

        if ($dbConnection != null) {
            $conn = $dbConnection->getConnection();

            if($conn != null) {
                try {
                    $tableName = $this->getExistingTableName($conn);

                    if ($tableName === null) {
                        return $patients;
                    }

                    $sql = "select * from {$tableName}";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute();

                    while($row = $stmt->fetch()) {
                        $fullName = $row['fullName'] ?? ($row['name'] ?? '');
                        $gender = $row['gender'] ?? '';
                        $patient = new Patient($row['id'], $fullName, $gender);
                        $patients[] = $patient;
                    }
                } catch (PDOException $e) {
                    return $patients;
                }

                return $patients;
            }
        }

        return $patients;
    }

    public function addPatient($patient) {
        $dbConnection = new DBConnection("localhost", "root", "", "testdb");
        if ($dbConnection != null) {
            $conn = $dbConnection->getConnection();

            if($conn != null) {
               try {
                    $tableName = $this->getExistingTableName($conn);

                    if ($tableName === null) {
                        return false;
                    }

                    $nameColumn = $this->getExistingNameColumn($conn, $tableName);

                    if ($nameColumn === null) {
                        return false;
                    }

                    $name = $patient->getFullName();
                    $gender = $patient->getGender();
                    $sql = "INSERT INTO {$tableName} ({$nameColumn}, gender) VALUES (:name, :gender)";
                    $stmt = $conn->prepare($sql);

                    return $stmt->execute(['name' => $name, 'gender' => $gender]);
               } catch (PDOException $e) {
                    return false;
               }
            }
        }

        return false;
    }

    public function getPatientById($id) {
        $dbConnection = new DBConnection("localhost", "root", "", "testdb");

        if ($dbConnection == null) {
            return null;
        }

        $conn = $dbConnection->getConnection();

        if ($conn == null) {
            return null;
        }

        try {
            $tableName = $this->getExistingTableName($conn);
            if ($tableName === null) {
                return null;
            }

            $nameColumn = $this->getExistingNameColumn($conn, $tableName);
            if ($nameColumn === null) {
                return null;
            }

            $sql = "SELECT id, {$nameColumn} AS fullName, gender FROM {$tableName} WHERE id = :id LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['id' => $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return new Patient($row['id'], $row['fullName'], $row['gender']);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function updatePatient($patient) {
        $dbConnection = new DBConnection("localhost", "root", "", "testdb");

        if ($dbConnection == null) {
            return false;
        }

        $conn = $dbConnection->getConnection();

        if ($conn == null) {
            return false;
        }

        try {
            $tableName = $this->getExistingTableName($conn);
            if ($tableName === null) {
                return false;
            }

            $nameColumn = $this->getExistingNameColumn($conn, $tableName);
            if ($nameColumn === null) {
                return false;
            }

            $sql = "UPDATE {$tableName} SET {$nameColumn} = :name, gender = :gender WHERE id = :id";
            $stmt = $conn->prepare($sql);

            return $stmt->execute([
                'name' => $patient->getFullName(),
                'gender' => $patient->getGender(),
                'id' => $patient->getId()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deletePatientById($id) {
        $dbConnection = new DBConnection("localhost", "root", "", "testdb");

        if ($dbConnection == null) {
            return false;
        }

        $conn = $dbConnection->getConnection();

        if ($conn == null) {
            return false;
        }

        try {
            $tableName = $this->getExistingTableName($conn);
            if ($tableName === null) {
                return false;
            }

            $sql = "DELETE FROM {$tableName} WHERE id = :id";
            $stmt = $conn->prepare($sql);
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            return false;
        }
    }

}
?>

