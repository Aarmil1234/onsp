<?php
session_start();
require_once "../config/supabase.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$ch = curl_init(SUPABASE_URL . "/auth/v1/token?grant_type=password");

curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/json",
    "apikey: " . SUPABASE_ANON_KEY,
    "Authorization: Bearer " . SUPABASE_ANON_KEY
  ],
  CURLOPT_POSTFIELDS => json_encode([
    "email" => $email,
    "password" => $password
  ])
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($response, true);

if ($httpCode !== 200) {
  $_SESSION['error'] = $data['error_description'] ?? "Invalid login";
  header("Location: login.php");
  exit;
}

$_SESSION['access_token'] = $data['access_token'];
$_SESSION['user_id'] = $data['user']['id'];

header("Location: ../index.php");
exit;
