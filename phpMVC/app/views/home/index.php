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
        <h3 class="text-center text-uppercase text-success mt-3 mb-3">Quan ly sinh vien</h3>

        <?php if (isset($_GET['success']) && $_GET['success'] === 'updated') { ?>
            <div class="alert alert-success" role="alert">Cap nhat sinh vien thanh cong.</div>
        <?php } ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'patient_not_found') { ?>
            <div class="alert alert-warning" role="alert">Khong tim thay sinh vien voi MaSV da chon.</div>
        <?php } ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'update_failed') { ?>
            <div class="alert alert-danger" role="alert">Cap nhat sinh vien that bai. Vui long thu lai.</div>
        <?php } ?>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'delete_failed') { ?>
            <div class="alert alert-danger" role="alert">Xoa sinh vien that bai. Vui long thu lai.</div>
        <?php } ?>

        <a href="<?= DOMAIN."public/index.php?controller=patient&action=add" ?>" class="btn btn-primary mb-3">Them sinh vien</a> 
        <table class="table">
        <thead>
            <tr>
            <th scope="col">MaSV</th>
            <th scope="col">TenSV</th>
            <th scope="col">Lop</th>
            <th scope="col">Khoa</th>
            <th scope="col">Sua</th>
            <th scope="col">Xoa</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($patients)) { ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Chua co du lieu sinh vien trong bang sinhvien.</td>
                </tr>
            <?php } ?>

            <?php
                foreach ($patients as $patient) {
                  
            ?>
        
                <tr>
                <th scope="row"><?php echo $patient->getMaSV(); ?></th>
                <td><?php echo $patient->getTenSV(); ?></td>
                <td><?php echo $patient->getLop(); ?></td>
                <td><?php echo $patient->getKhoa(); ?></td>
                <td>
                    <a href="<?= DOMAIN . 'public/index.php?controller=patient&action=edit&MaSV=' . $patient->getMaSV() ?>" class="btn btn-warning btn-sm">Sua</a>
                    
                </td>
                <td>
                    <a href="<?= DOMAIN . 'public/index.php?controller=patient&action=delete&MaSV=' . $patient->getMaSV() ?>" class="btn btn-danger btn-sm">Xoa</a>
                    
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