<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id = $_SESSION["arzt_id"];
$flash = $flash_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['action'] ?? '';

    if ($act === 'update_profile') {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $telefon = trim($_POST['telefonnummer'] ?? '');
        $adresse = trim($_POST['addresse'] ?? '');
        if ($name && $email) {
            $s = mysqli_prepare($conn, "UPDATE arzt SET name=?, email=?, telefonnummer=?, addresse=? WHERE arzt_id=?");
            mysqli_stmt_bind_param($s, "ssssi", $name, $email, $telefon, $adresse, $arzt_id);
            mysqli_stmt_execute($s);
            $_SESSION['arzt_name']  = $name;
            $_SESSION['arzt_email'] = $email;
            $_SESSION['flash'] = "Profil aktualisiert."; $_SESSION['flash_type'] = "success";
        }

    } elseif ($act === 'change_password') {
        $current = $_POST['current_password'] ?? '';
        $new     = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $r = mysqli_prepare($conn, "SELECT passwort FROM arzt WHERE arzt_id=?");
        mysqli_stmt_bind_param($r, "i", $arzt_id);
        mysqli_stmt_execute($r);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($r));
        if (!password_verify($current, $row['passwort'])) {
            $_SESSION['flash'] = "Aktuelles Passwort falsch."; $_SESSION['flash_type'] = "error";
        } elseif (strlen($new) < 6) {
            $_SESSION['flash'] = "Neues Passwort min. 6 Zeichen."; $_SESSION['flash_type'] = "error";
        } elseif ($new !== $confirm) {
            $_SESSION['flash'] = "Passwörter stimmen nicht überein."; $_SESSION['flash_type'] = "error";
        } else {
            $hash = password_hash($new, PASSWORD_DEFAULT);
            $s = mysqli_prepare($conn, "UPDATE arzt SET passwort=? WHERE arzt_id=?");
            mysqli_stmt_bind_param($s, "si", $hash, $arzt_id);
            mysqli_stmt_execute($s);
            $_SESSION['flash'] = "Passwort geändert."; $_SESSION['flash_type'] = "success";
        }
    }
    header("Location: doctor_profil.php"); exit;
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

$r = mysqli_prepare($conn, "SELECT * FROM arzt WHERE arzt_id=?");
mysqli_stmt_bind_param($r, "i", $arzt_id);
mysqli_stmt_execute($r);
$arzt = mysqli_fetch_assoc(mysqli_stmt_get_result($r));
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Mein Profil — IDAS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="cms_style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-logo"><div class="logo-word">ID<span>AS</span></div><div class="sidebar-sub">Arzt Portal</div></div>
    <nav>
      <div class="nav-label">Übersicht</div>
      <a class="nav-link" href="doctor_dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <div class="nav-label">Meine Daten</div>
      <a class="nav-link" href="doctor_termine.php"><span class="icon">📅</span> Termine</a>
      <a class="nav-link" href="doctor_patienten.php"><span class="icon">👤</span> Meine Patienten</a>
      <a class="nav-link" href="doctor_verfuegbarkeit.php"><span class="icon">🕐</span> Verfügbarkeit</a>
      <a class="nav-link active" href="doctor_profil.php"><span class="icon">⚙️</span> Mein Profil</a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($_SESSION["arzt_name"], 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION["arzt_name"]) ?></div>
          <div class="user-role">Arzt</div>
        </div>
      </div>
      <a href="doctor_logout.php">⎋ Logout</a>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Mein Profil</div>
        <div class="page-subtitle">Persönliche Daten &amp; Passwort</div>
      </div>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="panels">

      <!-- Profile edit -->
      <div class="panel" style="flex:1.5">
        <div class="panel-head"><span class="panel-title">👤 Profil bearbeiten</span></div>
        <div class="panel-body">
          <form method="post">
            <input type="hidden" name="action" value="update_profile">
            <div class="grid2">
              <div class="fg">
                <label>Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($arzt['name']) ?>" required>
              </div>
              <div class="fg">
                <label>E-Mail *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($arzt['email']) ?>" required>
              </div>
              <div class="fg">
                <label>Telefon</label>
                <input type="text" name="telefonnummer" value="<?= htmlspecialchars($arzt['telefonnummer'] ?? '') ?>">
              </div>
              <div class="fg">
                <label>Adresse</label>
                <input type="text" name="addresse" value="<?= htmlspecialchars($arzt['addresse'] ?? '') ?>">
              </div>
            </div>
            <button type="submit" class="btn green">💾 Speichern</button>
          </form>
        </div>
      </div>

      <!-- Password change -->
      <div class="panel" style="flex:1">
        <div class="panel-head"><span class="panel-title">🔑 Passwort ändern</span></div>
        <div class="panel-body">
          <form method="post">
            <input type="hidden" name="action" value="change_password">
            <div class="fg">
              <label>Aktuelles Passwort</label>
              <input type="password" name="current_password" required>
            </div>
            <div class="fg">
              <label>Neues Passwort</label>
              <input type="password" name="new_password" required minlength="6">
            </div>
            <div class="fg">
              <label>Bestätigen</label>
              <input type="password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn blue">🔑 Passwort ändern</button>
          </form>
        </div>
      </div>

    </div>
  </main>
</div>
</body>
</html>
