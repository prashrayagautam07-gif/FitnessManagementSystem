<?php
// server_validation.php
// Server-side validation functions (include in all form handling files)

/* Sanitize input data to prevent XSS
 */
function sanitizeInput($data) {
    if (empty($data)) return '';
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/* Validate email format
 */
function validateEmail($email) {
    if (empty($email)) {
        return ['valid' => false, 'message' => 'Email is required'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['valid' => false, 'message' => 'Invalid email format'];
    }
    
    if (strlen($email) > 100) {
        return ['valid' => false, 'message' => 'Email too long (max 100 chars)'];
    }
    
    return ['valid' => true, 'message' => 'Email is valid'];
}

/* Validate phone number
 */
function validatePhone($phone) {
    if (empty($phone)) {
        return ['valid' => false, 'message' => 'Phone number is required'];
    }
    
    // Remove non-digit characters
    $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
    
    if (strlen($cleanPhone) < 7 || strlen($cleanPhone) > 15) {
        return ['valid' => false, 'message' => 'Phone number should be 7-15 digits'];
    }
    
    return ['valid' => true, 'message' => 'Phone number is valid'];
}

/* Validate name
 */
function validateName($name) {
    if (empty($name)) {
        return ['valid' => false, 'message' => 'Name is required'];
    }
    
    if (strlen($name) > 100) {
        return ['valid' => false, 'message' => 'Name too long (max 100 chars)'];
    }
    
    // Allow letters, spaces, dots, hyphens, apostrophes
    if (!preg_match('/^[a-zA-Z\s\.\-\']+$/', $name)) {
        return ['valid' => false, 'message' => 'Name can only contain letters, spaces, dots, and hyphens'];
    }
    
    return ['valid' => true, 'message' => 'Name is valid'];
}

/* Validate date (YYYY-MM-DD format)
 */
function validateDate($date) {
    if (empty($date)) {
        return ['valid' => false, 'message' => 'Date is required'];
    }
    
    // Check format
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date) {
        return ['valid' => false, 'message' => 'Invalid date format (use YYYY-MM-DD)'];
    }
    
    // Check if date is realistic
    $minDate = '2000-01-01';
    $maxDate = '2050-12-31';
    
    if ($date < $minDate) {
        return ['valid' => false, 'message' => 'Date cannot be before 2000-01-01'];
    }
    
    if ($date > $maxDate) {
        return ['valid' => false, 'message' => 'Date cannot be after 2050-12-31'];
    }
    
    return ['valid' => true, 'message' => 'Date is valid'];
}

/*
  Validate membership type
 */
function validateMembershipType($type) {
    $allowedTypes = ['Monthly', 'Quarterly', 'Yearly'];
    
    if (empty($type)) {
        return ['valid' => false, 'message' => 'Membership type is required'];
    }
    
    if (!in_array($type, $allowedTypes)) {
        return ['valid' => false, 'message' => 'Invalid membership type'];
    }
    
    return ['valid' => true, 'message' => 'Membership type is valid'];
}

/* Validate numeric input (for sets, reps, etc.)
 */
function validateNumber($number, $fieldName, $min = 1, $max = 100) {
    if (empty($number) && $number !== '0') {
        return ['valid' => false, 'message' => "$fieldName is required"];
    }
    
    if (!is_numeric($number)) {
        return ['valid' => false, 'message' => "$fieldName must be a number"];
    }
    
    $num = (int)$number;
    
    if ($num < $min) {
        return ['valid' => false, 'message' => "$fieldName must be at least $min"];
    }
    
    if ($num > $max) {
        return ['valid' => false, 'message' => "$fieldName cannot exceed $max"];
    }
    
    return ['valid' => true, 'message' => "$fieldName is valid"];
}

/*
  Validate exercise name
 */
function validateExercise($exercise) {
    if (empty($exercise)) {
        return ['valid' => false, 'message' => 'Exercise name is required'];
    }
    
    if (strlen($exercise) > 100) {
        return ['valid' => false, 'message' => 'Exercise name too long (max 100 chars)'];
    }
    
    // Allow letters, numbers, spaces, hyphens
    if (!preg_match('/^[a-zA-Z0-9\s\-]+$/', $exercise)) {
        return ['valid' => false, 'message' => 'Exercise name can only contain letters, numbers, spaces, and hyphens'];
    }
    
    return ['valid' => true, 'message' => 'Exercise name is valid'];
}

/*
  Process all validations and return errors
 */
function validateForm($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        $validation = $rule($value);
        
        if (!$validation['valid']) {
            $errors[$field] = $validation['message'];
        }
    }
    
    return $errors;
}
?>