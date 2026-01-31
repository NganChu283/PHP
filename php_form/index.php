<?php
    session_start();
    if (!isset($_SESSION['username'])) {
        header('Location: login.php');
        
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
</head>
<body>
    Chuc mung ban co username la: <?php echo $_SESSION['username']; ?> da dang nhap thanh cong! <br>
</body>
</html>