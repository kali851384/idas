<?php
session_start();
require_once "../includes/db_config.php";

function clean_input($v) {
    $v = trim($v);
    $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v);
    return $v;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = clean_input($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username !== "" && $password !== "") {

        $sql = "SELECT admin_id, email, passwort FROM admin_account WHERE email = ? LIMIT 1";
        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["passwort"])) {

                $_SESSION["admin_id"] = $row["admin_id"];
                $_SESSION["admin_user"] = $row["email"];

                header("Location: admin_dashboard.php");
                exit;
            }
        }

        $error = "Login falsch.";
    } else {
        $error = "Bitte ausfüllen.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Login</title>
</head>
<body>

<h1>Admin Login</h1>

<p><?php echo htmlspecialchars($error); ?></p>

<form method="post">
    Benutzername:<br>
    <input type="text" name="username"><br><br>

    Passwort:<br>
    <input type="password" name="password"><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
