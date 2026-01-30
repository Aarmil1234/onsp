<?php
session_start();
$data = json_decode(file_get_contents("php://input"), true);

$_SESSION['access_token'] = $data['access_token'];
$_SESSION['user_id'] = $data['user']['id'];

echo json_encode(["status" => "ok"]);
