<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang dang ky</title>
    <style>
        * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, Helvetica, sans-serif;
}

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
    background: #45a049;
}
    </style>
</head>
<body>
    <form action="register.php" method="post" class="form">
        <h2>Register member</h2>
        Username: <input type="text" name="username" placeholder="Username" required><br>
        Email: <input type="email" name="email" placeholder="Email" required><br>
        Password: <input type="password" name="password" placeholder="Password" required><br>
        Phone: <input type="text" name="phone" placeholder="Phone" required><br>
        <input type="submit" name="dangky" value="Dang ky">
        <?php
            require"resgister_process.php";
        ?>      
    </form>
</body>
</html>