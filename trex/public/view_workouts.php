<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";

$query = "SELECT wp.plan_id, m.name AS member_name, t.name AS trainer_name, wp.workout_date, wp.exercise_name, wp.sets, wp.reps 
          FROM workout_plans wp
          JOIN members m ON wp.member_id = m.member_id
          JOIN trainers t ON wp.trainer_id = t.trainer_id
          ORDER BY wp.workout_date DESC";
$result = $conn->query($query);
?>

<h2>Workouts</h2>

<a href="add_workout.php"><button type="submit">Add Workout</button></a>

<table class="workout-table">
    <tr>
        <th>ID</th>
        <th>Member</th>
        <th>Trainer</th>
        <th>Date</th>
        <th>Exercise</th>
        <th>Sets</th>
        <th>Reps</th>
        <th>Action</th>
    </tr>
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['plan_id']) ?></td>
                <td><?= htmlspecialchars($row['member_name']) ?></td>
                <td><?= htmlspecialchars($row['trainer_name']) ?></td>
                <td><?= htmlspecialchars($row['workout_date']) ?></td>
                <td><?= htmlspecialchars($row['exercise_name']) ?></td>
                <td><?= htmlspecialchars($row['sets']) ?></td>
                <td><?= htmlspecialchars($row['reps']) ?></td>
                <td>
                    <a href="delete_workout.php?id=<?= $row['plan_id'] ?>" onclick="return confirm('Delete this workout plan?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="8">No workout plans found.</td></tr>
    <?php endif; ?>
</table>

<?php include "../includes/footer.php"; ?>
