<?php
    session_start();
    $conn = mysqli_connect("localhost", "root", "", "testdb") or die ("Loi ket noi: "); mysqli_set_charset($conn, "utf8");
    if (isset($_POST["btn_submit"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];

        //lam sach thong tin, xoa bo cac tag html, ki tu dac biet
        //ma nguoi dung co tinh them vao de tan cong sql
        $username = strip_tags($username);
        $username = addslashes($username);
        $password = strip_tags($password);
        $password = addslashes($password);

       if($username == "" || $password =="") {
            echo "username or password is not empty";
       } else {
            $sql = "select * from member where username = '$username'and password = '$password'";
            $query = mysqli_query($conn, $sql);
            $num_rows = mysqli_num_rows($query);
            if ($num_rows == 0) {
                echo "Username or password is incorrect";
            } else {
                //tien hanh luu ten dang nhap vao session de tien xu ly sau nay
                $_SESSION['username'] = $username;
                //thuc thi hanh dong sau khi dang nhap thanh cong, chuyen trang
                header('Location: index.php');
            }
       }
    }
?>