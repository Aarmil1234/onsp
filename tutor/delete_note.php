<?php
session_start();
require_once "../config/supabase.php";

if (!isset($_SESSION['access_token'], $_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$noteId = $_GET['id'] ?? null;
if (!$noteId) die("Invalid request");

/* Fetch note (ownership check) */
$ch = curl_init(SUPABASE_URL . "/rest/v1/notes?id=eq.$noteId&tutor_id=eq.".$_SESSION['user_id']);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer " . $_SESSION['access_token']
    ]
]);
$note = json_decode(curl_exec($ch), true)[0] ?? null;
curl_close($ch);

if (!$note) die("Unauthorized");

/* Delete PDF */
$ch = curl_init(SUPABASE_URL . "/storage/v1/object/notes-pdfs/" . $note['pdf_path']);
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer " . $_SESSION['access_token']
    ]
]);
curl_exec($ch);
curl_close($ch);

/* Delete DB record */
$ch = curl_init(SUPABASE_URL . "/rest/v1/notes?id=eq.$noteId");
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => "DELETE",
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer " . $_SESSION['access_token']
    ]
]);
curl_exec($ch);
curl_close($ch);

header("Location: dashboard.php");
exit;
