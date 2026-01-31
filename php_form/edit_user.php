<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editing User</title>
      
    <style>
    body {
    background: #f2f2f2;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* Form box */
.form {
    width: 400px;
}

/* Fieldset */
fieldset {
    background: #ffffff;
    padding: 25px 30px;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

/* Legend */
legend {
    font-weight: bold;
    padding: 0 10px;
}

/* Table (dùng cho login) */
table {
    width: 100%;
}

td {
    padding: 8px 5px;
}

/* Input chung */
input[type="text"],
input[type="password"],
input[type="email"] {
    width: 100%;
    padding: 7px 8px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

/* Focus */
input:focus {
    border-color: #4CAF50;
    outline: none;
}

/* Button */
input[type="submit"] {
    width: 100%;
    padding: 8px;
    background: #4CAF50;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-weight: bold;
}

input[type="submit"]:hover {
    background: #156b19;
}
</style>
</head>
<body>
<?php
    include_once('connect.php');
    $id = $_GET['id'];
    $query= mysqli_query($conn, "SELECT * FROM member WHERE id='$id'");
    $row = mysqli_fetch_assoc($query);
?>
<form action="" class="form" method="POST">
    <h2>Editing User</h2>
    <label for="">username: <input type="text" value="<?php echo $row['username']; ?>" name="username"></label><br>
    <label for="">email: <input type="text" value="<?php echo $row['email']; ?>" name="email"></label><br>
    <label for="">phone: <input type="text" value="<?php echo $row['phone']; ?>" name="phone"></label><br>
    <input type="submit" value="Update" name="update_user">

    <?php
    if(isset($_POST['update_user'])) {
        $id = $_GET['id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];

        $conn = new mysqli('localhost', 'root', '', 'testdb');
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        $sql = "UPDATE member SET username='$username', email='$email', phone='$phone' WHERE id='$id'";
        if ($conn-> query($sql) === TRUE ) {
            echo "Record updated successfully";
        } else {
            echo "Error updating record: " . $conn->error;
        }
        $conn->close();
    }
?>


</form>
    
</body>
</html>