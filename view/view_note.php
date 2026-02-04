<?php
require_once "../config/supabase.php";

$noteId = $_GET['id'] ?? null;
if (!$noteId) {
    die("Invalid request");
}

/* Fetch note */
$ch = curl_init(SUPABASE_URL . "/rest/v1/notes?id=eq.$noteId");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_ANON_KEY
    ]
]);
$response = curl_exec($ch);
curl_close($ch);

$note = json_decode($response, true)[0] ?? null;
if (!$note) {
    die("Note not found");
}

/* Build PUBLIC URL – SAME AS MANUAL URL */
$pdfUrl = SUPABASE_URL .
          "/storage/v1/object/public/notes-pdfs/" .
          $note['pdf_path'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>View PDF</title>
</head>
<body style="margin:0">


<iframe
    src="<?= htmlspecialchars($pdfUrl) ?>"
    width="100%"
    height="100vh"
    style="border:none; height:100vh;">
</iframe>

</body>
</html>
