<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act      = $_POST['action'] ?? '';
    $id       = intval($_POST['patient_id'] ?? 0);
    $vorname  = trim($_POST['vorname'] ?? '');
    $nachname = trim($_POST['nachname'] ?? '');
    $geb      = trim($_POST['geburtsdatum'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $tel      = trim($_POST['telefon'] ?? '');
    $geschl   = trim($_POST['geschlecht'] ?? '');
    $wohnort  = trim($_POST['wohnort'] ?? '');
    $plz      = trim($_POST['plz'] ?? '');
    $adresse  = trim($_POST['adresse'] ?? '');

    if ($act === 'delete') {
        $s = mysqli_prepare($conn, "DELETE FROM patient WHERE patient_id=?");
        mysqli_stmt_bind_param($s, "i", $id);
        mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Patient gelöscht."; $_SESSION['flash_type'] = "success";

    } elseif ($act === 'create' || $act === 'update') {
        if ($vorname === '' || $nachname === '') {
            $_SESSION['flash'] = "Vor- und Nachname sind Pflichtfelder."; $_SESSION['flash_type'] = "error";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash'] = "Ungültige E-Mail-Adresse."; $_SESSION['flash_type'] = "error";
        } else {
            // Check duplicate email
            $c = mysqli_prepare($conn, "SELECT patient_id FROM patient WHERE email=?" . ($act==='update' ? " AND patient_id!=?" : ""));
            if ($act === 'update') { mysqli_stmt_bind_param($c, "si", $email, $id); }
            else { mysqli_stmt_bind_param($c, "s", $email); }
            mysqli_stmt_execute($c); mysqli_stmt_store_result($c);

            if (mysqli_stmt_num_rows($c)) {
                $_SESSION['flash'] = "E-Mail bereits vergeben."; $_SESSION['flash_type'] = "error";
            } else {
                if ($act === 'create') {
                    $pw = password_hash("Patient123!", PASSWORD_DEFAULT); // default pw
                    $s = mysqli_prepare($conn, "INSERT INTO patient (vorname,nachname,geburtsdatum,email,telefon,geschlecht,wohnort,plz,adresse,passwort) VALUES (?,?,?,?,?,?,?,?,?,?)");
                    mysqli_stmt_bind_param($s, "ssssssssss", $vorname, $nachname, $geb, $email, $tel, $geschl, $wohnort, $plz, $adresse, $pw);
                    $_SESSION['flash'] = "Patient erstellt. Standard-Passwort: Patient123!";
                } else {
                    $s = mysqli_prepare($conn, "UPDATE patient SET vorname=?,nachname=?,geburtsdatum=?,email=?,telefon=?,geschlecht=?,wohnort=?,plz=?,adresse=? WHERE patient_id=?");
                    mysqli_stmt_bind_param($s, "sssssssssi", $vorname, $nachname, $geb, $email, $tel, $geschl, $wohnort, $plz, $adresse, $id);

                    mysqli_stmt_close($s);
                    $s = mysqli_prepare($conn, "UPDATE patient SET vorname=?,nachname=?,geburtsdatum=?,email=?,telefon=?,geschlecht=?,wohnort=?,plz=?,adresse=? WHERE patient_id=?");
                    mysqli_stmt_bind_param($s, "sssssssssi", $vorname, $nachname, $geb, $email, $tel, $geschl, $wohnort, $plz, $adresse, $id);
                    $_SESSION['flash'] = "Patient aktualisiert.";
                }
                mysqli_stmt_execute($s);
                $_SESSION['flash_type'] = "success";
            }
        }
    } elseif ($act === 'reset_pw') {
        $new_pw = password_hash("Patient123!", PASSWORD_DEFAULT);
        $s = mysqli_prepare($conn, "UPDATE patient SET passwort=? WHERE patient_id=?");
        mysqli_stmt_bind_param($s, "si", $new_pw, $id);
        mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Passwort auf 'Patient123!' zurückgesetzt."; $_SESSION['flash_type'] = "success";
    }
    header("Location: patienten_verwaltung.php"); exit;
}

$flash = $flash_type = "";
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

// Search
$search = trim($_GET['q'] ?? '');
$where  = "";
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where = "WHERE vorname LIKE '%$s%' OR nachname LIKE '%$s%' OR email LIKE '%$s%' OR wohnort LIKE '%$s%'";
}

$patienten = [];
$r = mysqli_query($conn, "SELECT * FROM patient $where ORDER BY nachname, vorname");
while ($row = mysqli_fetch_assoc($r)) $patienten[] = $row;
$total = count($patienten);

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Patienten verwalten</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body class="padded">

<div class="topbar">
  <h1>👤 Patienten verwalten</h1>
  <a href="admin_dashboard.php" class="btn ghost">← Dashboard</a>
  <a href="aerzte_verwaltung.php" class="btn ghost">🩺 Ärzte</a>
</div>

<?php if ($flash): ?>
<div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<div class="toolbar">
  <form class="search-form" method="get">
    <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Name, E-Mail, Wohnort…">
    <button class="btn blue" type="submit">Suchen</button>
    <?php if ($search): ?><a href="patienten_verwaltung.php" class="btn ghost">✕</a><?php endif; ?>
  </form>
  <span class="count-badge"><?= $total ?> Patient<?= $total !== 1 ? 'en' : '' ?></span>
  <button class="btn green" onclick="openModal()">✚ Neuer Patient</button>
</div>

<div class="table-wrap">
<?php if (!$patienten): ?>
  <div class="empty"><p style="font-size:48px">👤</p><p style="margin-top:12px">Keine Patienten gefunden.</p></div>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Geburtsdatum</th>
      <th>E-Mail</th>
      <th>Wohnort</th>
      <th>Telefon</th>
      <th>Aktionen</th>
    </tr>
  </thead>
  <tbody>
  <?php foreach ($patienten as $p):
    $initials = strtoupper(substr($p['vorname'],0,1).substr($p['nachname'],0,1));
    $colors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c','#1abc9c'];
    $color  = $colors[crc32($p['vorname']) % count($colors)];
  ?>
  <tr>
    <td style="color:var(--muted);font-family:monospace">#<?= $p['patient_id'] ?></td>
    <td>
      <div class="name-cell">
        <div class="avatar" style="background:<?= $color ?>"><?= $initials ?></div>
        <div>
          <div style="font-weight:600"><?= htmlspecialchars($p['vorname'].' '.$p['nachname']) ?></div>
          <?php if ($p['geschlecht']): ?>
          <span class="badge-g badge-<?= $p['geschlecht'] ?>"><?= $p['geschlecht']==='m'?'Männlich':'Weiblich' ?></span>
          <?php endif; ?>
        </div>
      </div>
    </td>
    <td><?= $p['geburtsdatum'] ? date('d.m.Y', strtotime($p['geburtsdatum'])) : '—' ?></td>
    <td><?= htmlspecialchars($p['email']) ?></td>
    <td><?= htmlspecialchars($p['wohnort'] ?: '—') ?></td>
    <td><?= htmlspecialchars($p['telefon'] ?: '—') ?></td>
    <td>
      <div class="act-cell">
        <button class="btn blue" style="padding:4px 10px;font-size:12px"
          onclick='openDetail(<?= json_encode($p) ?>)'>👁 Details</button>
        <button class="btn ghost" style="padding:4px 10px;font-size:12px"
          onclick='openModal(<?= json_encode($p) ?>)'>✏️</button>
        <form method="post" onsubmit="return confirm('Patient löschen? Alle zugehörigen Daten werden ebenfalls gelöscht.')" style="margin:0">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="patient_id" value="<?= $p['patient_id'] ?>">
          <button class="btn ghost" style="padding:4px 10px;font-size:12px;color:var(--red);border-color:#f5c6cb">🗑</button>
        </form>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<!-- EDIT / CREATE MODAL -->
<div class="ov" id="editModal">
  <div class="modal">
    <button class="xbtn" type="button" onclick="closeModal('editModal')">✕</button>
    <h3 id="modalTitle">Patient</h3>
    <form method="post">
      <input type="hidden" name="action" id="formAction" value="create">
      <input type="hidden" name="patient_id" id="formId">

      <div class="section-title">Persönliche Daten</div>
      <div class="grid2">
        <div class="fg"><label>Vorname *</label><input type="text" name="vorname" id="fVorname" required maxlength="30"></div>
        <div class="fg"><label>Nachname *</label><input type="text" name="nachname" id="fNachname" required maxlength="30"></div>
        <div class="fg"><label>Geburtsdatum</label><input type="date" name="geburtsdatum" id="fGeb"></div>
        <div class="fg">
          <label>Geschlecht</label>
          <select name="geschlecht" id="fGeschl">
            <option value="">— wählen —</option>
            <option value="m">Männlich</option>
            <option value="w">Weiblich</option>
            <option value="d">Divers</option>
          </select>
        </div>
      </div>

      <div class="section-title">Kontakt</div>
      <div class="grid2">
        <div class="fg"><label>E-Mail *</label><input type="email" name="email" id="fEmail" required></div>
        <div class="fg"><label>Telefon</label><input type="text" name="telefon" id="fTel" placeholder="+49 …"></div>
      </div>

      <div class="section-title">Adresse</div>
      <div class="fg" style="margin-bottom:12px"><label>Straße & Hausnummer</label><input type="text" name="adresse" id="fAdresse" maxlength="70"></div>
      <div class="grid2">
        <div class="fg"><label>PLZ</label><input type="text" name="plz" id="fPlz" maxlength="10"></div>
        <div class="fg"><label>Wohnort</label><input type="text" name="wohnort" id="fWohnort" maxlength="50"></div>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn ghost" onclick="closeModal('editModal')">Abbrechen</button>
        <button type="submit" class="btn green" id="submitBtn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<!-- DETAIL MODAL -->
<div class="ov" id="detailModal">
  <div class="modal">
    <button class="xbtn" type="button" onclick="closeModal('detailModal')">✕</button>
    <h3 id="detailTitle">Patient Details</h3>
    <div class="detail-grid" id="detailContent"></div>
    <div style="margin-top:20px;display:flex;gap:8px;justify-content:space-between;align-items:center">
      <form method="post" onsubmit="return confirm('Passwort auf Standard zurücksetzen?')" id="resetPwForm">
        <input type="hidden" name="action" value="reset_pw">
        <input type="hidden" name="patient_id" id="detailPwId">
        <button class="btn purple" type="submit">🔑 Passwort zurücksetzen</button>
      </form>
      <button class="btn ghost" onclick="closeModal('detailModal')">Schließen</button>
    </div>
  </div>
</div>

<script>
function openModal(p) {
  const create = !p;
  document.getElementById('modalTitle').textContent  = create ? '✚ Neuer Patient' : '✏️ Patient bearbeiten';
  document.getElementById('formAction').value = create ? 'create' : 'update';
  document.getElementById('formId').value     = p ? p.patient_id   : '';
  document.getElementById('fVorname').value   = p ? p.vorname       : '';
  document.getElementById('fNachname').value  = p ? p.nachname      : '';
  document.getElementById('fGeb').value       = p ? (p.geburtsdatum||'') : '';
  document.getElementById('fGeschl').value    = p ? (p.geschlecht||'')   : '';
  document.getElementById('fEmail').value     = p ? p.email         : '';
  document.getElementById('fTel').value       = p ? (p.telefon||'') : '';
  document.getElementById('fAdresse').value   = p ? (p.adresse||'') : '';
  document.getElementById('fPlz').value       = p ? (p.plz||'')     : '';
  document.getElementById('fWohnort').value   = p ? (p.wohnort||'') : '';
  document.getElementById('editModal').classList.add('on');
  setTimeout(() => document.getElementById('fVorname').focus(), 50);
}

function openDetail(p) {
  document.getElementById('detailTitle').textContent = p.vorname + ' ' + p.nachname;
  document.getElementById('detailPwId').value = p.patient_id;
  const fields = [
    ['Patient-ID', '#' + p.patient_id],
    ['Vorname',    p.vorname],
    ['Nachname',   p.nachname],
    ['Geburtsdatum', p.geburtsdatum ? formatDate(p.geburtsdatum) : '—'],
    ['Geschlecht', p.geschlecht === 'm' ? 'Männlich' : p.geschlecht === 'w' ? 'Weiblich' : (p.geschlecht || '—')],
    ['E-Mail',     p.email],
    ['Telefon',    p.telefon || '—'],
    ['Adresse',    p.adresse || '—'],
    ['PLZ',        p.plz || '—'],
    ['Wohnort',    p.wohnort || '—'],
  ];
  document.getElementById('detailContent').innerHTML = fields.map(([l,v]) =>
    `<div class="detail-row"><span class="detail-label">${l}</span><span class="detail-val">${v}</span></div>`
  ).join('');
  document.getElementById('detailModal').classList.add('on');
}

function closeModal(id) { document.getElementById(id).classList.remove('on'); }
document.querySelectorAll('.ov').forEach(o => o.addEventListener('click', e => { if (e.target===o) o.classList.remove('on'); }));
document.addEventListener('keydown', e => { if (e.key==='Escape') document.querySelectorAll('.ov.on').forEach(o=>o.classList.remove('on')); });

function formatDate(d) {
  if (!d) return '—';
  const parts = d.split('-');
  return parts[2] + '.' + parts[1] + '.' + parts[0];
}
</script>

<hr style="margin-top:40px;border:none;border-top:1px solid var(--border)">
<p style="margin-top:12px;font-size:12px;color:var(--muted)">Patienten verwalten – <?= date('d.m.Y H:i') ?></p>
</body>
</html>
