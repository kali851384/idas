<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

$msg = $msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['action'] ?? '';
    $id  = intval($_POST['admin_id'] ?? 0);

    if ($act === 'create') {
        $email = trim($_POST['email'] ?? '');
        $pw    = $_POST['password'] ?? '';
        if ($email === '' || $pw === '') {
            $msg = "Bitte alle Felder ausfüllen."; $msg_type = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Ungültige E-Mail."; $msg_type = "error";
        } elseif (strlen($pw) < 8) {
            $msg = "Passwort min. 8 Zeichen."; $msg_type = "error";
        } elseif ($pw !== ($_POST['password_confirm'] ?? '')) {
            $msg = "Passwörter stimmen nicht überein."; $msg_type = "error";
        } else {
            $c = mysqli_prepare($conn, "SELECT admin_id FROM admin_account WHERE email=?");
            mysqli_stmt_bind_param($c, "s", $email);
            mysqli_stmt_execute($c);
            mysqli_stmt_store_result($c);
            if (mysqli_stmt_num_rows($c)) {
                $msg = "E-Mail bereits vergeben."; $msg_type = "error";
            } else {
                $s = mysqli_prepare($conn, "INSERT INTO admin_account (email,passwort) VALUES (?,?)");
                mysqli_stmt_bind_param($s, "ss", $email, password_hash($pw, PASSWORD_DEFAULT));
                mysqli_stmt_execute($s);
                $_SESSION['flash']='Admin erfolgreich erstellt.';$_SESSION['flash_type']='success';header('Location: konto_verwaltung.php'); exit;
            }
        }

    } elseif ($act === 'update_email') {
        $email = trim($_POST['email'] ?? '');
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $msg = "Ungültige E-Mail."; $msg_type = "error";
        } else {
            $c = mysqli_prepare($conn, "SELECT admin_id FROM admin_account WHERE email=? AND admin_id!=?");
            mysqli_stmt_bind_param($c, "si", $email, $id);
            mysqli_stmt_execute($c);
            mysqli_stmt_store_result($c);
            if (mysqli_stmt_num_rows($c)) {
                $msg = "E-Mail bereits vergeben."; $msg_type = "error";
            } else {
                $s = mysqli_prepare($conn, "UPDATE admin_account SET email=? WHERE admin_id=?");
                mysqli_stmt_bind_param($s, "si", $email, $id);
                mysqli_stmt_execute($s);
                if ($id == $_SESSION["admin_id"]) $_SESSION["admin_user"] = $email;
                $_SESSION['flash']="E-Mail aktualisiert.";$_SESSION['flash_type']="success";header("Location: konto_verwaltung.php"); exit;
            }
        }

    } elseif ($act === 'update_password') {
        $pw = $_POST['new_password'] ?? '';
        $ok = true;
        if (strlen($pw) < 8) {
            $msg = "Passwort min. 8 Zeichen."; $msg_type = "error"; $ok = false;
        } elseif ($pw !== ($_POST['confirm_password'] ?? '')) {
            $msg = "Passwörter stimmen nicht überein."; $msg_type = "error"; $ok = false;
        } elseif ($id == $_SESSION["admin_id"]) {
            $s = mysqli_prepare($conn, "SELECT passwort FROM admin_account WHERE admin_id=?");
            mysqli_stmt_bind_param($s, "i", $id);
            mysqli_stmt_execute($s);
            $row = mysqli_fetch_assoc(mysqli_stmt_get_result($s));
            if (!password_verify($_POST['current_password'] ?? '', $row['passwort'])) {
                $msg = "Aktuelles Passwort falsch."; $msg_type = "error"; $ok = false;
            }
        }
        if ($ok) {
            $s = mysqli_prepare($conn, "UPDATE admin_account SET passwort=? WHERE admin_id=?");
            mysqli_stmt_bind_param($s, "si", password_hash($pw, PASSWORD_DEFAULT), $id);
            mysqli_stmt_execute($s);
            $_SESSION['flash']="Passwort geändert.";$_SESSION['flash_type']="success";header("Location: konto_verwaltung.php"); exit;
        }

    } elseif ($act === 'delete') {
        if ($id == $_SESSION["admin_id"]) {
            $msg = "Eigenen Account nicht löschbar."; $msg_type = "error";
        } else {
            $s = mysqli_prepare($conn, "DELETE FROM admin_account WHERE admin_id=?");
            mysqli_stmt_bind_param($s, "i", $id);
            mysqli_stmt_execute($s);
            $_SESSION['flash']="Account gelöscht.";$_SESSION['flash_type']="success";header("Location: konto_mverwaltung.php"); exit;
        }
    }
}

if (!$msg && isset($_SESSION['flash'])) {
    $msg      = $_SESSION['flash'];
    $msg_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

$admins = [];
$r = mysqli_query($conn, "SELECT admin_id, email FROM admin_account ORDER BY admin_id");
while ($row = mysqli_fetch_assoc($r)) $admins[] = $row;
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Konto-Verwaltung</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body class="padded">

<h1>🔐 Konto-Verwaltung</h1>
<p>Eingeloggt als <strong><?= htmlspecialchars($_SESSION["admin_user"]) ?></strong> &nbsp;|&nbsp;
   <a href="admin_dashboard.php">← Dashboard</a> &nbsp;|&nbsp; <a href="admin_logout.php">Logout</a></p>

<?php if ($msg): ?>
  <div class="flash <?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>Neuen Admin erstellen</h2>
<div class="card">
  <form method="post">
    <input type="hidden" name="action" value="create">
    <div class="frow">
      <div class="fg" style="flex:2"><label>E-Mail</label><input type="email" name="email" required placeholder="admin@beispiel.de"></div>
    </div>
    <div class="frow">
      <div class="fg"><label>Passwort (min. 8)</label><input type="password" name="password" required minlength="8"></div>
      <div class="fg"><label>Bestätigen</label><input type="password" name="password_confirm" required minlength="8"></div>
    </div>
    <button class="btn green">✚ Erstellen</button>
  </form>
</div>

<h2>Alle Admins</h2>
<table>
  <thead><tr><th>ID</th><th>E-Mail</th><th>Aktionen</th></tr></thead>
  <tbody>
  <?php foreach ($admins as $a):
    $self = $a['admin_id'] == $_SESSION["admin_id"]; ?>
  <tr>
    <td><?= $a['admin_id'] ?></td>
    <td><?= htmlspecialchars($a['email']) ?><?php if ($self) echo "<span class='badge'>Ich</span>"; ?></td>
    <td>
      <div class="acts">
        <button class="btn blue" onclick="openEmail(<?= $a['admin_id'] ?>,'<?= htmlspecialchars($a['email'], ENT_QUOTES) ?>')">✏️ E-Mail</button>
        <button class="btn purple" onclick="openPw(<?= $a['admin_id'] ?>,<?= $self ? 'true' : 'false' ?>)">🔑 Passwort</button>
        <?php if (!$self): ?>
        <form method="post" onsubmit="return confirm('Löschen?')" style="display:inline;margin:0">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="admin_id" value="<?= $a['admin_id'] ?>">
          <button class="btn red">🗑 Löschen</button>
        </form>
        <?php endif; ?>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="ov" id="emModal">
  <div class="modal">
    <button class="xbtn" type="button" onclick="closeModal('emModal')">✕</button>
    <h3>✏️ E-Mail bearbeiten</h3>
    <form method="post">
      <input type="hidden" name="action" value="update_email">
      <input type="hidden" name="admin_id" id="emId">
      <div class="mfg"><label>Neue E-Mail</label><input type="email" name="email" id="emInput" required></div>
      <div class="mrow">
        <button type="button" class="btn grey" onclick="closeModal('emModal')">Abbrechen</button>
        <button class="btn blue">💾 Speichern</button>
      </div>
    </form>
  </div>
</div>

<div class="ov" id="pwModal">
  <div class="modal">
    <button class="xbtn" type="button" onclick="closeModal('pwModal')">✕</button>
    <h3>🔑 Passwort ändern</h3>
    <form method="post">
      <input type="hidden" name="action" value="update_password">
      <input type="hidden" name="admin_id" id="pwId">
      <div class="mfg" id="curGrp" style="display:none"><label>Aktuelles Passwort</label><input type="password" name="current_password" id="curPw"></div>
      <div class="mfg"><label>Neues Passwort (min. 8)</label><input type="password" name="new_password" id="newPw" required minlength="8"></div>
      <div class="mfg"><label>Bestätigen</label><input type="password" name="confirm_password" required minlength="8"></div>
      <div class="mrow">
        <button type="button" class="btn grey" onclick="closeModal('pwModal')">Abbrechen</button>
        <button class="btn blue">💾 Ändern</button>
      </div>
    </form>
  </div>
</div>

<script>
function openEmail(id, email) {
  document.getElementById('emId').value = id;
  document.getElementById('emInput').value = email;
  openModal('emModal');
  setTimeout(() => document.getElementById('emInput').focus(), 50);
}
function openPw(id, isSelf) {
  document.getElementById('pwId').value = id;
  document.getElementById('curGrp').style.display = isSelf ? 'block' : 'none';
  document.getElementById('curPw').required = isSelf;
  document.getElementById('curPw').value = '';
  document.getElementById('newPw').value = '';
  openModal('pwModal');
  setTimeout(() => document.getElementById(isSelf ? 'curPw' : 'newPw').focus(), 50);
}
function openModal(id)  { document.getElementById(id).classList.add('on'); }
function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.ov').forEach(o =>
  o.addEventListener('click', e => { if (e.target === o) o.classList.remove('on'); })
);
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') document.querySelectorAll('.ov.on').forEach(o => o.classList.remove('on'));
});
</script>

<hr style="margin-top:40px">
<p><small>Konto-Verwaltung – <?= date('d.m.Y H:i') ?></small></p>
</body>
</html>