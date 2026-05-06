<?php
session_start();
require_once "../includes/db_config.php";

if (isset($_SESSION["arzt_id"])) {
    header("Location: doctor_dashboard.php"); exit;
}

function clean_input($v) {
    $v = trim($v);
    $v = preg_replace('/[\x00-\x1F\x7F]/u', '', $v);
    return $v;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = clean_input($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email !== "" && $password !== "") {
        $stmt = mysqli_prepare($conn, "SELECT arzt_id, name, email, passwort, fachbereich_id FROM arzt WHERE email = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $row["passwort"])) {
                $_SESSION["arzt_id"]   = $row["arzt_id"];
                $_SESSION["arzt_name"] = $row["name"];
                $_SESSION["arzt_email"]= $row["email"];
                header("Location: doctor_dashboard.php");
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
<title>Arzt Login — IDAS</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-box">
    <div class="login-logo">ID<span>AS</span></div>
    <div class="login-sub">Arzt Portal</div>

    <?php if ($error): ?>
      <div class="login-error">⚠ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post">
      <div class="login-fg">
        <label>E-Mail</label>
        <input type="email" name="email" required autofocus
          placeholder="arzt@beispiel.de"
          value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
      <div class="login-fg">
        <label>Passwort</label>
        <input type="password" name="password" required placeholder="••••••••">
      </div>
      <button type="submit" class="login-btn">Anmelden</button>
    </form>

    <div style="display:flex;gap:10px;margin-top:16px">
      <a href="../seiten/index.php" class="btn ghost" style="flex:1;text-align:center;text-decoration:none">🏠 Hauptseite</a>
      <a href="admin_login.php" class="btn ghost" style="flex:1;text-align:center;text-decoration:none">🔐 Admin Login</a>
    </div>

  </div>
</div>
</body>
</html>
