<!DOCTYPE html>
<html>
    <body>
        <h1>Hoc lap trinh PHP can ban</h1>
        <?php
            echo "<hr>";
        ?>
        <p>Tai lieu hoc HTML</p>
        <p>Tai lieu hoc CSS</p>
        

        <?php
            echo "<h2>Tai lieu hoc PHP</h2>";
            echo "<h2>Tai lieu hoc My SQL</h2>";
            echo "<h3>Tai lieu hoc JavaScript</h3>";
        ?>
        <hr>

        <?php
            $text = "Tu co ban ". " den nang cao";
            echo $text;
        ?>
        <br>
        <?php
           $text = "O Ha Noi";
           echo "Toi la " . " Chu Kim Ngan " . " sinh nam " . 2005 . " lam viec " . $text;
        ?>
        <br>
        <?php
           $name = "Chu Kim Ngan";
           $year = 2005;
           echo "<p> Ho va ten: $name </p>";
           echo "<p>Gio tinh cua $name la: Nu </p>";
           $new_year = $year + 20;
           echo"<p>Nam sinh cua $name la $new_year </p>";
           echo var_dump($new_year);
           $len = strlen($name);// dem do dai chuoi
           echo "<p> do dai cua chuoi la: $len </p> ";

           $a = str_word_count("HTML");
           $b = str_word_count("HTML CSS");
           echo "<p>So tu cua chuoi HTML la: $a</p>";
           echo "<p>So tu cua chuoi HTML CSS la: $b</p>";
           $c = str_word_count($name);
           echo"<p> So tu cua chuoi $name la: $c </p>";
           print_r(str_word_count($name,1));
           print_r(str_word_count($name,1,"ẽăă"))
        ?>
        <hr>
        <?php
            $number = 70;
            if($number > 50) {
                echo "<p>Tai lieu hoc HTML</p>";
                echo "<p>Tai lieu hoc CSS</p>";
            } else {
                echo "<p>Tai lieu hoc PHP</p>";
                echo "<p>Tai lieu hoc My SQL</p>";
                echo "<p>Tai lieu hoc JavaScript</p>";
            }
        ?>
        <hr>
        <?php
            $day = getdate()["mday"];
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
            for($i =1; $i <=5; $i++) {
                echo "<p>Lap trinh PHP $i</p>";
            }
        ?>

         <?php
            for($i =0; $i < 5; $i++) {
                echo "So: ".($i+1)."<br>";
            }
        ?>
        <hr>
        <style>
            .square {
                width: 50px;
                height: 50px;
                background-color: blue;
                float: left;
                margin: 2px;
            }
        </style>

         <?php
            for($i =0; $i < 5; $i++) {
                for ($j = 0; $j < 10; $j++) {
                    echo "<div class = 'square'></div>";
                }
                echo "<div style ='clear:both'></div>";
            }
        ?>

        <?php
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            for($i=0; $i < count ($mobile); $i++) {
                echo "<p> $mobile[$i] </p>";
            }
        ?>

         <?php
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            foreach($mobile as $value) {
                echo "<p> $value </p><hr>";
            }
        ?>

        <?php
        $i = 1;
        echo "<hr>";
        while ($i <10) {{
            echo "-" .$i . "-";
            $i++;
        }}
        ?>

        <?php
        echo "<hr>";
        $i = 1;
        do {
            echo " " . $i ." ";
            $i--;
        } while ($i > 0);
        
        ?>

        <?php
            echo "<hr>";
            $mobile = array("iPhone", "Samsung", "Oppo", "Nokia", "Xiaomi");
            $i =0;
            while($i < count($mobile)) {
                echo "-" . $mobile[$i] . "-";
                $i++;
            }
        ?>

        <?php
            $luong = array("A" => 1000000, "B" => 2000000, "C" => 3000000);
            echo "<br/> Luong ['C'] = " . $luong["C"];
        ?>

        <?php
            function GioiThieuBanThan () {
                $name = "Chu Kim Ngan";
                $year = 2005;
                echo "<p> Toi ten la $name sinh nam $year </p>";
            }
            GioiThieuBanThan ();
        ?>

        <?php
            function GioiThieuBanThan1 ($name, $year) {
                    echo "<p> Toi ten la $name sinh nam $year </p>";
            }
            GioiThieuBanThan1 ("Le Thi Hoa", 2004);
            GioiThieuBanThan1 ("La Thanh",1990);
            GioiThieuBanThan1 ("Hai Lua",2006);
             
        ?> 

      <?php
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
