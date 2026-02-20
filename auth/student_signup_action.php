<?php
require_once "../config/supabase.php";

$name     = $_POST['name'];
$email    = $_POST['email'];
$password = $_POST['password'];

if(!$name || !$email || !$password){
    die("All fields required");
}

/* ---------------- CREATE USER IN AUTH ---------------- */
$payload = json_encode([
    "email"=>$email,
    "password"=>$password,
    "email_confirm"=>true
]);

$ch = curl_init(SUPABASE_URL."/auth/v1/admin/users");
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
        "apikey: ".SUPABASE_SERVICE_KEY,
        "Authorization: Bearer ".SUPABASE_SERVICE_KEY,
        "Content-Type: application/json"
    ],
    CURLOPT_POSTFIELDS=>$payload
]);

$res = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$data = json_decode($res,true);

if($status!=200 && $status!=201){
    echo "Signup failed<br>";
    print_r($data);
    exit;
}

$userId = $data['id'];

/* ---------------- INSERT INTO PROFILES ---------------- */
$profileData = json_encode([
    "id"=>$userId,
    "name"=>$name,
    "email"=>$email,
    "role"=>"student",
    "status"=>"active"
]);

$ch = curl_init(SUPABASE_URL."/rest/v1/profiles");
curl_setopt_array($ch,[
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POST=>true,
    CURLOPT_HTTPHEADER=>[
        "apikey: ".SUPABASE_SERVICE_KEY,
        "Authorization: Bearer ".SUPABASE_SERVICE_KEY,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ],
    CURLOPT_POSTFIELDS=>$profileData
]);

curl_exec($ch);
curl_close($ch);

echo "<script>
alert('Signup successful! Login now');
window.location='login.php';
</script>";
?>
