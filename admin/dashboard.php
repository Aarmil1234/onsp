<?php
session_start();
require_once "../config/supabase.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['access_token'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- FETCH COUNTS ---------- */
function supabaseGet($endpoint) {
    $ch = curl_init(SUPABASE_URL . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: " . SUPABASE_ANON_KEY,
            "Authorization: Bearer " . $_SESSION['access_token']
        ]
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

$tutors   = supabaseGet("/rest/v1/profiles?role=eq.tutor");
$students = supabaseGet("/rest/v1/profiles?role=eq.student");
$notes    = supabaseGet("/rest/v1/notes?select=id,view_count");

$totalViews = array_sum(array_column($notes, 'view_count'));
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fb; }
    .card-box { border-radius:15px; }
    .badge-active { background:#28a745; }
    .badge-inactive { background:#dc3545; }
  </style>
</head>
<body>

<nav class="navbar bg-white shadow-sm px-4">
  <span class="navbar-brand fw-bold">📘 NoteShare</span>
  <div>
    <span class="me-3 fw-semibold">Admin</span>
    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container my-4">

<h2 class="fw-bold">Admin Dashboard</h2>
<p class="text-muted">Manage tutors, students, and platform activity</p>

<!-- STATS -->
<div class="row g-4 my-3">
  <div class="col-md-3">
    <div class="card card-box p-3">
      <h6>Total Tutors</h6>
      <h2><?= count($tutors) ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-box p-3">
      <h6>Total Students</h6>
      <h2><?= count($students) ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-box p-3">
      <h6>Total Notes</h6>
      <h2><?= count($notes) ?></h2>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card card-box p-3">
      <h6>Total Views</h6>
      <h2><?= number_format($totalViews) ?></h2>
    </div>
  </div>
</div>

<!-- TUTORS -->
<div class="card card-box p-4 mb-4">
  <div class="d-flex justify-content-between">
    <h4>👨‍🏫 Tutors</h4>
    <a href="create_tutor.php" class="btn btn-primary btn-sm">+ Create Tutor</a>
  </div>

  <table class="table mt-3">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tutors as $t): ?>
      <tr>
        <td><?= $t['name'] ?></td>
        <td><?= $t['email'] ?></td>
        <td>
          <span class="badge <?= $t['status']=='active'?'badge-active':'badge-inactive' ?>">
            <?= ucfirst($t['status']) ?>
          </span>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- STUDENTS -->
<div class="card card-box p-4">
  <h4>🎓 Recent Students</h4>
  <table class="table mt-3">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach (array_slice($students, 0, 5) as $s): ?>
      <tr>
        <td><?= $s['name'] ?></td>
        <td><?= $s['email'] ?></td>
        <td>
          <span class="badge badge-active">Active</span>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

</body>
</html>
