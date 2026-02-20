<?php
session_start();
require_once "config/supabase.php";


/* ===============================
   GET NOTE ID
================================ */
$id = $_GET['id'] ?? null;
if(!$id){
    die("Invalid note ID");
}

/* ===============================
   FETCH NOTE FROM SUPABASE
================================ */
$url = SUPABASE_URL."/rest/v1/notes?id=eq.$id";

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if(!$data){
    die("Note not found");
}

$note = $data[0];

/* ===============================
   INCREASE VIEW COUNT
================================ */

$currentViews = $note['view_count'] ?? 0;
$newViews = $currentViews + 1;

$updateUrl = SUPABASE_URL."/rest/v1/notes?id=eq.$id";

$payload = json_encode([
    "view_count" => $newViews
]);

$ch = curl_init($updateUrl);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "PATCH",
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer " . SUPABASE_ANON_KEY,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ]
]);

$result = curl_exec($ch);

if(curl_errno($ch)){
    die("Update error: " . curl_error($ch));
}

curl_close($ch);

/* ===============================
   PDF PATH
================================ */

$pdf = SUPABASE_URL."/storage/v1/object/public/notes-pdfs/".$note['pdf_path'];
?>

<!DOCTYPE html>
<html>
<head>
<title><?= htmlspecialchars($note['title']) ?></title>

<style>
body{
    margin:0;
    background:#111;
    font-family: Arial, sans-serif;
}

.viewer{
    position:relative;
    height:100vh;
}

.blur{
    position:absolute;
    bottom:0;
    height:55%;
    width:100%;
    background:linear-gradient(transparent,rgba(0,0,0,0.92));
    display:flex;
    align-items:center;
    justify-content:center;
    flex-direction:column;
    color:white;
    text-align:center;
}

.btn{
    background:#4f46e5;
    color:white;
    padding:12px 24px;
    text-decoration:none;
    border-radius:10px;
    margin-top:12px;
    font-weight:600;
}
</style>
</head>

<body>

<div class="viewer">

<?php if(isset($_SESSION['user_id'])): ?>

    <!-- FULL PDF -->
    <iframe src="<?= $pdf ?>" width="100%" height="100%"></iframe>

<?php else: ?>

    <!-- ONLY FIRST PAGE -->
    <iframe src="<?= $pdf ?>#page=1" width="100%" height="100%"></iframe>

    <div class="blur">
        <h2>Login to view full notes</h2>
        <a href="auth/login.php" class="btn">Login Now</a>
    </div>

<?php endif; ?>

</div>

</body>
</html>
