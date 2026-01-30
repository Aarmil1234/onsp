<?php
session_start();
require_once "../config/supabase.php";

if (!isset($_SESSION['access_token'], $_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$noteId = $_GET['id'] ?? null;
if (!$noteId) die("Invalid note");

function supabase($method, $endpoint, $payload = null) {
    $ch = curl_init(SUPABASE_URL . $endpoint);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            "apikey: " . SUPABASE_ANON_KEY,
            "Authorization: Bearer " . $_SESSION['access_token'],
            "Content-Type: application/json"
        ],
        CURLOPT_POSTFIELDS => $payload
    ]);
    $res = curl_exec($ch);
    curl_close($ch);
    return json_decode($res, true);
}

/* Fetch note */
$note = supabase("GET", "/rest/v1/notes?id=eq.$noteId&tutor_id=eq.".$_SESSION['user_id'])[0] ?? null;
if (!$note) die("Unauthorized");

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = json_encode([
        "title" => $_POST['title'],
        "description" => $_POST['description'],
        "category" => $_POST['category']
    ]);

    supabase("PATCH", "/rest/v1/notes?id=eq.$noteId", $payload);
    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Note</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-6">
<div class="card shadow">
<div class="card-body">
<h4>Edit Note</h4>

<form method="POST">
<input class="form-control mb-3" name="title" value="<?= htmlspecialchars($note['title']) ?>" required>
<textarea class="form-control mb-3" name="description" required><?= htmlspecialchars($note['description']) ?></textarea>

<select class="form-control mb-3" name="category" required>
<option><?= $note['category'] ?></option>
<option>Mathematics</option>
<option>Physics</option>
<option>Chemistry</option>
<option>Biology</option>
<option>Computer Science</option>
</select>

<button class="btn btn-primary w-100">Update Note</button>
<a href="dashboard.php" class="btn btn-link w-100">Cancel</a>
</form>

</div>
</div>
</div>
</body>
</html>
