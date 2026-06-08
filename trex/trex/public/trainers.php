<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";
$result = $conn->query("SELECT * FROM trainers");
?>

<h2>Trainers</h2>
<a href="add_trainer.php"><button type="submit">Add Trainer</button> </a>


<table class="trainer-table">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Specialization</th>
    <th>Phone</th>
    <th>Email</th>
    <th>Action</th>
</tr>

<?php while($t = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($t['trainer_id']) ?></td>
<td><?= htmlspecialchars($t['name']) ?></td>
<td><?= htmlspecialchars($t['specialization']) ?></td>
<td><?= htmlspecialchars($t['phone']) ?></td>
<td><?= htmlspecialchars($t['email']) ?></td>
<td>
    <a href="edit_trainer.php?id=<?= $t['trainer_id'] ?>">Edit</a> |
    <a href="delete_trainer.php?id=<?= $t['trainer_id'] ?>"
       onclick="return confirm('Delete this trainer?')">Delete</a>
</td>
</tr>
<?php endwhile; ?>
</table>
<?php include "../includes/footer.php"; ?>
