<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<title>Xoa benh nhan</title>
	<style>
		.confirm-card {
			max-width: 520px;
			margin: 40px auto;
		}

		.patient-info {
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
                <h5 class="card-title text-danger">Xoa benh nhan</h5>
                <p class="card-text">Ban co chac chan muon xoa benh nhan nay khong?</p>

                <div class="patient-info mb-3">
                    <p><strong>ID:</strong> <?= $patient->getId() ?></p>
                    <p><strong>Full Name:</strong> <?= htmlspecialchars($patient->getFullName(), ENT_QUOTES, 'UTF-8') ?></p>
                    <p><strong>Gender:</strong> <?= ($patient->getGender() === '0' || $patient->getGender() === 0) ? 'Male' : 'Female' ?></p>
                </div>
            </div>
            <div class="card-footer">
                <form action="<?= DOMAIN . 'public/index.php?controller=patient&action=destroy' ?>" method="post" style="display: inline-block;">
                    <input type="hidden" name="id" value="<?= $patient->getId() ?>">
                    <button type="submit" class="btn btn-danger">Xoa</button>
                </form>
                <a href="<?= DOMAIN . 'public/index.php?controller=home' ?>" class="btn btn-secondary">Huy</a>
            </div>
        </div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
