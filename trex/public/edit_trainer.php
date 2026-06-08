<?php
include "auth.php";
include "../config/db.php";
include "server_validation.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid trainer ID.");

$errors = [];
$old_values = [];

// Get current data
$stmt = $conn->prepare("SELECT * FROM trainers WHERE trainer_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) die("Trainer not found.");

// Initialize old values
$old_values = [
    'name' => $data['name'],
    'specialization' => $data['specialization'],
    'phone' => $data['phone'],
    'email' => $data['email']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $specialization = sanitizeInput($_POST['specialization']);
    $phone = sanitizeInput($_POST['phone']);
    $email = sanitizeInput($_POST['email']);
    
    $old_values = ['name' => $name, 'specialization' => $specialization, 'phone' => $phone, 'email' => $email];
    
    // Validate
    $name_validation = validateName($name);
    if (!$name_validation['valid']) $errors['name'] = $name_validation['message'];
    
    if (!empty($specialization)) {
        if (strlen($specialization) > 100) {
            $errors['specialization'] = 'Specialization too long (max 100 chars)';
        } elseif (!preg_match('/^[a-zA-Z\s\.\-\']+$/', $specialization)) {
            $errors['specialization'] = 'Specialization can only contain letters, spaces, dots, and hyphens';
        }
    }
    
    $email_validation = validateEmail($email);
    if (!$email_validation['valid']) $errors['email'] = $email_validation['message'];
    
    $phone_validation = validatePhone($phone);
    if (!$phone_validation['valid']) $errors['phone'] = $phone_validation['message'];
    
    // Check for duplicates (excluding current trainer)
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ? AND trainer_id != ?");
        $check_stmt->bind_param("si", $email, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['email'] = "Email already exists in system";
        $check_stmt->close();
        
        $check_stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE phone = ? AND trainer_id != ?");
        $check_stmt->bind_param("si", $phone, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['phone'] = "Phone number already exists in system";
        $check_stmt->close();
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE trainers SET name=?, specialization=?, phone=?, email=? WHERE trainer_id=?");
        $stmt->bind_param("ssssi", $name, $specialization, $phone, $email, $id);

        if ($stmt->execute()) {
            header("Location: trainers.php?updated=1");
            exit;
        } else {
            $db_error = "Update failed: " . $conn->error;
        }
    }
}

include "../includes/header.php";
?>

<h2>Edit Trainer: <?= htmlspecialchars($data['name']) ?></h2>

<?php if (isset($db_error)): ?>
    <div class="error-message"><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></div>
<?php endif; ?>

<form method="POST">
    <label>Name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($old_values['name']) ?>" required>
        <?php if (isset($errors['name'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['name']); ?></span><?php endif; ?>
    </label><br>

    <label>Specialization:<br>
        <input type="text" name="specialization" value="<?= htmlspecialchars($old_values['specialization']) ?>">
        <?php if (isset($errors['specialization'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['specialization']); ?></span><?php endif; ?>
    </label><br>

    <label>Phone:<br>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old_values['phone']) ?>" required>
        <span id="phone-msg"></span>
        <?php if (isset($errors['phone'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['phone']); ?></span><?php endif; ?><br>
    </label>

    <label>Email:<br>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($old_values['email']) ?>" required>
        <span id="email-msg"></span>
        <?php if (isset($errors['email'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['email']); ?></span><?php endif; ?><br>
    </label><br>

    <button type="submit" id="submitBtn">Update</button>
  
    <input type="hidden" id="formType" value="trainer">
    <input type="hidden" id="currentId" value="<?= $id ?>">
</form>

<script src="../assets/js/validation.js"></script>
<?php include "../includes/footer.php"; ?>