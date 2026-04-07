<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>Them sinh vien</title>
    <style>
        form {
            width: 30%;
            margin: auto;
        }

        button {
            margin-top: 10px;
            width: 100%;   
        }
    </style>

</head>
<body>
    <?php
        if (!defined('DOMAIN')) {
            require_once __DIR__ . '/../../config/config.php';
        }
    ?>
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <div class="container">
        <h3 class="text-center text-uppercase text-success mt-3 mb-3">Them sinh vien</h3>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'save_failed') { ?>
            <div class="alert alert-danger" role="alert">Khong the them sinh vien. Vui long thu lai.</div>
        <?php } ?>
        <form action="<?= DOMAIN . 'public/index.php?controller=patient&action=store' ?>" method="post">
            <div class="mb-3">
                <label for="MaSV" class="form-label">Ma sinh vien</label>
                <input type="text" class="form-control" id="MaSV" name="MaSV" required>
            </div>
            <div class="mb-3">
                <label for="TenSV" class="form-label">Ten sinh vien</label>
                <input type="text" class="form-control" id="TenSV" name="TenSV" required>
            </div>
            <div class="mb-3">
                <label for="Lop" class="form-label"> Lop</label>
                <input type="text" class="form-control" id="Lop" name="Lop" required>
            </div>
             <div class="mb-3">
                <label for="Lop" class="form-label">Khoa</label>
                <input type="text" class="form-control" id="Khoa" name="Khoa" required>
            </div>
            <button type="submit" class="btn btn-primary">Them sinh viên</button>
        </form>
    </div>

</body>
</html>
