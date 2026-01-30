<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-4">
      <div class="card shadow">
        <div class="card-body">
          <h4 class="text-center mb-3">Login</h4>

          <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
              <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
          <?php endif; ?>

          <form method="POST" action="login_action.php">
            <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button class="btn btn-primary w-100">Login</button>
          </form>

        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
