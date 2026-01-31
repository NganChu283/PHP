<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quan ly benh nhan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

</head>
<body>
    <div class ="Container">
        <h3 class="text-center text-uppercase text-success mt-3 mb-3">Quan ly benh nhan</h3>
        <a href="<?= DOMAIN."public/index.php?controller=patient&action=add" ?>" class="btn btn-primary mb-3">Them benh nhan</a> 
        <table class="table">
        <thead>
            <tr>
            <th scope="col">ID</th>
            <th scope="col">Full Name</th>
            <th scope="col">Gender</th>
            <th scope="col">Sua</th>
            <th scope="col">Xoa</th>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach ($patients as $patient) {
                  
            ?>
        
                <tr>
                <th scope="row"><?php echo $patient->getId(); ?></th>
                <td><?php echo $patient->getFullName(); ?></td>
                <td><?php echo $patient->getGender(); ?></td>
                <td>
                    <a href="<?= DOMAIN . 'public/index.php?controller=patient&action=edit&id=' . $patient->getId() ?>" class="btn btn-warning btn-sm">Sua</a>
                    
                </td>
                <td>
                    <a href="<?= DOMAIN . 'public/index.php?controller=patient&action=delete&id=' . $patient->getId() ?>" class="btn btn-danger btn-sm">Xoa</a>
                    
                </td>
                </tr>
            <?php
                }
            ?>
        </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>



</body>
</html>