<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Xoa sinh vien</title>
    <style>
        .confirm-card {
            max-width: 520px;
            margin: 40px auto;
        }

        .student-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="confirm-card card">
            <div class="card-body">
                <h5 class="card-title text-danger">Xoa sinh vien</h5>
                <p class="card-text">Ban co chac chan muon xoa sinh vien nay khong?</p>

                <div class="student-info mb-3">
                    <p><strong>MaSV:</strong> <?= htmlspecialchars($patient->getMaSV(), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>TenSV:</strong> <?= htmlspecialchars($patient->getTenSV(), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Lop:</strong> <?= htmlspecialchars($patient->getLop(), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Khoa:</strong> <?= htmlspecialchars($patient->getKhoa(), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
            <div class="card-footer">
                <form action="<?= DOMAIN . 'public/index.php?controller=patient&action=destroy' ?>" method="post" style="display: inline-block;">
                    <input type="hidden" name="MaSV" value="<?= htmlspecialchars($patient->getMaSV(), ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-danger">Xoa</button>
                </form>
                <a href="<?= DOMAIN . 'public/index.php?controller=home' ?>" class="btn btn-secondary">Huy</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
