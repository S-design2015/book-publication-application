<?php
// TEMPORARY: Run once to get hashed password
$plain_password = 'admin123';  // Change this to your desired password
$hashed = password_hash($plain_password, PASSWORD_DEFAULT);
echo "Copy this hashed password:<br>";
echo "<strong>" . $hashed . "</strong><br>";
echo "<br>Plain password: " . $plain_password;
?>
