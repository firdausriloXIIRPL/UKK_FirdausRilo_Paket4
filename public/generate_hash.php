<?php
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h1>🔐 Password Hash Generator</h1>";
echo "<div style='background:#f0f0f0; padding:20px; border:2px solid #333; margin:20px;'>";
echo "<h2>Generated Hash:</h2>";
echo "<p>Password: <strong>$password</strong></p>";
echo "<p>Hash:</p>";
echo "<textarea style='width:100%; height:80px; padding:10px; font-family:monospace;' onclick='this.select()'>$hash</textarea>";
echo "</div>";

echo "<div style='background:#dbeafe; padding:20px; border-left:4px solid #2563eb; margin:20px;'>";
echo "<h2>📝 Langkah Update:</h2>";
echo "<ol style='line-height:2;'>";
echo "<li>Copy hash di atas (klik di textarea)</li>";
echo "<li>Buka phpMyAdmin</li>";
echo "<li>Database <strong>perpusrlo</strong> → Tab <strong>SQL</strong></li>";
echo "<li>Paste query ini:</li>";
echo "</ol>";
echo "<textarea style='width:100%; height:100px; padding:10px; font-family:monospace;' onclick='this.select()'>UPDATE users SET password = '$hash' WHERE username = 'admin';</textarea>";
echo "<br><br>";
echo "<button onclick='navigator.clipboard.writeText(this.previousElementSibling.value); alert(\"Query copied!\");' style='padding:10px 20px; background:#10b981; color:white; border:none; border-radius:5px; cursor:pointer;'>📋 Copy Query</button>";
echo "</div>";

// Test verify
echo "<div style='background:#fff3cd; padding:20px; border-left:4px solid #f59e0b; margin:20px;'>";
echo "<h2>🧪 Verification Test:</h2>";
if (password_verify($password, $hash)) {
    echo "<p style='color:#065f46; font-weight:bold;'>✅ Hash verified successfully!</p>";
} else {
    echo "<p style='color:#991b1b; font-weight:bold;'>❌ Hash verification failed!</p>";
}
echo "</div>";
?>
