<!DOCTYPE html>
<html>
    <body>
        <h1>Hoc lap trinh PHP can ban</h1>
        <!-- Hiện một đường kẻ ngang bằng PHP -->
        <?php
            // echo dùng để in ra chuỗi HTML/ text
            echo "<hr>";
        ?>
        <p>Tai lieu hoc HTML</p>
        <p>Tai lieu hoc CSS</p>
        

        <?php
            // In ra các tiêu đề khác nhau bằng PHP
            echo "<h2>Tai lieu hoc PHP</h2>";
            echo "<h2>Tai lieu hoc My SQL</h2>";
            echo "<h3>Tai lieu hoc JavaScript</h3>";
        ?>
        <hr>

        <?php
            // Ghép chuỗi bằng toán tử '.'
            $text = "Tu co ban ". " den nang cao";
            echo $text; // Hiển thị: Tu co ban  den nang cao
        ?>
        <br>
        <?php
           // Kết hợp chuỗi và số trực tiếp trong echo
           $text = "O Ha Noi";
           echo "Toi la " . " Chu Kim Ngan " . " sinh nam " . 2005 . " lam viec " . $text;
        ?>
        <br>
        <?php
           // Sử dụng biến và chuỗi được nhúng trong chuỗi đôi
           $name = "Chu Kim Ngan";
           $year = 2005;
           echo "<p> Ho va ten: $name </p>"; // Hiển thị tên
           echo "<p>Gio tinh cua $name la: Nu </p>";

           // Toán tử + với số tăng giá trị biến
           $new_year = $year + 20; // 2005 + 20 = 2025
           echo"<p>Nam sinh cua $name la $new_year </p>";

           // var_dump in ra kiểu và giá trị (dùng cho debug)
           echo var_dump($new_year);

           // Một số hàm xử lý chuỗi cơ bản
           $len = strlen($name); // đếm độ dài chuỗi (số ký tự)
           echo "<p> do dai cua chuoi la: $len </p> ";

           $a = str_word_count("HTML");
           $b = str_word_count("HTML CSS");
           echo "<p>So tu cua chuoi HTML la: $a</p>"; // 1
           echo "<p>So tu cua chuoi HTML CSS la: $b</p>"; // 2
           $c = str_word_count($name);
           echo"<p> So tu cua chuoi $name la: $c </p>";

           // print_r hiển thị mảng
           print_r(str_word_count($name,1));
           // Tham số thứ 3 dùng để chỉ định ký tự mở rộng (nếu cần), ở đây thêm các ký tự tiếng Việt
           print_r(str_word_count($name,1,"ẽăă"));
        ?>
        <hr>
        <?php
            // Cấu trúc điều kiện if..else
            $number = 70;
            if($number > 50) {
                // Nếu đúng thì in HTML liên quan đến HTML/CSS
                echo "<p>Tai lieu hoc HTML</p>";
                echo "<p>Tai lieu hoc CSS</p>";
            } else {
                // Ngược lại in các tài liệu khác
                echo "<p>Tai lieu hoc PHP</p>";
                echo "<p>Tai lieu hoc My SQL</p>";
                echo "<p>Tai lieu hoc JavaScript</p>";
            }
        ?>
        <hr>
        <?php
            // Ví dụ if..elseif..else kiểm tra ngày trong tháng
            $day = getdate()["mday"]; // lấy ngày của tháng
            if($day == 0) {
                echo "Hom nay la chu nhat";
            } elseif ($day == 1) {
                echo "Hom nay la thu hai";
            } elseif ($day == 2) {
                echo "Hom nay la thu ba";
            } elseif ($day == 3) {
                echo "Hom nay la thu tu";
            } elseif ($day == 4) {
                echo "Hom nay la thu nam";
            } elseif ($day == 5) {
                echo "Hom nay la thu sau";
            } else {
                echo "Hom nay la thu bay";
            }
        ?>
        <hr>

        <?php
            // Cấu trúc switch..case (thường dùng khi kiểm tra nhiều giá trị cụ thể)
            $money = 12000;
            switch ($money) {
                case 2000:
                    echo "Tra da";
                    break;
                case 5000:
                    echo "Sting dau";
                    break;
                case 10000:
                    echo "Coca";
                    break;
                case 12000:
                    echo "Pepsi";
                    break;
                }
        ?>
        <hr>
        <?php
            // Ví dụ switch với chuỗi (mùa)
            $season = "Thu";
            switch ($season) {
                case "Xuan":
                    echo "<p>Mua xuan</p>";
                    break;
                case "Ha":
                    echo "<p>Mua ha</p>";
                    break;
                case "Thu":
                    echo "<p>Mua thu</p>";
                    break;
                case "Dong":
                    echo "<p>Mua dong</p>";
                    break;
            }
        ?>

        <?php
            // Vòng lặp for - lặp cố định số lần
            for($i =1; $i <=5; $i++) {
                echo "<p>Lap trinh PHP $i</p>"; // in 5 dòng
            }
        ?>

         <?php
            // Vòng lặp for với in số (chú ý: i chạy từ 0 nên +1 để in 1..5)
            for($i =0; $i < 5; $i++) {
                echo "So: ".($i+1)."<br>";
            }
        ?>
        <hr>
        <style>
            /* CSS để hiển thị các ô vuông*/
            .square {
                width: 50px;
                height: 50px;
                background-color: blue;
                float: left;
                margin: 2px;
            }
        </style>

         <?php
            // In ra lưới 5 hàng x 10 cột ô vuông bằng vòng lặp lồng nhau
            for($i =0; $i < 5; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    echo "<div class = 'square'></div>";
                }
                // Dùng clear để xuống hàng mới
                echo "<div style ='clear:both'></div>";
            }
        ?>

        <?php
            // Mảng tuần tự (indexed array) và dùng for để duyệt
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            for($i=0; $i < count ($mobile); $i++) {
                echo "<p> $mobile[$i] </p>";
            }
        ?>

         <?php
            // Dùng foreach để duyệt mảng dễ hơn
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            foreach($mobile as $value) {
                echo "<p> $value </p><hr>";
            }
        ?>

        <?php
        // Vòng lặp while: lặp khi điều kiện đúng
        $i = 1;
        echo "<hr>";
        while ($i <10) {{
            // Lưu ý: cú pháp có thêm ngoặc nhọn kép ở đây, nhưng PHP vẫn chạy (không cần thiết)
            echo "-" .$i . "-";
            $i++;
        }}
        ?>

        <?php
        // Vòng lặp do..while: chạy ít nhất 1 lần
        echo "<hr>";
        $i = 1;
        do {
            echo " " . $i ." ";
            $i--; // Giảm i -> sau lần đầu i = 0, vòng sẽ dừng
        } while ($i > 0);
        
        ?>

        <?php
            echo "<hr>";
            // Dùng while để duyệt mảng theo chỉ số
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            $i =0;
            while($i < count($mobile)) {
                echo "-" . $mobile[$i] . "-";
                $i++;
            }
        ?>

        <?php
            // Mảng kết hợp (associative array) - truy cập theo key
            $luong = array("A" => 1000000, "B" => 2000000, "C" => 3000000);
            echo "<br/> Luong ['C'] = " . $luong["C"];
        ?>

        <?php
            // Định nghĩa hàm không có tham số
            function GioiThieuBanThan () {
                $name = "Chu Kim Ngan";
                $year = 2005;
                echo "<p> Toi ten la $name sinh nam $year </p>";
            }
            // Gọi hàm
            GioiThieuBanThan ();
        ?>

        <?php
            // Hàm có tham số: tên và năm sinh
            function GioiThieuBanThan1 ($name, $year) {
                    echo "<p> Toi ten la $name sinh nam $year </p>";
            }
            // Gọi hàm với các tham số khác nhau
            GioiThieuBanThan1 ("Le Thi Hoa", 2004);
            GioiThieuBanThan1 ("La Thanh",1990);
            GioiThieuBanThan1 ("Hai Lua",2006);
             
        ?> 

      <?php
        // Hàm trả về giá trị: tính (a + b)^2 bằng hàm pow
        function number($a, $b) {
            return pow($a + $b, 2);
        }
        $a = 5;
        $b = 7;
        $result = number($a, $b);
        echo "<p> Tong binh phuong của $a va $b la: $result </p>";
      ?>
    </body>

</html>