<?php
require_once "config/supabase.php";

header('Content-Type: application/json');

if (!isset($_GET['note_id'])) {
    echo json_encode([]);
    exit;
}

$note_id = $_GET['note_id'];

$url = SUPABASE_URL .
"/rest/v1/note_feedback" .
"?note_id=eq." . urlencode($note_id) .
"&select=rating,feedback,student_name,created_at" .
"&order=created_at.desc";

$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY,
        "Authorization: Bearer " . SUPABASE_ANON_KEY,
        "Content-Type: application/json"
    ]
]);

$response = curl_exec($ch);
curl_close($ch);

echo $response;