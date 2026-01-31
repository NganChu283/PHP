<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View_users</title>
    <style> 
        body {
    font-family: Time News Roman, sans-serif;
    font-size: 14px;
    background: #FFFFCC;
    display: flex;
    justify-content: center;
    margin-top: 40px;
}

/* Container */
.container {
    text-align: center;
}

/* Table nhỏ gọn */
table {
    border-collapse: collapse;
    margin: auto;
    width: 500px;   /* bảng nhỏ */
}

/* Viền bảng */
table, th, td {
    border: 0.5px solid black;
}

/* Ô */
th, td {
    padding: 5px 8px;
    text-align: center;
}

/* Header */
th {
    background-color: #e5c7c7;
    font-weight: bold;

}

/* Link */
a {
    text-decoration: none;
    color: blue;
}

a:hover {
    text-decoration: underline;
}
    </style>
</head>


<body>
    <form action="view_users.php" method="get" class="form">
        <h2> Danh sach thanh vien </h2>
        <table border="1">
            <tr>
                <td>ID</td>
                <td>Username</td>
                <td>Email</td>
                <td>Phone</td>
            </tr>
        
        <?php
            $conn = mysqli_connect("localhost", "root", "", "testdb") or die ("Loi ket noi: "); mysqli_set_charset($conn, "utf8");
            $query = mysqli_query($conn, "select * from member");
            while ($row = mysqli_fetch_array($query)) {
        ?>
            <tr>
                <td><?php echo $row["id"]; ?></td>
                <td><?php echo $row["username"]; ?></td>
                <td><?php echo $row["email"]; ?></td>
                <td><?php echo $row["phone"]; ?></td>
                <td><a href ="edit_user.php?id=<?php echo $row['id']; ?>">Edit</a></td>
                <td><a href="delete_user.php?id=<?php echo $row['id']; ?>">Delete</a></td>
            </tr>
        <?php
            }
        ?>
        </table>
    </form>
</body>
</html>
