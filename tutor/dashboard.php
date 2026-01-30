<?php
session_start();
require_once "../config/supabase.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['access_token'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- SUPABASE GET FUNCTION ---------- */
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

/* ---------- FETCH PROFILE ---------- */
$profile = supabaseGet("/rest/v1/profiles?id=eq." . $_SESSION['user_id'])[0];
if ($profile['role'] !== 'tutor') {
    die("Unauthorized");
}

/* ---------- FETCH TUTOR NOTES ---------- */
$notes = supabaseGet("/rest/v1/notes?tutor_id=eq." . $_SESSION['user_id'] . "&order=created_at.desc");

$totalNotes = count($notes);
$totalViews = array_sum(array_column($notes, 'view_count'));
?>
<!DOCTYPE html>
<html>
<head>
  <title>Tutor Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background:#f8f9fb; }
    .card-box { border-radius:15px; }
    .badge-subject { background:#f1f3f5; color:#333; }
    .action-icon { cursor:pointer; }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar bg-white shadow-sm px-4">
  <span class="navbar-brand fw-bold">📘 NoteShare</span>
  <div>
    <span class="me-3 fw-semibold"><?= $profile['name'] ?> <span class="badge bg-primary">Tutor</span></span>
    <a href="../auth/logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
  </div>
</nav>

<div class="container my-4">

<!-- HEADER -->
<div class="d-flex justify-content-between align-items-center">
  <div>
    <h2 class="fw-bold">Tutor Dashboard</h2>
    <p class="text-muted">Welcome back, <?= $profile['name'] ?>!</p>
  </div>
  <a href="upload_note.php" class="btn btn-primary">
    ➕ Upload Notes
  </a>
</div>

<!-- STATS -->
<div class="row g-4 my-4">
  <div class="col-md-6">
    <div class="card card-box p-4">
      <h6 class="text-muted">Notes Uploaded</h6>
      <h2><?= $totalNotes ?></h2>
      <p class="text-muted">Total notes you've shared</p>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card card-box p-4">
      <h6 class="text-muted">Total Views</h6>
      <h2><?= number_format($totalViews) ?></h2>
      <p class="text-muted">Combined views across all notes</p>
    </div>
  </div>
</div>

<!-- NOTES TABLE -->
<div class="card card-box p-4">
  <h4 class="mb-3">📄 Your Notes</h4>

  <table class="table align-middle">
    <thead>
      <tr>
        <th>Title</th>
        <th>Subject</th>
        <th>Views</th>
        <th>Created</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$notes): ?>
        <tr>
          <td colspan="5" class="text-center text-muted">No notes uploaded yet</td>
        </tr>
      <?php endif; ?>

      <?php foreach ($notes as $n): ?>
      <tr>
        <td>
          <strong><?= $n['title'] ?></strong>
          <div class="text-muted small">
            <?= substr($n['description'], 0, 80) ?>...
          </div>
        </td>
        <td>
          <span class="badge badge-subject"><?= $n['category'] ?></span>
        </td>
        <td>👁 <?= $n['view_count'] ?></td>
        <td><?= date('d/m/Y', strtotime($n['created_at'])) ?></td>
        <td>
            <a href="../view/view_note.php?id=<?= $n['id'] ?>" target="_blank" class="me-2">👁</a>
          <a href="edit_note.php?id=<?= $n['id'] ?>" class="me-2 action-icon">✏️</a>
          <a href="delete_note.php?id=<?= $n['id'] ?>" 
             onclick="return confirm('Delete this note?')" 
             class="text-danger action-icon">🗑</a>
        </td>

      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</div>

</body>
</html>
