<?php
session_start();
require_once "../config/supabase.php";

/* ================= AUTH CHECK ================= */
if (!isset($_SESSION['access_token']) || !isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* ================= SUPABASE GET HELPER ================= */
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

/* ================= PROFILE CHECK ================= */
$profileData = supabaseGet("/rest/v1/profiles?id=eq." . $_SESSION['user_id']);
$profile = $profileData[0] ?? null;

if (!$profile || $profile['role'] !== 'tutor') {
    die("Unauthorized access");
}

/* ================= FORM HANDLING ================= */
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title       = trim($_POST['title']);
    $description = trim($_POST['description']);
    $category    = trim($_POST['category']);

    if (empty($title) || empty($description) || empty($category)) {
        $error = "All fields are required";
    } elseif (!isset($_FILES['pdf']) || $_FILES['pdf']['error'] !== 0) {
        $error = "PDF upload failed";
    } else {

        /* ================= FILE PREP ================= */
        $fileTmp  = $_FILES['pdf']['tmp_name'];
        $fileName = time() . "_" . preg_replace("/[^a-zA-Z0-9._-]/", "", $_FILES['pdf']['name']);
        $filePath = $profile['id'] . "/" . $fileName;

        /* ================= UPLOAD TO SUPABASE STORAGE ================= */
        $uploadUrl = SUPABASE_URL . "/storage/v1/object/notes-pdfs/" . $filePath;

        $ch = curl_init($uploadUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer " . $_SESSION['access_token'], // USER TOKEN
                "apikey: " . SUPABASE_ANON_KEY,
                "Content-Type: application/pdf",
                "x-upsert: true"
            ],
            CURLOPT_POSTFIELDS => file_get_contents($fileTmp)
        ]);

        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!in_array($status, [200, 201])) {
            $error = "Storage upload failed (HTTP $status)";
        } else {

            /* ================= INSERT NOTE RECORD ================= */
            $notePayload = json_encode([
                "tutor_id"   => $_SESSION['user_id'],
                "title"      => $title,
                "description"=> $description,
                "category"   => $category,
                "pdf_path"   => $filePath,
                "view_count" => 0
            ]);

            $ch = curl_init(SUPABASE_URL . "/rest/v1/notes");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    "apikey: " . SUPABASE_ANON_KEY,
                    "Authorization: Bearer " . $_SESSION['access_token'],
                    "Content-Type: application/json",
                    "Prefer: return=minimal"
                ],
                CURLOPT_POSTFIELDS => $notePayload
            ]);

            curl_exec($ch);
            curl_close($ch);

            header("Location: dashboard.php");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Upload Notes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-6">

      <div class="card shadow">
        <div class="card-body">

          <h4 class="mb-3">📄 Upload Notes</h4>

          <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
          <?php endif; ?>

          <form method="POST" enctype="multipart/form-data">

            <input type="text" name="title" class="form-control mb-3" placeholder="Title" required>

            <textarea name="description" class="form-control mb-3" placeholder="Description" rows="3" required></textarea>

            <select name="category" class="form-control mb-3" required>
              <option value="">Select Subject</option>
              <option>Mathematics</option>
              <option>Physics</option>
              <option>Chemistry</option>
              <option>Biology</option>
              <option>Computer Science</option>
            </select>

            <input type="file" name="pdf" class="form-control mb-3" accept="application/pdf" required>

            <button class="btn btn-primary w-100">Upload Notes</button>
            <a href="dashboard.php" class="btn btn-link w-100">Cancel</a>

          </form>

        </div>
      </div>

    </div>
  </div>
</div>

</body>
</html>
