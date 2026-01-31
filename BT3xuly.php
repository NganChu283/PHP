<?php 
    if (isset($_POST['functionname'])) {
        $functionname = $_POST['functionname'];

        $aResult = "null";
        if ($functionname == "TestAjax") {
            $TenLop = $_POST['tenLop'];
            $MaLop = $_POST['maLop'];
            $connnect=mysqli_connect("localhost","root","","sinhvien") or die ("khong the ket noi csdl");
            $query="INSERT INTO member (TenLop, MaLop) VALUES ('$TenLop', '$MaLop')";
            if (mysqli_query($connnect, $query)) {
            echo"them du lieu thanh cong"."<br>";
            } else {
            echo"them du lieu that bai:".mysqli_error($connnect)."<br>";
            }
            mysqli_close($connnect);
            $aResult = $TenLop . " " . $MaLop;

        }
        echo $aResult;
       
    }
?>