<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" type="text/javascript"></script>
</head>
<body>
    <form action="<?php $_PHP_SELF ?>" method="get">
        Ten: <input type="text" name="TenLop"><br>
        Ma Lop:<input type="text" name="MaLop"><br>
        <input type="button"  value="Click me" onclick="showMessage()">
     </form>

     <script>
        function showMessage() {
            alert("Su kien click đã được kích hoạt");
            var ten = document.getElementsByName("TenLop")[0].value;
            var maLop = document.getElementsByName("MaLop")[0].value;
            alert(ten);
            alert(maLop);
        }

        $.ajax ({
            type: "POST",
            url: "BT3xuly.php",
            data: {
                functionname: "TestAjax", TenLop: tenLop, MaLop: maLop;
            }, 
            success: function (result, status, erro) {
                alert(result);
            },
                error: function (req, status, erro) {
                    alert(req + "" + status + "" + erro);
                } 
        });

     </script>

    

</body>
</html>