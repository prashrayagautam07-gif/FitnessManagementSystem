<?php
include "../config/db.php";

$phone = $_GET['phone'] ?? '';
$type  = $_GET['type'] ?? 'member';

$table = ($type === 'trainer') ? 'trainers' : 'members';

$stmt = $conn->prepare("SELECT phone FROM $table WHERE phone = ?");
$stmt->bind_param("s", $phone);
$stmt->execute();
$stmt->store_result();

echo json_encode([
    "exists" => $stmt->num_rows > 0
]);
