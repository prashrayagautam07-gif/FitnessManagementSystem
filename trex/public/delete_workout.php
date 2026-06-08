<?php
// ADD AUTHENTICATION FIRST
include "auth.php";

// Use correct path to config
include "../config/db.php";  

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request");
}

$plan_id = (int) $_GET['id'];

// SQL Injection safe delete using prepared statement
$stmt = $conn->prepare("DELETE FROM workout_plans WHERE plan_id = ?");
$stmt->bind_param("i", $plan_id);

if ($stmt->execute()) {
    // Success - redirect
    header("Location: view_workouts.php?deleted=1");
    exit;
} else {
    // Error - show message
    die("Error deleting workout: " . $conn->error);
}