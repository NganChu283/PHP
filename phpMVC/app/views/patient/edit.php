<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Sua sinh vien</title>
    <style>
        form {
            width: 40%;
            margin: auto;
        }

        .actions {
            margin-top: 10px;
            display: grid;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h3 class="text-center text-uppercase text-success mt-3 mb-3">Sua sinh vien</h3>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'update_failed') { ?>
            <div class="alert alert-danger" role="alert">Khong the cap nhat sinh vien. Vui long thu lai.</div>
        <?php } ?>

        <form action="<?= DOMAIN . 'public/index.php?controller=patient&action=update' ?>" method="post">
            <div class="mb-3">
                <label for="MaSV" class="form-label">Ma sinh vien</label>
                <input type="text" class="form-control" id="MaSV" name="MaSV" value="<?= htmlspecialchars($patient->getMaSV(), ENT_QUOTES, 'UTF-8') ?>" readonly>
            </div>

            <div class="mb-3">
                <label for="TenSV" class="form-label">Ten sinh vien</label>
                <input type="text" class="form-control" id="TenSV" name="TenSV" value="<?= htmlspecialchars($patient->getTenSV(), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="mb-3">
                <label for="Lop" class="form-label">Lop</label>
                <input type="text" class="form-control" id="Lop" name="Lop" value="<?= htmlspecialchars($patient->getLop(), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="mb-3">
                <label for="Khoa" class="form-label">Khoa</label>
                <input type="text" class="form-control" id="Khoa" name="Khoa" value="<?= htmlspecialchars($patient->getKhoa(), ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-warning" name="btn_update">Cap nhat</button>
                <a href="<?= DOMAIN . 'public/index.php?controller=home' ?>" class="btn btn-secondary">Quay lai</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
