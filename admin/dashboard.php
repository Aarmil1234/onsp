<?php
session_start();
require_once "../config/supabase.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['access_token'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- SUPABASE GET (ADMIN) ---------- */
function supabaseGet($endpoint) {
    $ch = curl_init(SUPABASE_URL . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            "apikey: " . SUPABASE_SERVICE_KEY,
            "Authorization: Bearer " . SUPABASE_SERVICE_KEY
        ]
    ]);
    $res = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($res, true);
    return is_array($data) ? $data : [];
}

/* ---------- FETCH DATA ---------- */
$tutors   = supabaseGet("/rest/v1/profiles?role=eq.tutor");
$students = supabaseGet("/rest/v1/profiles?role=eq.student");
$notes    = supabaseGet("/rest/v1/notes?select=id,view_count");

$totalViews = array_sum(array_column($notes, 'view_count'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - ONSP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    :root {
      --primary: #6366f1;
      --primary-dark: #4f46e5;
      --secondary: #8b5cf6;
      --success: #10b981;
      --warning: #f59e0b;
      --danger: #ef4444;
      --dark: #0f172a;
      --gray: #64748b;
      --light-bg: #f8fafc;
      --border: #e2e8f0;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: var(--light-bg);
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
      color: var(--dark);
    }

    /* NAVBAR */
    .navbar {
      background: white;
      border-bottom: 1px solid var(--border);
      padding: 1.2rem 0;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: 800;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .admin-badge {
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      padding: 0.4rem 1rem;
      border-radius: 10px;
      font-weight: 600;
      font-size: 0.85rem;
    }

    .btn-logout {
      background: transparent;
      border: 2px solid var(--danger);
      color: var(--danger);
      padding: 0.5rem 1.2rem;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s;
    }

    .btn-logout:hover {
      background: var(--danger);
      color: white;
    }

    /* HEADER */
    .page-header {
      margin-bottom: 2rem;
    }

    .page-title {
      font-size: 2rem;
      font-weight: 800;
      color: var(--dark);
      margin-bottom: 0.3rem;
    }

    .page-subtitle {
      color: var(--gray);
      font-size: 1rem;
    }

    /* STAT CARDS */
    .stat-card {
      background: white;
      border-radius: 20px;
      padding: 1.8rem;
      border: 1px solid var(--border);
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 4px;
      height: 100%;
      background: linear-gradient(180deg, var(--primary), var(--secondary));
      opacity: 0;
      transition: opacity 0.3s;
    }

    .stat-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
      border-color: var(--primary);
    }

    .stat-card:hover::before {
      opacity: 1;
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
      margin-bottom: 1rem;
    }

    .stat-icon.purple {
      background: rgba(99, 102, 241, 0.1);
    }

    .stat-icon.green {
      background: rgba(16, 185, 129, 0.1);
    }

    .stat-icon.orange {
      background: rgba(245, 158, 11, 0.1);
    }

    .stat-icon.pink {
      background: rgba(236, 72, 153, 0.1);
    }

    .stat-label {
      color: var(--gray);
      font-size: 0.85rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 0.5rem;
    }

    .stat-value {
      font-size: 2rem;
      font-weight: 800;
      color: var(--dark);
    }

    /* CONTENT CARDS */
    .content-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid var(--border);
      margin-bottom: 2rem;
    }

    .content-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1.5rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--light-bg);
    }

    .content-title {
      font-size: 1.4rem;
      font-weight: 700;
      color: var(--dark);
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-create {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 0.6rem 1.5rem;
      border-radius: 12px;
      border: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-block;
    }

    .btn-create:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
      color: white;
    }

    /* TABLE */
    .custom-table {
      margin-top: 1rem;
    }

    .custom-table thead {
      background: var(--light-bg);
      border-radius: 10px;
    }

    .custom-table thead th {
      padding: 1rem 1.2rem;
      font-weight: 700;
      font-size: 0.85rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: var(--gray);
      border: none;
    }

    .custom-table thead th:first-child {
      border-radius: 10px 0 0 10px;
    }

    .custom-table thead th:last-child {
      border-radius: 0 10px 10px 0;
    }

    .custom-table tbody tr {
      border-bottom: 1px solid var(--border);
      transition: background 0.2s;
    }

    .custom-table tbody tr:hover {
      background: var(--light-bg);
    }

    .custom-table tbody tr:last-child {
      border-bottom: none;
    }

    .custom-table tbody td {
      padding: 1.2rem;
      vertical-align: middle;
      color: var(--dark);
      font-size: 0.95rem;
    }

    .user-name {
      font-weight: 600;
      color: var(--dark);
    }

    .user-email {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .user-avatar {
      width: 38px;
      height: 38px;
      border-radius: 50%;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
      color: white;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 0.9rem;
      margin-right: 0.8rem;
    }

    .user-info {
      display: flex;
      align-items: center;
    }

    .empty-state {
      text-align: center;
      padding: 3rem 2rem;
      color: var(--gray);
    }

    .empty-state-icon {
      font-size: 3rem;
      margin-bottom: 1rem;
      opacity: 0.5;
    }

    .empty-state-text {
      font-size: 1.1rem;
      font-weight: 600;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .page-title {
        font-size: 1.5rem;
      }

      .stat-value {
        font-size: 1.5rem;
      }

      .content-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .btn-create {
        width: 100%;
        text-align: center;
      }

      .custom-table {
        font-size: 0.85rem;
      }

      .custom-table thead th,
      .custom-table tbody td {
        padding: 0.8rem;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between w-100">
      <span class="navbar-brand">ONSP ADMIN</span>
      <div class="d-flex align-items-center gap-3">
        <span class="admin-badge">Admin Panel</span>
        <a href="../auth/logout.php" class="btn btn-logout">Logout</a>
      </div>
    </div>
  </div>
</nav>

<div class="container-fluid px-4 py-4">

  <!-- PAGE HEADER -->
  <div class="page-header">
    <h1 class="page-title">Dashboard Overview</h1>
    <p class="page-subtitle">Manage tutors, students, and platform activity</p>
  </div>

  <!-- STATS GRID -->
  <div class="row g-4 mb-4">
    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon purple">👨‍🏫</div>
        <div class="stat-label">Total Tutors</div>
        <div class="stat-value"><?= count($tutors) ?></div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon green">🎓</div>
        <div class="stat-label">Total Students</div>
        <div class="stat-value"><?= count($students) ?></div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon orange">📄</div>
        <div class="stat-label">Total Notes</div>
        <div class="stat-value"><?= count($notes) ?></div>
      </div>
    </div>

    <div class="col-lg-3 col-md-6">
      <div class="stat-card">
        <div class="stat-icon pink">👁️</div>
        <div class="stat-label">Total Views</div>
        <div class="stat-value"><?= number_format($totalViews) ?></div>
      </div>
    </div>
  </div>

  <!-- TUTORS SECTION -->
  <div class="content-card">
    <div class="content-header">
      <h4 class="content-title">
        <span>👨‍🏫</span>
        <span>All Tutors</span>
      </h4>
      <a href="create_tutor.php" class="btn btn-create">+ Create New Tutor</a>
    </div>

    <?php if (count($tutors) > 0): ?>
    <table class="table custom-table">
      <thead>
        <tr>
          <th>Tutor</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($tutors as $t): ?>
        <tr>
          <td>
            <div class="user-info">
              <div class="user-avatar">
                <?= strtoupper(substr($t['name'] ?? 'T', 0, 1)) ?>
              </div>
              <span class="user-name"><?= htmlspecialchars($t['name'] ?? 'Unknown') ?></span>
            </div>
          </td>
          <td>
            <span class="user-email"><?= htmlspecialchars($t['email'] ?? 'N/A') ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">👨‍🏫</div>
      <div class="empty-state-text">No tutors found</div>
    </div>
    <?php endif; ?>
  </div>

  <!-- STUDENTS SECTION -->
  <div class="content-card">
    <div class="content-header">
      <h4 class="content-title">
        <span>🎓</span>
        <span>Recent Students</span>
      </h4>
    </div>

    <?php if (count($students) > 0): ?>
    <table class="table custom-table">
      <thead>
        <tr>
          <th>Student</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach (array_slice($students, 0, 10) as $s): ?>
        <tr>
          <td>
            <div class="user-info">
              <div class="user-avatar">
                <?= strtoupper(substr($s['name'] ?? 'S', 0, 1)) ?>
              </div>
              <span class="user-name"><?= htmlspecialchars($s['name'] ?? 'Unknown') ?></span>
            </div>
          </td>
          <td>
            <span class="user-email"><?= htmlspecialchars($s['email'] ?? 'N/A') ?></span>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">🎓</div>
      <div class="empty-state-text">No students found</div>
    </div>
    <?php endif; ?>
  </div>

</div>

</body>
</html>