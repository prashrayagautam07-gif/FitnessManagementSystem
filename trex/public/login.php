<?php
session_start();
include "../config/db.php";
$error="";
if($_SERVER["REQUEST_METHOD"]=="POST"){
$user=$_POST['username'];
$pass=md5($_POST['password']);
$stmt=$conn->prepare("SELECT * FROM admins WHERE username=? AND password=?");
$stmt->bind_param("ss",$user,$pass);
$stmt->execute();
$res=$stmt->get_result();
if($res->num_rows===1){ $_SESSION['admin']=$user; header("Location:index.php"); exit(); }
else{ $error="Invalid login!"; }
}
?>
<!DOCTYPE html><html><head>
<link rel="stylesheet" href="../assets/css/style.css"></head>
<body class="login-body">
<div class="login-box"><h2>Admin Login</h2>
<form method="POST">
<label>Username:<br>
<input type="text" name="username" placeholder="Enter username" required>
</label>

<label>Password:<br>
<input type="password" name="password" placeholder="Enter password" required>
</label>

<button>Login</button></form>
<p class="error"><?php echo $error; ?></p>
</div></body></html>