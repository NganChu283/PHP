<?php
    require_once APP_ROOT . "/app/services/PatientService.php";

    class PatientController {
        public function add (){
            include_once APP_ROOT . "/app/views/patient/add.php";

        }
        public function store() {
            if (!isset($_POST['MaSV']) || !isset($_POST['TenSV']) || !isset($_POST['Lop']) || !isset($_POST['Khoa'])) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add');
                exit;
            }

            $MaSV = trim($_POST['MaSV']);
            $TenSV = trim($_POST['TenSV']);
            $Lop = trim($_POST['Lop'] );
            $Khoa = trim($_POST['Khoa']);

            if ($MaSV === '' || $TenSV === '') {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add&error=save_failed');
                exit;
            }


            $patient = new Patient($MaSV, $TenSV, $Lop, $Khoa);

            $patientService = new PatientService();
            $saved = $patientService->addPatient($patient);

            if (!$saved) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=add&error=save_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home');
            exit;
        }

        public function edit($MaSV) {
            if (!isset($MaSV)) {
                echo "Ma SV khong hop le";
                return;
            }

            $patientService = new PatientService();
            $patient = $patientService->getPatientById($MaSV);

            if ($patient === null) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=patient_not_found');
                exit;
            }

            include_once APP_ROOT . "/app/views/patient/edit.php";
        }

        public function update() {
            if (!isset($_POST['MaSV']) || !isset($_POST['TenSV']) || !isset($_POST['Lop']) || !isset($_POST['Khoa']) ) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=update_failed');
                exit;
            }

            $MaSV = trim($_POST['MaSV']);
            $TenSV = trim($_POST['TenSV']);
            $Lop = trim($_POST['Lop']);
            $Khoa = trim($_POST['Khoa']);


            if ($MaSV === '' || $TenSV === '' || $Lop === '' || $Khoa === '') {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=edit&MaSV=' . urlencode($MaSV) . '&error=update_failed');
                exit;
            }

            $patient = new Patient($MaSV, $TenSV, $Lop, $Khoa);
            $patientService = new PatientService();
            $updated = $patientService->updatePatient($patient);

            if (!$updated) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=edit&MaSV=' . urlencode($MaSV) . '&error=update_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home&success=updated');
            exit;
        }

        public function delete($MaSV) {
            if (!isset($MaSV) || $MaSV === '') {
                echo "MaSV khong hop le";
                return;
            }

            $patientService = new PatientService();
            $patient = $patientService->getPatientById($MaSV);

            if ($patient === null) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=patient_not_found');
                exit;
            }

            include_once APP_ROOT . "/app/views/patient/delete.php";
        }

        public function destroy() {
            if (!isset($_POST['MaSV'])) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=delete_failed');
                exit;
            }

            $MaSV = trim($_POST['MaSV']);
            if ($MaSV === '') {
                header('Location: ' . DOMAIN . 'public/index.php?controller=home&error=delete_failed');
                exit;
            }

            $patientService = new PatientService();
            $deleted = $patientService->deletePatientById($MaSV);

            if (!$deleted) {
                header('Location: ' . DOMAIN . 'public/index.php?controller=patient&action=delete&MaSV=' . urlencode($MaSV) . '&error=delete_failed');
                exit;
            }

            header('Location: ' . DOMAIN . 'public/index.php?controller=home&success=deleted');
            exit;
        }

    }
?>