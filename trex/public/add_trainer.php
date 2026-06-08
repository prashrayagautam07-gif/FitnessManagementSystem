<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";
include "server_validation.php";

$errors = [];
$old_values = [];

if ($_POST) {
    $name = sanitizeInput($_POST['name']);
    $specialization = sanitizeInput($_POST['specialization']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    
    $old_values = ['name' => $name, 'specialization' => $specialization, 'email' => $email, 'phone' => $phone];
    
    // Validate each field
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
    
    // Check for duplicate email/phone
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['email'] = "Email already exists in system";
        $check_stmt->close();
        
        $check_stmt = $conn->prepare("SELECT trainer_id FROM trainers WHERE phone = ?");
        $check_stmt->bind_param("s", $phone);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['phone'] = "Phone number already exists in system";
        $check_stmt->close();
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO trainers (name, specialization, email, phone) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $name, $specialization, $email, $phone);
        
        if ($stmt->execute()) {
            header("Location: trainers.php?success=1");
            exit();
        } else {
            $db_error = "Database error: " . $conn->error;
        }
    }
}
?>

<h2>Add Trainer</h2>

<?php if (isset($db_error)): ?>
    <div class="error-message"><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></div>
<?php endif; ?>

<form method="POST">
    <label>Name:<br>
        <input name="name" placeholder="Name" required value="<?= isset($old_values['name']) ? htmlspecialchars($old_values['name']) : '' ?>">
        <?php if (isset($errors['name'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['name']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Specialization:<br>
        <input name="specialization" placeholder="Specialization" value="<?= isset($old_values['specialization']) ? htmlspecialchars($old_values['specialization']) : '' ?>">
        <?php if (isset($errors['specialization'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['specialization']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Email:<br>
        <input type="email" name="email" id="email" placeholder="Email" required value="<?= isset($old_values['email']) ? htmlspecialchars($old_values['email']) : '' ?>">
        <span id="email-msg"></span>
        <?php if (isset($errors['email'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['email']); ?></span><?php endif; ?><br>
    </label>
    
    <label>Phone:<br>
        <input type="text" name="phone" id="phone" placeholder="Phone" required value="<?= isset($old_values['phone']) ? htmlspecialchars($old_values['phone']) : '' ?>">
        <span id="phone-msg"></span>
        <?php if (isset($errors['phone'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['phone']); ?></span><?php endif; ?><br>
    </label><br>
    
    <button id="submitBtn">Add Trainer</button>
    <input type="hidden" id="formType" value="trainer">
</form>

<script src="../assets/js/validation.js"></script>
<?php include "../includes/footer.php"; ?>