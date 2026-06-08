<?php
include "../includes/header.php";
include "../config/db.php";
$result = $conn->query("SELECT * FROM members");
?>

<h2>Members</h2>

<a href="add_member.php"><button type="submit">Add Member</button> </a>


<table class="members-table">

<tr>
<th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Type</th><th>Expiry</th><th>Status</th><th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['member_id']) ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['phone']) ?></td>
<td><?= htmlspecialchars($row['membership_type']) ?></td>
<td><?= htmlspecialchars($row['expiry_date']) ?></td>
<?php
$today = date('Y-m-d');
if ($row['expiry_date'] < $today) {
    $status = "Expired";
} else {
    $status = $row['status'];
}
?>
<td><?= htmlspecialchars($status) ?></td>

<td>
<a href="edit_member.php?id=<?= $row['member_id'] ?>">Edit</a> |
<a href="delete_member.php?id=<?= $row['member_id'] ?>" onclick="return confirm('Delete?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>

<?php include "../includes/footer.php"; ?>
