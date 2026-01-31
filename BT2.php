<!DOCTYPE html>
<html lang="en">
<body>
    <?php
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    const DB_DSN = 'mysql:host=localhost;dbname=testbai';
    const DB_USER = 'root';
    const DB_PASSWORD = '';
    ?>
    <h1>Lap trinh csdl voi php </h1>
    <!-- <?php
        echo"Cau lenh select:"."<br>";
        $connnect=mysqli_connect("localhost","root","","testbai") or die ("khong the ket noi csdl");
        mysqli_set_charset($connnect,"utf8");
        $query="SELECT * FROM member";
        $result=mysqli_query($connnect, $query);
        if (mysqli_num_rows($result)> 0) {   
            while ($row = mysqli_fetch_assoc($result)) {
                echo"username:".$row['username']."<br>";
                echo"password:".$row['password']."<br>";
                echo "id".$row["id"]."<br>";
            
            }
        }
        mysqli_close($connnect);
    ?>    -->

    <?php
        echo"Cau lenh insert:"."<br>";
        function insertSP () {
            
        }
        $connnect=mysqli_connect("localhost","root","","qlsp") or die ("khong the ket noi csdl");
        mysqli_set_charset($connnect,"utf8");
        $query="INSERT INTO sanpham (ma,tenSP,mota,donViTinh) VALUES ('Ma04','may giat Panasonic','giat quan ao dang cap',100)";
        if (mysqli_query($connnect, $query)) {
            echo"them du lieu thanh cong"."<br>";
        } else {
            echo"them du lieu that bai:".mysqli_error($connnect)."<br>";
        }
        mysqli_close($connnect);
    ?> 

     <!-- <?php
        echo"Cau lenh update:"."<br>";
        $connnect=mysqli_connect("localhost","root","","testbai") or die ("khong the ket noi csdl");
        mysqli_set_charset($connnect,"utf8");
        $query= "Update member set phone = '08995643' where id=4";
        if (mysqli_query($connnect, $query)) {
            echo"cap nhat du lieu thanh cong"."<br>";
        } else {
            echo"cap nhat du lieu that bai:".mysqli_error($connnect)."<br>";
        }
        mysqli_close($connnect);
    ?>
    
     <?php
            echo"Cau lenh delete:"."<br>";
            $connnect=mysqli_connect("localhost","root","","testbai") or die ("khong the ket noi csdl");
            mysqli_set_charset($connnect,"utf8");
            $query= "Delete from member where id=10";
            $isDelete =mysqli_query($connnect, $query);
            if ($isDelete) {
                echo"Xoa du lieu thanh cong"."<br>";
            } else {
                echo"xoa du lieu that bai:";
            }
            mysqli_close($connnect); 
    ?>

    <?php
        echo "Cau lenh select pdo"."<br>";
        try {
            $connnect = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
            $sqlselect = "SELECT * FROM member where id = ?";
            $queryselect = $connnect->prepare($sqlselect);
            $id = 5;
            $queryselect->execute([$id]);
            $members = $queryselect->fetchAll(PDO::FETCH_ASSOC);
            foreach ($members as $member) {
                echo "username:".$member['username']."<br>";
                echo "password:".$member['password']."<br>";
            }
            echo "Ket noi thanh cong";
        } catch (PDOException $e) {
            echo "Ket noi that bai: " . $e->getMessage();

        }
        $connnect=null;
    ?> 
   <?php
    echo "Cau lenh insert PDO <br>";
    try {
    $connnect = new PDO(DB_DSN, DB_USER, DB_PASSWORD);
    $sqlinsert = "INSERT INTO member (username, password, phone) VALUES (?, ?, ?)";
    $queryinsert = $connnect->prepare($sqlinsert);
    $queryinsert->execute(['chukimngan', '12345', '023456']);
    echo "Them thanh cong <br>";    
    } catch (PDOException $e) {
        echo "Loi them: " . $e->getMessage();
}
$connnect = null;
?> 


    <?php
        echo "Cau lenh update: "."<br>";
        try {
            $connnect=new PDO(DB_DSN,DB_USER,DB_PASSWORD);
            $sqlupdate="UPDATE member SET phone = ? WHERE id = ?";
            $queryupdate=$connnect->prepare($sqlupdate);
            $queryupdate->execute(['01234567', 5]);
            echo "Cap nhat thanh cong";
        } catch (PDOException $e) {
            echo "Loi cap nhat: " . $e->getMessage();
        }
        $connnect=null;
    ?>
    <?php
        echo "Cau lenh delete: "."<br>";
        try {
            $connnect=new PDO(DB_DSN,DB_USER,DB_PASSWORD);
            $sqldelete="DELETE FROM member WHERE id = ?";
            $querydelete=$connnect->prepare($sqldelete);
            $querydelete->execute([11]);
            echo "Xoa thanh cong";
        } catch (PDOException $e) {
            echo "Loi xoa: " . $e->getMessage();
        } 
        $connnect=null;
    ?>   -->

</body>
</html>