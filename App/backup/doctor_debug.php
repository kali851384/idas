<?php
require_once "../includes/db_config.php";

$email    = "al.dente@clinic.de";
$password = "Arzt123!";

$stmt = mysqli_prepare($conn, "SELECT arzt_id, name, email, passwort FROM arzt WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

echo "<pre>";
echo "Row found: " . ($row ? "YES" : "NO") . "\n";
if ($row) {
    echo "Hash from DB: " . $row['passwort'] . "\n";
    echo "Password verify: " . (password_verify($password, $row['passwort']) ? "TRUE" : "FALSE") . "\n";
}
echo "</pre>";
