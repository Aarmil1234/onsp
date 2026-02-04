<?php
session_start();
require_once "../config/supabase.php";

$name     = trim($_POST['name']);
$email    = trim($_POST['email']);
$password = $_POST['password'];

if (!$name || !$email || !$password) {
    die("All fields are required");
}

/* ================= STEP 1: CREATE AUTH USER ================= */
$ch = curl_init(SUPABASE_URL . "/auth/v1/admin/users");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_SERVICE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "email" => $email,
        "password" => $password,
        "email_confirm" => true
    ])
]);

$authResponse = json_decode(curl_exec($ch), true);
curl_close($ch);

if (!isset($authResponse['id'])) {
    echo "<pre>";
    print_r($authResponse); // 👈 shows exact Supabase error if any
    exit;
}

$userId = $authResponse['id'];

/* ================= STEP 2: INSERT INTO PROFILES ================= */
$ch = curl_init(SUPABASE_URL . "/rest/v1/profiles");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        "apikey: " . SUPABASE_SERVICE_KEY,
        "Authorization: Bearer " . SUPABASE_SERVICE_KEY,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ],
    CURLOPT_POSTFIELDS => json_encode([
        "id"     => $userId,        // 🔗 same as auth.users.id
        "name"   => $name,
        "email"  => $email,
        "role"   => "tutor",
        "status" => "active"
    ])
]);

$profileResult = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if (!in_array($httpCode, [200, 201])) {
    die("Auth user created, but profile insert failed");
}

/* ================= SUCCESS ================= */
header("Location: dashboard.php");
exit;
