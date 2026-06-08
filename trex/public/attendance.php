<?php 
include "auth.php";
include "../includes/header.php";
?>
<h2>Attendance Check</h2>

<input id="member_id" placeholder="Enter Member ID" onkeyup="checkMembership()">
<div id="result"></div>

<script src="../assets/js/validation.js"></script>
<?php include "../includes/footer.php"; ?>