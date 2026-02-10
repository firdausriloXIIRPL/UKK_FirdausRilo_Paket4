<?php
require_once '../config/database.php';

echo "<h1>🔐 Password Verification Test</h1>";

$database = new Database();
$db = $database->connect();

// Get user
$stmt = $db->prepare("SELECT * FROM users WHERE username = 'admin'");
$stmt->execute();
$user = $stmt->fetch();

echo "<h2>User Data:</h2>";
echo "Username: " . $user['username'] . "<br>";
echo "Password Hash: <code>" . $user['password'] . "</code><br>";
echo "Role: " . $user['role'] . "<br>";
echo "Status: " . $user['status'] . "<br><br>";

// Test password
$test_password = 'admin123';
echo "<h2>Test Password Verify:</h2>";
echo "Input: <code>$test_password</code><br>";

if (password_verify($test_password, $user['password'])) {
    echo "<div style='background:#d1fae5; padding:20px; color:#065f46; margin:20px 0;'>";
    echo "✅ <strong>SUCCESS!</strong> Password cocok!<br>";
    echo "Login seharusnya bisa berhasil.";
    echo "</div>";
} else {
    echo "<div style='background:#fee2e2; padding:20px; color:#991b1b; margin:20px 0;'>";
    echo "❌ <strong>FAILED!</strong> Password tidak cocok!";
    echo "</div>";
}
?>
