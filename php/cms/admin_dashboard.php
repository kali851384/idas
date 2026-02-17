<?php
session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Bereich</title>
</head>
<body>

<h1>Willkommen <?php echo htmlspecialchars($_SESSION["admin_user"]); ?></h1>

<p>Du bist eingeloggt.</p>

<a href="admin_logout.php">Logout</a>

</body>
</html>
