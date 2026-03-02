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
                $_SESSION['flash']='Admin erfolgreich erstellt.';$_SESSION['flash_type']='success';header('Location: konto_manager.php'); exit;
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
                $_SESSION['flash']="E-Mail aktualisiert.";$_SESSION['flash_type']="success";header("Location: konto_manager.php"); exit;
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
            $_SESSION['flash']="Passwort geändert.";$_SESSION['flash_type']="success";header("Location: konto_manager.php"); exit;
        }

    } elseif ($act === 'delete') {
        if ($id == $_SESSION["admin_id"]) {
            $msg = "Eigenen Account nicht löschbar."; $msg_type = "error";
        } else {
            $s = mysqli_prepare($conn, "DELETE FROM admin_account WHERE admin_id=?");
            mysqli_stmt_bind_param($s, "i", $id);
            mysqli_stmt_execute($s);
            $_SESSION['flash']="Account gelöscht.";$_SESSION['flash_type']="success";header("Location: konto_manager.php"); exit;
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
<style>
  *{box-sizing:border-box} body{font-family:Arial,sans-serif;max-width:800px;margin:30px auto;padding:0 20px;color:#333}
  h1{color:#2c3e50} h2{color:#34495e;border-bottom:2px solid #eee;padding-bottom:5px;margin-top:28px}
  .msg{padding:10px 16px;border-radius:4px;margin-bottom:16px}
  .success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
  .error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
  table{width:100%;border-collapse:collapse;margin-top:8px}
  th{background:#2c3e50;color:#fff;padding:9px 12px;text-align:left}
  td{padding:8px 12px;border-bottom:1px solid #ddd;vertical-align:middle}
  tr:hover td{background:#f5f5f5}
  .badge{background:#3498db;color:#fff;font-size:11px;padding:2px 7px;border-radius:10px;margin-left:5px}
  .card{background:#f8f9fa;border:1px solid #dee2e6;border-radius:6px;padding:20px;margin-bottom:20px}
  .fg{display:flex;flex-direction:column;gap:4px;flex:1;min-width:170px}
  .frow{display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end;margin-bottom:12px}
  label{font-size:13px;font-weight:bold;color:#555}
  input[type=email],input[type=password]{padding:7px 10px;border:1px solid #ccc;border-radius:4px;font-size:14px;width:100%}
  .btn{padding:6px 14px;border:none;border-radius:4px;cursor:pointer;font-size:13px;line-height:1.4}
  .blue{background:#2980b9;color:#fff} .blue:hover{background:#1f618d}
  .green{background:#27ae60;color:#fff} .green:hover{background:#1e8449}
  .red{background:#c0392b;color:#fff} .red:hover{background:#96281b}
  .grey{background:#7f8c8d;color:#fff} .grey:hover{background:#626567}
  .purple{background:#8e44ad;color:#fff} .purple:hover{background:#6c3483}
  .acts{display:flex;gap:6px;align-items:center;flex-wrap:wrap}
  .ov{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;justify-content:center;align-items:center}
  .ov.on{display:flex}
  .modal{background:#fff;border-radius:8px;padding:24px;width:100%;max-width:400px;position:relative;box-shadow:0 8px 32px rgba(0,0,0,.25)}
  .modal h3{margin:0 0 16px;color:#2c3e50}
  .xbtn{position:absolute;top:10px;right:14px;background:none;border:none;font-size:20px;cursor:pointer;color:#aaa}
  .xbtn:hover{color:#333}
  .mrow{display:flex;gap:8px;margin-top:16px;justify-content:flex-end}
  .mfg{display:flex;flex-direction:column;gap:4px;margin-bottom:12px}
  .mfg label{font-size:13px;font-weight:bold;color:#555}
</style>
</head>
<body>

<h1>🔐 Konto-Verwaltung</h1>
<p>Eingeloggt als <strong><?= htmlspecialchars($_SESSION["admin_user"]) ?></strong> &nbsp;|&nbsp;
   <a href="admin_dashboard.php">← Dashboard</a> &nbsp;|&nbsp; <a href="admin_logout.php">Logout</a></p>

<?php if ($msg): ?>
  <div class="msg <?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
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