<?php
include "../config/db.php";
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT expiry_date FROM members WHERE member_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    if (strtotime($row['expiry_date']) >= time()) {
       echo "<span class='status-active'>Membership Active</span>";
    } else {
        echo "<span class='status-expired'>Membership Expired</span>";

    }
} else {
    echo "Invalid Member ID";
}
