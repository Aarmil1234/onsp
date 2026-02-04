<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Create Tutor</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 col-md-5">
  <div class="card shadow p-4">
    <h4>Create Tutor</h4>

    <form method="POST" action="create_tutor_action.php">
      <input class="form-control mb-3" name="name" placeholder="Tutor Name" required>
      <input class="form-control mb-3" name="email" type="email" placeholder="Email" required>
      <input class="form-control mb-3" name="password" type="password" placeholder="Password" required>

      <button class="btn btn-primary w-100">Create Tutor</button>
      <a href="dashboard.php" class="btn btn-link w-100">Cancel</a>
    </form>
  </div>
</div>

</body>
</html>
