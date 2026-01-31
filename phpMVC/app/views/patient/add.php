<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <title>Them benh nhan</title>
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
        <h3 class="text-center text-uppercase text-success mt-3 mb-3">Them benh nhan</h3>
        <?php if (isset($_GET['error']) && $_GET['error'] === 'save_failed') { ?>
            <div class="alert alert-danger" role="alert">Khong the them benh nhan. Vui long thu lai.</div>
        <?php } ?>
        <form action="<?= DOMAIN . 'public/index.php?controller=patient&action=store' ?>" method="post">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label"> Gender</label>
                <select name="gender" id="gender" class="form-control" required>
                    <option value="">Select Gender</option>
                    <option value="0">0</option>
                    <option value="1">1</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Patient</button>
        </form>
    </div>

</body>
</html>
