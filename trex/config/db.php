<?php
$conn = mysqli_connect("localhost", "root", "", "fitness_club");

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
