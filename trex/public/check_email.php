<?php
include "../config/db.php";

$email = $_GET['email'] ?? '';
$type  = $_GET['type'] ?? 'member';
$exclude_id = (int)($_GET['exclude_id'] ?? 0);

$table = ($type === 'trainer') ? 'trainers' : 'members';
$id_col = ($type === 'trainer') ? 'trainer_id' : 'member_id';

if ($exclude_id > 0) {
    $stmt = $conn->prepare("SELECT email FROM $table WHERE email = ? AND $id_col != ?");
    $stmt->bind_param("si", $email, $exclude_id);
} else {
    $stmt = $conn->prepare("SELECT email FROM $table WHERE email = ?");
    $stmt->bind_param("s", $email);
}

$stmt->execute();
$stmt->store_result();

echo json_encode([
    "exists" => $stmt->num_rows > 0
]);
