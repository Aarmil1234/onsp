<?php
session_start();
require_once "config/supabase.php";

if (!isset($_SESSION['access_token'])) {
  header("Location: auth/login.php");
  exit;
}

$ch = curl_init(SUPABASE_URL . "/rest/v1/profiles?id=eq." . $_SESSION['user_id']);

curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => [
    "apikey: " . SUPABASE_ANON_KEY,
    "Authorization: Bearer " . $_SESSION['access_token']
  ]
]);

$response = curl_exec($ch);
curl_close($ch);

$profile = json_decode($response, true)[0] ?? null;

if (!$profile) {
  die("Profile not found");
}

switch ($profile['role']) {
  case 'admin':
    header("Location: admin/dashboard.php");
    break;
  case 'tutor':
    header("Location: tutor/dashboard.php");
    break;
  case 'student':
    header("Location: student/dashboard.php");
    break;
}
exit;
