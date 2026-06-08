<?php
include "auth.php";
include "../config/db.php";
$id = $_GET['id'];

$stmt = $conn->prepare("DELETE FROM members WHERE member_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();

header("Location: index.php");
exit();
?>