<?php
require_once "../includes/db_config.php";

$username = "admin";
$password = "1234";

$hash = password_hash($password, PASSWORD_DEFAULT);

mysqli_query($conn,
    "INSERT INTO admin_account (email, passwort)
     VALUES ('$username', '$hash')"
);

echo "Admin erstellt";
