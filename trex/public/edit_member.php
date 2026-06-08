<?php
include "auth.php";
include "../config/db.php";
include "server_validation.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid member ID.");

$errors = [];
$old_values = [];

// Get current data
$stmt = $conn->prepare("SELECT name, email, phone, membership_type, start_date FROM members WHERE member_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if (!$row) die("Member not found.");

// Initialize old values with current data
$old_values = [
    'name' => $row['name'],
    'email' => $row['email'],
    'phone' => $row['phone'],
    'type' => $row['membership_type']
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = sanitizeInput($_POST['phone']);
    $membership_type = $_POST['membership_type'];
    
    // Update old values with new input
    $old_values = ['name' => $name, 'email' => $email, 'phone' => $phone, 'type' => $membership_type];
    
    // Validate
    $name_validation = validateName($name);
    if (!$name_validation['valid']) $errors['name'] = $name_validation['message'];
    
    $email_validation = validateEmail($email);
    if (!$email_validation['valid']) $errors['email'] = $email_validation['message'];
    
    $phone_validation = validatePhone($phone);
    if (!$phone_validation['valid']) $errors['phone'] = $phone_validation['message'];
    
    $type_validation = validateMembershipType($membership_type);
    if (!$type_validation['valid']) $errors['type'] = $type_validation['message'];
    
    // Check for duplicate email/phone (excluding current member)
    if (empty($errors)) {
        $check_stmt = $conn->prepare("SELECT member_id FROM members WHERE email = ? AND member_id != ?");
        $check_stmt->bind_param("si", $email, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['email'] = "Email already exists in system";
        $check_stmt->close();
        
        $check_stmt = $conn->prepare("SELECT member_id FROM members WHERE phone = ? AND member_id != ?");
        $check_stmt->bind_param("si", $phone, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) $errors['phone'] = "Phone number already exists in system";
        $check_stmt->close();
    }
    
    if (empty($errors)) {
        if ($membership_type == "Monthly") {
            $expiry = date('Y-m-d', strtotime("+1 month", strtotime($row['start_date'])));
        } elseif ($membership_type == "Quarterly") {
            $expiry = date('Y-m-d', strtotime("+3 month", strtotime($row['start_date'])));
        } else {
            $expiry = date('Y-m-d', strtotime("+1 year", strtotime($row['start_date'])));
        }
        
        $stmt = $conn->prepare("UPDATE members SET name = ?, email = ?, phone = ?, membership_type = ?, expiry_date = ? WHERE member_id = ?");
        $stmt->bind_param("sssssi", $name, $email, $phone, $membership_type, $expiry, $id);

        if ($stmt->execute()) {
            header("Location: index.php?updated=1");
            exit;
        } else {
            $db_error = "Update failed: " . $conn->error;
        }
    }
}

include "../includes/header.php";
?>

<h2>Edit Member: <?= htmlspecialchars($row['name']) ?></h2>

<?php if (isset($db_error)): ?>
    <div class="error-message"><strong>Error:</strong> <?php echo htmlspecialchars($db_error); ?></div>
<?php endif; ?>

<form method="POST">
    <label>Name:<br>
        <input type="text" name="name" value="<?= htmlspecialchars($old_values['name']) ?>" required>
        <?php if (isset($errors['name'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['name']); ?></span><?php endif; ?>
    </label><br>

    <label>Email:<br>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($old_values['email']) ?>" required>
        <span id="email-msg"></span>
        <?php if (isset($errors['email'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['email']); ?></span><?php endif; ?><br>
    </label>

    <label>Phone:<br>
        <input type="text" name="phone" id="phone" value="<?= htmlspecialchars($old_values['phone']) ?>" required>
        <span id="phone-msg"></span>
        <?php if (isset($errors['phone'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['phone']); ?></span><?php endif; ?><br>
    </label>

    <label>Membership Type:<br>
        <select name="membership_type" required>
            <option value="Monthly" <?= $old_values['type'] == 'Monthly' ? 'selected' : '' ?>>Monthly</option>
            <option value="Quarterly" <?= $old_values['type'] == 'Quarterly' ? 'selected' : '' ?>>Quarterly</option>
            <option value="Yearly" <?= $old_values['type'] == 'Yearly' ? 'selected' : '' ?>>Yearly</option>
        </select>
        <?php if (isset($errors['type'])): ?><span class="validation-error"><?php echo htmlspecialchars($errors['type']); ?></span><?php endif; ?>
    </label><br>

    <button type="submit" id="submitBtn">Update</button>
    
    <input type="hidden" id="formType" value="member">
    <input type="hidden" id="currentId" value="<?= $id ?>">
</form>

<script src="../assets/js/validation.js"></script>
<?php include "../includes/footer.php"; ?>