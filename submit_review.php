<?php
session_start();
require_once "config/supabase.php";

header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if(!isset($_SESSION['user_id'])){
    echo json_encode(["success"=>false,"message"=>"Login required"]);
    exit;
}

$payload = json_encode([
    "note_id"       => $data['note_id'],
    "student_id"    => $_SESSION['user_id'],
    "student_name"  => $_SESSION['name'],   // 🔥 store name snapshot
    "rating"        => $data['rating'],
    "feedback"      => $data['feedback']
]);

$ch = curl_init(SUPABASE_URL."/rest/v1/note_feedback");

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_HTTPHEADER => [
        "apikey: ".SUPABASE_ANON_KEY,
        "Authorization: Bearer ".SUPABASE_ANON_KEY,
        "Content-Type: application/json",
        "Prefer: return=minimal"
    ]
]);

$response = curl_exec($ch);
$error = curl_error($ch);
curl_close($ch);

if($error){
    echo json_encode(["success"=>false,"error"=>$error]);
    exit;
}

echo json_encode(["success"=>true]);