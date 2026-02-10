<?php
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validateRequired($value) {
    return !empty(trim($value));
}

function validateMinLength($value, $min) {
    return strlen($value) >= $min;
}

function validateMaxLength($value, $max) {
    return strlen($value) <= $max;
}

function validateNumeric($value) {
    return is_numeric($value);
}

function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)));
}
?>
