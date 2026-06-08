<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";
include "server_validation.php";

$errors = [];
$old_values = [];

// Fetch members and trainers for dropdowns
$members_result = $conn->query("SELECT member_id, name FROM members WHERE status = 'Active'");
$trainers_result = $conn->query("SELECT trainer_id, name FROM trainers");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'] ?? '';
    $trainer_id = $_POST['trainer_id'] ?? '';
    $workout_date = $_POST['workout_date'] ?? '';
    $exercise_name = sanitizeInput($_POST['exercise_name'] ?? '');
    $sets = $_POST['sets'] ?? '';
    $reps = $_POST['reps'] ?? '';
    
    $old_values = [
        'member_id' => $member_id,
        'trainer_id' => $trainer_id,
        'workout_date' => $workout_date,
        'exercise_name' => $exercise_name,
        'sets' => $sets,
        'reps' => $reps
    ];
    
    // Validate fields
    if (empty($member_id)) $errors['member_id'] = "Please select a member";
    if (empty($trainer_id)) $errors['trainer_id'] = "Please select a trainer";
    
    $date_validation = validateDate($workout_date);
    if (!$date_validation['valid']) $errors['workout_date'] = $date_validation['message'];
    
    $exercise_validation = validateExercise($exercise_name);
    if (!$exercise_validation['valid']) $errors['exercise_name'] = $exercise_validation['message'];
    
    $sets_validation = validateNumber($sets, "Sets", 1, 20);
    if (!$sets_validation['valid']) $errors['sets'] = $sets_validation['message'];
    
    $reps_validation = validateNumber($reps, "Reps", 1, 100);
    if (!$reps_validation['valid']) $errors['reps'] = $reps_validation['message'];
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO workout_plans (member_id, trainer_id, workout_date, exercise_name, sets, reps) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iisssi", $member_id, $trainer_id, $workout_date, $exercise_name, $sets, $reps);
        
        if ($stmt->execute()) {
            header("Location: view_workouts.php?success=1");
            exit;
        } else {
            $db_error = "Database error: " . $conn->error;
        }
    }
}
?>

<h2>Add Workout Plan</h2>

<?php if (isset($db_error)): ?>
    <div class="error-message"><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></div>
<?php endif; ?>

<form method="POST">
    <label>Member:<br>
        <select name="member_id" required>
            <option value="">Select Member</option>
            <?php while($m = $members_result->fetch_assoc()): ?>
                <option value="<?= $m['member_id'] ?>" <?= (isset($old_values['member_id']) && $old_values['member_id'] == $m['member_id']) ? 'selected' : '' ?>><?= htmlspecialchars($m['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <?php if (isset($errors['member_id'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['member_id']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Trainer:<br>
        <select name="trainer_id" required>
            <option value="">Select Trainer</option>
            <?php while($t = $trainers_result->fetch_assoc()): ?>
                <option value="<?= $t['trainer_id'] ?>" <?= (isset($old_values['trainer_id']) && $old_values['trainer_id'] == $t['trainer_id']) ? 'selected' : '' ?>><?= htmlspecialchars($t['name']) ?></option>
            <?php endwhile; ?>
        </select>
        <?php if (isset($errors['trainer_id'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['trainer_id']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Date:<br>
        <input type="date" name="workout_date" required value="<?= isset($old_values['workout_date']) ? htmlspecialchars($old_values['workout_date']) : date('Y-m-d') ?>">
        <?php if (isset($errors['workout_date'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['workout_date']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Exercise Name:<br>
        <input type="text" name="exercise_name" required value="<?= isset($old_values['exercise_name']) ? htmlspecialchars($old_values['exercise_name']) : '' ?>">
        <?php if (isset($errors['exercise_name'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['exercise_name']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Sets:<br>
        <input type="number" name="sets" min="1" max="20" required value="<?= isset($old_values['sets']) ? htmlspecialchars($old_values['sets']) : '' ?>">
        <?php if (isset($errors['sets'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['sets']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Reps:<br>
        <input type="number" name="reps" min="1" max="100" required value="<?= isset($old_values['reps']) ? htmlspecialchars($old_values['reps']) : '' ?>">
        <?php if (isset($errors['reps'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['reps']); ?></span><?php endif; ?>
    </label><br>
    
    <button type="submit">Add Workout</button>
</form>

<?php include "../includes/footer.php"; ?>
