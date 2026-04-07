<?php

    require_once("../app/config/config.php");
     require_once APP_ROOT ."/app/libs/DbConnection.php";
    require_once APP_ROOT ."/app/controllers/HomeController.php";
    require_once APP_ROOT ."/app/controllers/PatientController.php";

    $controller = isset($_GET['controller']) ? $_GET['controller'] : "home";
    $action = isset($_GET['action']) ? $_GET['action'] : "index";
     $MaSV = isset($_GET['MaSV']) ? $_GET['MaSV'] : null;
    $homeController = new HomeController();
    $patientController = new PatientController();


   if($controller == 'home'){
        $homeController->index();
   } else if ($controller == 'patient' && $action == 'add') {
        $patientController->add();
   } else if ($controller == 'patient' && $action == 'store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $patientController->store();
   } else if ($controller == 'patient' && $action == 'edit' && $MaSV != null) {
         $patientController->edit($MaSV);
   } else if ($controller == 'patient' && $action == 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $patientController->update();
   } else if ($controller == 'patient' && $action == 'delete' && $MaSV != null) {
        $patientController->delete($MaSV);
   } else if ($controller == 'patient' && $action == 'destroy' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $patientController->destroy();

   } else {
        echo "Nothing";
   }
?>