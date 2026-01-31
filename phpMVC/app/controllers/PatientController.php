<?php
    require_once APP_ROOT . "/app/services/PatientService.php";

    class PatientController {
        public function add (){
            include_once APP_ROOT . "/app/views/patient/add.php";

        }
        public function store() {
            if (!isset($_POST['name']) || !isset($_POST['gender'])) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add');
                exit;
            }

            $name = trim($_POST['name']);
            $gender = trim($_POST['gender']);

            if ($name === '' || $gender === '') {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add&error=save_failed');
                exit;
            }

            if (strcasecmp($gender, 'Male') === 0) {
                $gender = '0';
            } else if (strcasecmp($gender, 'Female') === 0) {
                $gender = '1';
            }

            $patient = new Patient(null, $name, $gender);

            $patientService = new PatientService();
            $saved = $patientService->addPatient($patient);

            if (!$saved) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add&error=save_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home');
            exit;
        }

        public function edit($id) {
            if (!isset($id)) {
                echo "ID khong hop le";
                return;
            }

            $patientService = new PatientService();
            $patient = $patientService->getPatientById($id);

            if ($patient === null) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=patient_not_found');
                exit;
            }

            include_once APP_ROOT . "/app/views/patient/edit.php";
        }

        public function update() {
            if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['gender'])) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=update_failed');
                exit;
            }

            $id = (int) $_POST['id'];
            $name = trim($_POST['name']);
            $gender = trim($_POST['gender']);

            if (strcasecmp($gender, 'Male') === 0) {
                $gender = '0';
            } else if (strcasecmp($gender, 'Female') === 0) {
                $gender = '1';
            }

            if ($id <= 0 || $name === '' || ($gender !== '0' && $gender !== '1')) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=edit&id=' . $id . '&error=update_failed');
                exit;
            }

            $patient = new Patient($id, $name, $gender);
            $patientService = new PatientService();
            $updated = $patientService->updatePatient($patient);

            if (!$updated) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=edit&id=' . $id . '&error=update_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home&success=updated');
            exit;
        }

        public function delete($id) {
            if (!isset($id)) {
                echo "ID khong hop le";
                return;
            }

            $patientService = new PatientService();
            $patient = $patientService->getPatientById($id);

            if ($patient === null) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=patient_not_found');
                exit;
            }

            include_once APP_ROOT . "/app/views/patient/delete.php";
        }

        public function destroy() {
            if (!isset($_POST['id'])) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=delete_failed');
                exit;
            }

            $id = (int) $_POST['id'];
            if ($id <= 0) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=delete_failed');
                exit;
            }

            $patientService = new PatientService();
            $deleted = $patientService->deletePatientById($id);

            if (!$deleted) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=delete&id=' . $id . '&error=delete_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home&success=deleted');
            exit;
        }

        public function getPatientById($id) {
            $dbConnection = new DBConnection("localhost", "root", "", "testdb");

            if ($dbConnection != null) {
                $conn = $dbConnection->getConnection();

                if($conn != null) {
                     $sql = "select * from patient where id = :id";
                    $stmt = $conn->prepare($sql);
                    if($stmt->rowCount() > 0) {
                        $stmt->execute(['id' => $id]);
                        $row = $stmt->fetch();
                        $patient = new Patient($row['id'], $row['name'], $row['gender']);
                    }
                }
            }

        }

       
    }
?>