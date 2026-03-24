<?php
session_start();
require_once "../config/supabase.php";

/* ---------- AUTH CHECK ---------- */
if (!isset($_SESSION['access_token'], $_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$access_token = $_SESSION['access_token'];
$user_id = $_SESSION['user_id'];

$noteId = $_GET['id'] ?? null;

if (!$noteId) {
    die("Invalid note ID");
}


/* ---------- STEP 1: FETCH NOTE (VERIFY OWNERSHIP) ---------- */
$url = SUPABASE_URL . "/rest/v1/notes?id=eq.$noteId&tutor_id=eq.$user_id";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer $access_token",
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    die("Fetch Error: " . curl_error($ch));
}

$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);

if ($http != 200) {
    die("Fetch failed. HTTP Code: $http <br> Response: $response");
}

$data = json_decode($response, true);

if (!$data || count($data) == 0) {
    die("Note not found OR you don't have permission");
}

$note = $data[0];
$pdfPath = $note['pdf_path'];


/* ---------- STEP 2: DELETE PDF FROM STORAGE ---------- */

if (!empty($pdfPath)) {

    $storageURL = SUPABASE_URL . "/storage/v1/object/notes-pdfs/" . $pdfPath;

    $ch = curl_init($storageURL);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => "DELETE",
        CURLOPT_HTTPHEADER => [
            "apikey: " . SUPABASE_ANON_KEY,
            "Authorization: Bearer $access_token"
        ]
    ]);

    $storageResponse = curl_exec($ch);

    if (curl_errno($ch)) {
        echo "Storage Delete Error: " . curl_error($ch);
    }

    $storageHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    echo "Storage Delete HTTP Code: $storageHTTP<br>";
}


/* ---------- STEP 3: DELETE NOTE FROM DATABASE ---------- */

$deleteURL = SUPABASE_URL . "/rest/v1/notes?id=eq.$noteId&tutor_id=eq.$user_id";

$ch = curl_init($deleteURL);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_HEADER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer $access_token",
        "Content-Type: application/json",
        "Prefer: return=representation"
    ]
]);

$deleteResponse = curl_exec($ch);

if (curl_errno($ch)) {
    die("Delete Error: " . curl_error($ch));
}

$deleteHTTP = curl_getinfo($ch, CURLINFO_HTTP_CODE);

curl_close($ch);


/* ---------- DEBUG OUTPUT ---------- */

echo "<pre>";
echo "DELETE HTTP Code: " . $deleteHTTP . "\n\n";
echo "DELETE Response:\n";
echo $deleteResponse;
echo "</pre>";


/* ---------- SUCCESS ---------- */

if ($deleteHTTP == 204 || $deleteHTTP == 200) {

    header("Location: dashboard.php?deleted=success");
    exit;

} else {

    echo "<br>Delete failed. Check RLS policy.";
}
?>