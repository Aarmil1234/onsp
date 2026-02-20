<?php
session_start();
require_once "../config/supabase.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['access_token'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ---------- SUPABASE GET ---------- */
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Tutor Dashboard - ONSP</title>
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
      --info: #06b6d4;
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

    .tutor-badge {
      background: linear-gradient(135deg, var(--success), #059669);
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

    /* WELCOME SECTION */
    .welcome-section {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      padding: 3rem 0;
      position: relative;
      overflow: hidden;
      margin-bottom: 2rem;
    }

    .welcome-section::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }

    .welcome-content {
      position: relative;
      z-index: 1;
    }

    .welcome-title {
      font-size: 2rem;
      font-weight: 800;
      color: white;
      margin-bottom: 0.5rem;
    }

    .welcome-subtitle {
      color: rgba(255, 255, 255, 0.9);
      font-size: 1.1rem;
      margin-bottom: 2rem;
    }

    .btn-upload {
      background: white;
      color: var(--primary);
      padding: 0.9rem 2rem;
      border-radius: 14px;
      border: none;
      font-weight: 700;
      font-size: 1rem;
      transition: all 0.3s;
      text-decoration: none;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    .btn-upload:hover {
      transform: translateY(-3px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.3);
      color: var(--primary);
    }

    /* STAT CARDS */
    .stat-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid var(--border);
      transition: all 0.3s;
      position: relative;
      overflow: hidden;
      height: 100%;
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

    .stat-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 1rem;
    }

    .stat-icon {
      width: 50px;
      height: 50px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .stat-icon.purple {
      background: rgba(99, 102, 241, 0.1);
    }

    .stat-icon.blue {
      background: rgba(6, 182, 212, 0.1);
    }

    .stat-label {
      color: var(--gray);
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
    }

    .stat-value {
      font-size: 2.2rem;
      font-weight: 800;
      color: var(--dark);
    }

    /* CONTENT CARD */
    .content-card {
      background: white;
      border-radius: 20px;
      padding: 2rem;
      border: 1px solid var(--border);
    }

    .content-header {
      display: flex;
      align-items: center;
      gap: 0.8rem;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 2px solid var(--light-bg);
    }

    .content-title {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--dark);
    }

    /* TABLE */
    .custom-table {
      width: 100%;
    }

    .custom-table thead {
      background: var(--light-bg);
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
      font-size: 0.95rem;
    }

    .note-title-cell {
      font-weight: 600;
      color: var(--dark);
    }

    .note-subject {
      display: inline-block;
      background: var(--warning);
      color: white;
      padding: 0.3rem 0.8rem;
      border-radius: 8px;
      font-size: 0.75rem;
      font-weight: 700;
      text-transform: uppercase;
    }

    .note-views {
      display: flex;
      align-items: center;
      gap: 0.4rem;
      color: var(--gray);
      font-weight: 600;
    }

    .note-date {
      color: var(--gray);
      font-size: 0.9rem;
    }

    .action-buttons {
      display: flex;
      gap: 0.5rem;
    }

    .btn-action {
      width: 36px;
      height: 36px;
      border-radius: 10px;
      border: 1px solid var(--border);
      background: white;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
      text-decoration: none;
      font-size: 1rem;
    }

    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-action.view {
      border-color: var(--info);
      color: var(--info);
    }

    .btn-action.view:hover {
      background: var(--info);
      color: white;
    }

    .btn-action.edit {
      border-color: var(--primary);
      color: var(--primary);
    }

    .btn-action.edit:hover {
      background: var(--primary);
      color: white;
    }

    .btn-action.delete {
      border-color: var(--danger);
      color: var(--danger);
    }

    .btn-action.delete:hover {
      background: var(--danger);
      color: white;
    }

    /* EMPTY STATE */
    .empty-state {
      text-align: center;
      padding: 4rem 2rem;
    }

    .empty-state-icon {
      font-size: 4rem;
      margin-bottom: 1rem;
      opacity: 0.3;
    }

    .empty-state-title {
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 0.5rem;
    }

    .empty-state-text {
      color: var(--gray);
      margin-bottom: 2rem;
    }

    .btn-empty-action {
      background: linear-gradient(135deg, var(--primary), var(--primary-dark));
      color: white;
      padding: 0.8rem 2rem;
      border-radius: 12px;
      border: none;
      font-weight: 600;
      text-decoration: none;
      display: inline-block;
      transition: all 0.3s;
    }

    .btn-empty-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
      color: white;
    }

    /* RESPONSIVE */
    @media (max-width: 768px) {
      .welcome-title {
        font-size: 1.5rem;
      }

      .welcome-subtitle {
        font-size: 1rem;
      }

      .stat-value {
        font-size: 1.8rem;
      }

      .content-title {
        font-size: 1.2rem;
      }

      .custom-table {
        font-size: 0.85rem;
      }

      .custom-table thead th,
      .custom-table tbody td {
        padding: 0.8rem;
      }

      .action-buttons {
        flex-direction: column;
      }
    }

    /* Hide on mobile */
    @media (max-width: 576px) {
      .hide-mobile {
        display: none;
      }
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
  <div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between w-100">
      <span class="navbar-brand">ONSP</span>
      <div class="d-flex align-items-center gap-3">
        <span class="tutor-badge">Tutor</span>
        <a href="../auth/logout.php" class="btn btn-logout">Logout</a>
      </div>
    </div>
  </div>
</nav>

<!-- WELCOME SECTION -->
<section class="welcome-section">
  <div class="container">
    <div class="welcome-content" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
    <div class="welcome-para">
      <h1 class="welcome-title">Welcome back, <?= htmlspecialchars($profile['name'] ?? 'Tutor') ?>! 👋</h1>
      <p class="welcome-subtitle">Manage your notes and track your impact</p>
      </div>
      <a href="upload_note.php" class="btn-upload">
        <span>➕</span>
        <span>Upload New Notes</span>
      </a>
    </div>
  </div>
</section>

<div class="container pb-5">

  <!-- STATS GRID -->
  <div class="row g-4 mb-4">
    <div class="col-lg-6 col-md-6">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon purple">📄</div>
        </div>
        <div class="stat-label">Notes Uploaded</div>
        <div class="stat-value"><?= $totalNotes ?></div>
        <p style="color: var(--gray); font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">
          Total notes you've shared
        </p>
      </div>
    </div>

    <div class="col-lg-6 col-md-6">
      <div class="stat-card">
        <div class="stat-header">
          <div class="stat-icon blue">👁️</div>
        </div>
        <div class="stat-label">Total Views</div>
        <div class="stat-value"><?= number_format($totalViews) ?></div>
        <p style="color: var(--gray); font-size: 0.85rem; margin-top: 0.5rem; margin-bottom: 0;">
          Combined views across all notes
        </p>
      </div>
    </div>
  </div>

  <!-- NOTES TABLE -->
  <div class="content-card">
    <div class="content-header">
      <span style="font-size: 1.5rem;">📄</span>
      <h4 class="content-title">Your Notes</h4>
    </div>

    <?php if ($totalNotes > 0): ?>
    <div class="table-responsive">
      <table class="custom-table">
        <thead>
          <tr>
            <th>Title</th>
            <th class="hide-mobile">Subject</th>
            <th>Views</th>
            <th class="hide-mobile">Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($notes as $note): ?>
          <tr>
            <td class="note-title-cell">
              <?= htmlspecialchars($note['title']) ?>
            </td>
            <td class="hide-mobile">
              <span class="note-subject">
                <?= htmlspecialchars($note['category']) ?>
              </span>
            </td>
            <td>
              <div class="note-views">
                <span>👁️</span>
                <span><?= number_format($note['view_count'] ?? 0) ?></span>
              </div>
            </td>
            <td class="hide-mobile">
              <span class="note-date">
                <?= date('M d, Y', strtotime($note['created_at'])) ?>
              </span>
            </td>
            <td>
              <div class="action-buttons">
                <a href="../preview.php?id=<?= $note['id'] ?>" class="btn-action view" title="View">
                  👁️
                </a>
                <a href="edit.php?id=<?= $note['id'] ?>" class="btn-action edit" title="Edit">
                  ✏️
                </a>
                <a href="delete.php?id=<?= $note['id'] ?>" 
                   class="btn-action delete" 
                   title="Delete"
                   onclick="return confirm('Are you sure you want to delete this note?')">
                  🗑️
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php else: ?>
    <div class="empty-state">
      <div class="empty-state-icon">📄</div>
      <div class="empty-state-title">No notes uploaded yet</div>
      <p class="empty-state-text">Start sharing your knowledge by uploading your first note</p>
      <a href="upload_note.php" class="btn-empty-action">Upload Your First Note</a>
    </div>
    <?php endif; ?>
  </div>

</div>

</body>
</html>