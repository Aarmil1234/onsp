<?php
session_start();
require_once "../config/supabase.php";

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$email || !$password){
    $_SESSION['error'] = "Enter email & password";
    header("Location: login.php");
    exit;
}

/* ---------- LOGIN SUPABASE ---------- */
$payload = json_encode([
    "email"=>$email,
    "password"=>$password
]);

$ch = curl_init(SUPABASE_URL."/auth/v1/token?grant_type=password");
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
        "apikey: ".SUPABASE_ANON_KEY,
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS=>$payload
]);

$res = curl_exec($ch);
$data = json_decode($res,true);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if($status!=200){
    $_SESSION['error']="Invalid login credentials";
    header("Location: login.php");
    exit;
}

/* ---------- SAVE SESSION ---------- */
$_SESSION['access_token']=$data['access_token'];
$_SESSION['user_id']=$data['user']['id'];

/* ---------- GET PROFILE ---------- */
$ch = curl_init(SUPABASE_URL."/rest/v1/profiles?id=eq.".$_SESSION['user_id']);
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_HTTPHEADER=>[
        "apikey: ".SUPABASE_ANON_KEY,
        "Authorization: Bearer ".$_SESSION['access_token']
    ]
]);

$res = json_decode(curl_exec($ch),true);
curl_close($ch);

if(!$res){
    $_SESSION['error']="Profile not found";
    header("Location: login.php");
    exit;
}

$user = $res[0];

$_SESSION['name']=$user['name'];
$_SESSION['role']=$user['role'];

/* ---------- ROLE REDIRECT ---------- */
if($user['role']=="student"){
    header("Location: ../home.php");
    exit;
}

if($user['role']=="tutor"){
    header("Location: ../tutor/dashboard.php");
    exit;
}

if($user['role']=="admin"){
    header("Location: ../admin/dashboard.php");
    exit;
}

header("Location: ../home.php");
exit;
?>
