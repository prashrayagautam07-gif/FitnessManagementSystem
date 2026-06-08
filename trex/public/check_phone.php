<?php
include "../config/db.php";

$phone = $_GET['phone'] ?? '';
$type  = $_GET['type'] ?? 'member';
$exclude_id = (int)($_GET['exclude_id'] ?? 0);

$table = ($type === 'trainer') ? 'trainers' : 'members';
$id_col = ($type === 'trainer') ? 'trainer_id' : 'member_id';

if ($exclude_id > 0) {
    $stmt = $conn->prepare("SELECT phone FROM $table WHERE phone = ? AND $id_col != ?");
    $stmt->bind_param("si", $phone, $exclude_id);
} else {
    $stmt = $conn->prepare("SELECT phone FROM $table WHERE phone = ?");
    $stmt->bind_param("s", $phone);
}

$stmt->execute();
$stmt->store_result();

echo json_encode([
    "exists" => $stmt->num_rows > 0
]);
