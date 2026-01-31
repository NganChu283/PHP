<?php
  header('Content-Type: text/html; charset=UTF-8');
    //Ket noi csdl
    $conn = mysqli_connect("localhost", "root", "", "testdb") or die ("Loi ket noi: "); mysqli_set_charset($conn, "utf8");
    //Dung isset de kiem tra
    if(isset($_POST["dangky"])){
        $username =trim($_POST["username"]);
        $email =trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $phone = trim($_POST["phone"]);
        //Truy van sql
       if (empty($username)){
        array_push($errors, "Username is required");
       }
       if (empty($email)){
        array_push($errors, "Email is required");
       }
       if (empty($password)){
        array_push($errors, "Password is required");
       } 
       if(empty($phone)) {
        array_push($errors, "Phone is required");
       }

       //Kiem tra user hoac email co bi trung hay khong
       $sql = "select * from member where username = '$username' OR email = '$email'";
        $result = mysqli_query($conn, $sql);
       if (mysqli_num_rows($result) > 0) {
         echo "<script language='javascript'>alert('Username or email already exists');</script>";
         die();
    } else { 
        $sql = "insert into member(username, email, password, phone) values ('$username', '$email', '$password', '$phone')";

        if (mysqli_query($conn, $sql)) {
            echo "Ten dang nhap:" .$_POST["username"]. "<br>";
            echo "Mat khau:" .$_POST["password"]. "<br>";
            echo "Email:" .$_POST["email"]. "<br>";
            echo "Phone:" .$_POST["phone"]. "<br>";
            echo "<script language='javascript'>alert('Dang ky thanh cong'); window.location.href='register.php';</script>";
        }
        else {
            echo "<script language='javascript'>alert('Dang ky that bai'); window.location.href='register.php';</script>";
        }

    }
    }
?>