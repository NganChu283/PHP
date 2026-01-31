<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
	<title>Sua benh nhan</title>
	<style>
		form {
			width: 30%;
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
		<h3 class="text-center text-uppercase text-success mt-3 mb-3">Sua benh nhan</h3>

		<?php if (isset($_GET['error']) && $_GET['error'] === 'update_failed') { ?>
			<div class="alert alert-danger" role="alert">Khong the cap nhat benh nhan. Vui long thu lai.</div>
		<?php } ?>

		<form action="<?= DOMAIN . 'public/index.php?controller=patient&action=update' ?>" method="post">
			<input type="hidden" name="id" value="<?= $patient->getId() ?>">

			<div class="mb-3">
				<label for="name" class="form-label">Full Name</label>
				<input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($patient->getFullName(), ENT_QUOTES, 'UTF-8') ?>" required>
			</div>

			<div class="mb-3">
				<label for="gender" class="form-label">Gender</label>
				<select name="gender" id="gender" class="form-control" required>
					<option value="0" <?= ($patient->getGender() === '0' || $patient->getGender() === 0) ? 'selected' : '' ?>>0</option>
					<option value="1" <?= ($patient->getGender() === '1' || $patient->getGender() === 1) ? 'selected' : '' ?>>1</option>
				</select>
			</div>

			<div class="actions">
				<button type="submit" class="btn btn-warning" name ='btn_update'>Cap nhat</button>
				<a href="<?= DOMAIN . 'public/index.php?controller=home' ?>" class="btn btn-secondary">Quay lai</a>
			</div>
		</form>
	</div>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
