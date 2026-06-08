<?php
include "auth.php"; // ADD THIS
include "../config/db.php";

$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM trainers WHERE trainer_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: trainers.php");
exit();
?>