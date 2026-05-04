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
        $stmt = mysqli_prepare($conn, "SELECT admin_id, email, passwort FROM admin_account WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["passwort"])) {
                $_SESSION["admin_id"]   = $row["admin_id"];
                $_SESSION["admin_user"] = $row["email"];
                header("Location: admin_dashboard.php");
                exit;
            }
        }
        $error = "E-Mail oder Passwort falsch.";
    } else {
        $error = "Bitte alle Felder ausfüllen.";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — IDAS</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body>

<div class="login-wrap">
  <div class="login-box">

    <div class="login-logo">ID<span>AS</span></div>
    <div class="login-sub">Admin Panel</div>

    <?php if ($error): ?>
      <div class="login-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="login-fg">
        <label>E-Mail</label>
        <input type="email" name="username" required autofocus
          placeholder="admin@beispiel.de"
          value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      </div>
      <div class="login-fg">
        <label>Passwort</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <a href = "../seiten/index.php">Hauptseite</a>
      <button type="submit" class="login-btn">Anmelden</button>
    </form>

  </div>
</div>

</body>
</html>
