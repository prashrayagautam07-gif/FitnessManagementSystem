<?php
include "auth.php";
include "../includes/header.php";
include "../config/db.php";
include "server_validation.php";

$errors = [];
$old_values = [];

if ($_POST) {
    // Sanitize all inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $type = $_POST['membership_type'] ?? '';
    $start = $_POST['start_date'] ?? '';
    
    // Store old values
    $old_values = ['name' => $name, 'email' => $email, 'phone' => $phone, 'type' => $type, 'start' => $start];
    
    // Validate each field
    $name_validation = validateName($name);
    if (!$name_validation['valid']) $errors['name'] = $name_validation['message'];
    
    $email_validation = validateEmail($email);
    if (!$email_validation['valid']) $errors['email'] = $email_validation['message'];
    
    $phone_validation = validatePhone($phone);
    if (!$phone_validation['valid']) $errors['phone'] = $phone_validation['message'];
    
    $type_validation = validateMembershipType($type);
    if (!$type_validation['valid']) $errors['type'] = $type_validation['message'];
    
    $date_validation = validateDate($start);
    if (!$date_validation['valid']) $errors['start'] = $date_validation['message'];
    
    // Check for duplicate email/phone
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['email'] = "Email already exists in system";
        $check_stmt->close();
        
        $check_stmt = $conn->prepare("SELECT member_id FROM members WHERE phone = ?");
        $check_stmt->bind_param("s", $phone);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['phone'] = "Phone number already exists in system";
        $check_stmt->close();
    }
    
    if (empty($errors)) {
        if ($type == "Monthly") $expiry = date('Y-m-d', strtotime("+1 month", strtotime($start)));
        elseif ($type == "Quarterly") $expiry = date('Y-m-d', strtotime("+3 month", strtotime($start)));
        else $expiry = date('Y-m-d', strtotime("+1 year", strtotime($start)));

        $status = "Active";

        $stmt = $conn->prepare("INSERT INTO members (name,email,phone,membership_type,start_date,expiry_date,status) VALUES (?,?,?,?,?,?,?)");
        $stmt->bind_param("sssssss",$name,$email,$phone,$type,$start,$expiry,$status);
        
        if ($stmt->execute()) {
            header("Location: index.php?success=1");
            exit();
        } else {
            $db_error = "Database error: " . $conn->error;
        }
    }
}
?>

<h2>Add Member</h2>

<?php if (isset($db_error)): ?>
    <div class="error-message"><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></div>
<?php endif; ?>

<form method="POST">
    <label>Name:<br>
        <input name="name" placeholder="Full Name" required value="<?= isset($old_values['name']) ? htmlspecialchars($old_values['name']) : '' ?>">
        <?php if (isset($errors['name'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['name']); ?></span><?php endif; ?>
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
    </label>
    
    <label>Membership Type:<br>
        <select name="membership_type">
            <option value="">Select Type</option>
            <option value="Monthly" <?= (isset($old_values['type']) && $old_values['type'] == 'Monthly') ? 'selected' : '' ?>>Monthly</option>
            <option value="Quarterly" <?= (isset($old_values['type']) && $old_values['type'] == 'Quarterly') ? 'selected' : '' ?>>Quarterly</option>
            <option value="Yearly" <?= (isset($old_values['type']) && $old_values['type'] == 'Yearly') ? 'selected' : '' ?>>Yearly</option>
        </select>
        <?php if (isset($errors['type'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['type']); ?></span><?php endif; ?>
    </label><br>
    
    <label>Start Date:<br>
        <input type="date" name="start_date" required value="<?= isset($old_values['start']) ? htmlspecialchars($old_values['start']) : '' ?>">
        <?php if (isset($errors['start'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['start']); ?></span><?php endif; ?>
    </label><br>
    
    <button id="submitBtn">Add Member</button>
    <input type="hidden" id="formType" value="member">
</form>

<script src="../assets/js/validation.js"></script>
<?php include "../includes/footer.php"; ?>