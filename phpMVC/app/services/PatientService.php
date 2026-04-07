
<?php
require_once APP_ROOT . "/app/models/Patient.php";

class PatientService {
    private function getConn() {
        $dbConnection = new DBConnection(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        return $dbConnection->getConnection();
    }

    public function getAllPatients () {
        $patients = [];

        $conn = $this->getConn();

        if($conn != null) {
            try {
                $sql = "SELECT MaSV, TenSV, Lop, Khoa FROM sinhvien ORDER BY MaSV ASC";
                $stmt = $conn->prepare($sql);
                $stmt->execute();

                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $patients[] = new Patient($row['MaSV'], $row['TenSV'], $row['Lop'], $row['Khoa']);
                }
            } catch (PDOException $e) {
                return $patients;
            }
        }

        return $patients;
    }

    public function addPatient($patient) {
        $conn = $this->getConn();
        if($conn != null) {
            try {
                $sql = "INSERT INTO sinhvien (MaSV, TenSV, Lop, Khoa) VALUES (:MaSV, :TenSV, :Lop, :Khoa)";
                $stmt = $conn->prepare($sql);

                return $stmt->execute([
                    'MaSV' => $patient->getMaSV(),
                    'TenSV' => $patient->getTenSV(),
                    'Lop' => $patient->getLop(),
                    'Khoa' => $patient->getKhoa()
                ]);
            } catch (PDOException $e) {
                return false;
            }
        }

        return false;
    }

    public function getPatientById($MaSV) {
        $conn = $this->getConn();

        if ($conn == null) {
            return null;
        }

        try {
            $sql = "SELECT MaSV, TenSV, Lop, Khoa FROM sinhvien WHERE MaSV = :MaSV LIMIT 1";
            $stmt = $conn->prepare($sql);
            $stmt->execute(['MaSV' => $MaSV]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                return null;
            }

            return new Patient($row['MaSV'], $row['TenSV'], $row['Lop'], $row['Khoa']);
        } catch (PDOException $e) {
            return null;
        }
    }

    public function updatePatient($patient) {
        $conn = $this->getConn();

        if ($conn == null) {
            return false;
        }

        try {
            $sql = "UPDATE sinhvien SET TenSV = :TenSV, Lop = :Lop, Khoa = :Khoa WHERE MaSV = :MaSV";
            $stmt = $conn->prepare($sql);

            return $stmt->execute([
                'TenSV' => $patient->getTenSV(),
                'Lop' => $patient->getLop(),
                'Khoa' => $patient->getKhoa(),
                'MaSV' => $patient->getMaSV()
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function deletePatientById($MaSV) {
        $conn = $this->getConn();

        if ($conn == null) {
            return false;
        }

        try {
            $sql = "DELETE FROM sinhvien WHERE MaSV = :MaSV";
            $stmt = $conn->prepare($sql);
            return $stmt->execute(['MaSV' => $MaSV]);
        } catch (PDOException $e) {
            return false;
        }
    }

}
?>

