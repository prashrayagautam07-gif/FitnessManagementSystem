<?php
include "../config/db.php";

$email = $_GET['email'] ?? '';
$type  = $_GET['type'] ?? 'member';

$table = ($type === 'trainer') ? 'trainers' : 'members';

$stmt = $conn->prepare("SELECT email FROM $table WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    "exists" => $stmt->num_rows > 0
]);
