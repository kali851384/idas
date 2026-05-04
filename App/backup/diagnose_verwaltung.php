<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

// Add missing columns to existing diagnose table
mysqli_query($conn, "ALTER TABLE diagnose ADD COLUMN IF NOT EXISTS name VARCHAR(100) NOT NULL DEFAULT ''");
mysqli_query($conn, "ALTER TABLE diagnose ADD COLUMN IF NOT EXISTS beschreibung TEXT DEFAULT NULL");

/* ── POST ── */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act    = $_POST['action']         ?? '';
    $id     = intval($_POST['diagnose_id']  ?? 0);
    $pid    = intval($_POST['patient_id']   ?? 0);
    $name   = trim($_POST['name']      ?? '');
    $datum  = trim($_POST['datum']     ?? '');
    $beschr = trim($_POST['beschreibung']   ?? '');

    if ($act === 'create') {
        if ($pid <= 0 || $name === '' || $datum === '') {
            $_SESSION['flash'] = "Patient, Name und Datum sind Pflichtfelder.";
            $_SESSION['flash_type'] = "error";
        } else {
            $s = mysqli_prepare($conn, "INSERT INTO diagnose (patient_id, name, beschreibung, datum) VALUES (?,?,?,?)");
            mysqli_stmt_bind_param($s, "isss", $pid, $name, $beschr, $datum);
            mysqli_stmt_execute($s);
            $_SESSION['flash'] = "Diagnose \"$name\" hinzugefügt.";
            $_SESSION['flash_type'] = "success";
        }

    } elseif ($act === 'update') {
        if ($pid <= 0 || $name === '' || $datum === '') {
            $_SESSION['flash'] = "Patient, Name und Datum sind Pflichtfelder.";
            $_SESSION['flash_type'] = "error";
        } else {
            $s = mysqli_prepare($conn, "UPDATE diagnose SET patient_id=?, name=?, beschreibung=?, datum=? WHERE diagnose_id=?");
            mysqli_stmt_bind_param($s, "isssi", $pid, $name, $beschr, $datum, $id);
            mysqli_stmt_execute($s);
            $_SESSION['flash'] = "Diagnose aktualisiert.";
            $_SESSION['flash_type'] = "success";
        }

    } elseif ($act === 'delete') {
        $d = mysqli_prepare($conn, "DELETE FROM diagnosedet WHERE diagnose_id=?");
        mysqli_stmt_bind_param($d, "i", $id); mysqli_stmt_execute($d);
        $s = mysqli_prepare($conn, "DELETE FROM diagnose WHERE diagnose_id=?");
        mysqli_stmt_bind_param($s, "i", $id); mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Diagnose gelöscht.";
        $_SESSION['flash_type'] = "success";
    }

    header("Location: diagnose_verwaltung.php"); exit;
}

/* ── Flash ── */
$flash = $flash_type = "";
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

/* ── Filters ── */
$search    = trim($_GET['q'] ?? '');
$filterPat = intval($_GET['patient_id'] ?? 0);

$conds = ["d.name != ''"];
if ($filterPat > 0) $conds[] = "d.patient_id = $filterPat";
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $conds[] = "(d.name LIKE '%$esc%' OR d.beschreibung LIKE '%$esc%' OR p.vorname LIKE '%$esc%' OR p.nachname LIKE '%$esc%')";
}
$where = "WHERE " . implode(" AND ", $conds);

/* ── Load diagnoses ── */
$diagnosen = [];
$r = mysqli_query($conn,
    "SELECT d.diagnose_id, d.name, d.beschreibung, d.datum, d.patient_id,
            p.vorname, p.nachname
     FROM diagnose d
     JOIN patient p ON d.patient_id = p.patient_id
     $where
     ORDER BY d.datum DESC, d.name ASC");
while ($row = mysqli_fetch_assoc($r)) $diagnosen[] = $row;

/* ── Patients for dropdown ── */
$patienten = [];
$r2 = mysqli_query($conn, "SELECT patient_id, vorname, nachname FROM patient ORDER BY nachname, vorname");
while ($row = mysqli_fetch_assoc($r2)) $patienten[] = $row;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM diagnose WHERE name != ''"))['c'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Diagnosen — IDAS CMS</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body class="padded">

<div class="topbar">
  <h1>🏥 Diagnosen</h1>
  <a href="patienten_verwaltung.php" class="btn ghost">👤 Patienten</a>
  <a href="symptome_verwaltung.php"  class="btn ghost">🔬 Symptome</a>
  <a href="admin_dashboard.php"      class="btn ghost">← Dashboard</a>
  <button class="btn green" id="btnNeu">＋ Neue Diagnose</button>
</div>

<?php if ($flash): ?>
<div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
<?php endif; ?>

<form method="get" style="margin:0">
<div class="toolbar">
    <div class="search-form">
      <input type="search" name="q" value="<?= htmlspecialchars($search) ?>"
        placeholder="🔍 Name, Patient…">
      <button class="btn blue" type="submit">Suchen</button>
    </div>
    <select class="filter-sel" name="patient_id" onchange="this.form.submit()">
      <option value="0">Alle Patienten</option>
      <?php foreach ($patienten as $p): ?>
      <option value="<?= $p['patient_id'] ?>" <?= $filterPat===$p['patient_id']?'selected':'' ?>>
        <?= htmlspecialchars($p['nachname'].', '.$p['vorname']) ?>
      </option>
      <?php endforeach; ?>
    </select>
    <?php if ($search || $filterPat): ?>
      <a href="diagnose_verwaltung.php" class="btn ghost">✕</a>
    <?php endif; ?>
    <span class="count-badge"><?= $total ?> Diagnose<?= $total!==1?'n':'' ?></span>
</div>
</form>

<div class="table-wrap">
<?php if (!$diagnosen): ?>
  <div class="empty">
    <p style="font-size:40px">🏥</p>
    <p style="margin-top:10px">
      <?= ($search||$filterPat) ? 'Keine Diagnosen gefunden.' : 'Noch keine Diagnosen eingetragen.' ?>
    </p>
    <?php if (!$search && !$filterPat): ?>
      <button class="btn green" id="btnNeu2" style="margin-top:14px">＋ Erste Diagnose hinzufügen</button>
    <?php endif; ?>
  </div>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Patient</th>
      <th>Diagnose</th>
      <th>Datum</th>
      <th>Beschreibung</th>
      <th>Aktionen</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $avColors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c','#1abc9c','#2980b9','#16a085'];
  foreach ($diagnosen as $d):
    $initials = strtoupper(substr($d['vorname'],0,1).substr($d['nachname'],0,1));
    $aColor   = $avColors[$d['patient_id'] % count($avColors)];
  ?>
  <tr>
    <td><span class="id-chip">#<?= $d['diagnose_id'] ?></span></td>
    <td>
      <div class="name-cell">
        <div class="avatar" style="background:<?= $aColor ?>"><?= $initials ?></div>
        <div class="pat-name"><?= htmlspecialchars($d['vorname'].' '.$d['nachname']) ?></div>
      </div>
    </td>
    <td><div class="diag-name"><?= htmlspecialchars($d['name']) ?></div></td>
    <td><?= $d['datum'] ? date('d.m.Y', strtotime($d['datum'])) : '—' ?></td>
    <td>
      <?php if ($d['beschreibung']): ?>
        <div class="diag-desc"><?= htmlspecialchars($d['beschreibung']) ?></div>
      <?php else: ?>
        <span style="font-style:italic;color:var(--muted)">—</span>
      <?php endif; ?>
    </td>
    <td>
      <div class="act-cell">
        <button class="btn blue edit-btn" style="padding:4px 10px;font-size:12px"
          data-d='<?= htmlspecialchars(json_encode([
            "diagnose_id"  => $d["diagnose_id"],
            "patient_id"   => $d["patient_id"],
            "name"         => $d["name"],
            "datum"        => $d["datum"],
            "beschreibung" => $d["beschreibung"] ?? ""
          ]), ENT_QUOTES) ?>'>✏️ Bearbeiten</button>
        <form method="post"
          onsubmit="return confirm('Diagnose löschen?')"
          style="margin:0">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="diagnose_id" value="<?= $d['diagnose_id'] ?>">
          <button class="btn ghost"
            style="padding:4px 10px;font-size:12px;color:var(--red);border-color:#f5c6cb">🗑</button>
        </form>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<!-- MODAL -->
<div class="ov" id="modal">
  <div class="modal">
    <button class="xbtn" id="btnClose">✕</button>
    <h3 id="mTitle">Neue Diagnose</h3>
    <form method="post">
      <input type="hidden" name="action"      id="mAction" value="create">
      <input type="hidden" name="diagnose_id" id="mId"     value="">

      <div class="section-title">Patient</div>
      <div class="fg">
        <label>Patient *</label>
        <select name="patient_id" id="mPat" required>
          <option value="">— Patient wählen —</option>
          <?php foreach ($patienten as $p): ?>
          <option value="<?= $p['patient_id'] ?>">
            <?= htmlspecialchars($p['nachname'].', '.$p['vorname']) ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="section-title">Diagnose</div>
      <div class="g2">
        <div class="fg">
          <label>Name *</label>
          <input type="text" name="name" id="mName" required maxlength="100"
            placeholder="z.B. Diabetes mellitus Typ 2">
        </div>
        <div class="fg">
          <label>Datum *</label>
          <input type="date" name="datum" id="mDatum" required>
        </div>
      </div>
      <div class="fg">
        <label>Beschreibung</label>
        <textarea name="beschreibung" id="mBeschr"
          placeholder="Kurze Beschreibung…"></textarea>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn ghost" id="btnAbbrechen">Abbrechen</button>
        <button type="submit" class="btn green" id="mSubmit">Hinzufügen</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {

var modal   = document.getElementById('modal');
var today   = '<?= date('Y-m-d') ?>';

function openModal(d) {
  var create = !d;
  document.getElementById('mTitle').textContent  = create ? 'Neue Diagnose' : 'Diagnose bearbeiten';
  document.getElementById('mAction').value       = create ? 'create' : 'update';
  document.getElementById('mId').value           = d ? d.diagnose_id       : '';
  document.getElementById('mPat').value          = d ? d.patient_id        : '';
  document.getElementById('mName').value         = d ? d.name              : '';
  document.getElementById('mDatum').value        = d ? d.datum             : today;
  document.getElementById('mBeschr').value       = d ? (d.beschreibung||'') : '';
  document.getElementById('mSubmit').textContent = create ? 'Hinzufügen' : 'Speichern';
  modal.classList.add('on');
  setTimeout(function(){ document.getElementById('mPat').focus(); }, 50);
}

function closeModal() { modal.classList.remove('on'); }

// Open buttons
document.getElementById('btnNeu').addEventListener('click', function(){ openModal(); });
var btnNeu2 = document.getElementById('btnNeu2');
if (btnNeu2) btnNeu2.addEventListener('click', function(){ openModal(); });

document.getElementById('btnClose').addEventListener('click', closeModal);
document.getElementById('btnAbbrechen').addEventListener('click', closeModal);

// Edit buttons — use data attribute to avoid inline onclick issues
document.querySelectorAll('.edit-btn').forEach(function(btn) {
  btn.addEventListener('click', function() {
    var d = JSON.parse(this.getAttribute('data-d'));
    openModal(d);
  });
});

// Close on backdrop click
modal.addEventListener('click', function(e) {
  if (e.target === modal) closeModal();
});

// Close on Escape
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeModal();
});

}); // end DOMContentLoaded
</script>

<hr style="margin-top:40px;border:none;border-top:1px solid var(--border)">
<p style="margin-top:12px;font-size:12px;color:var(--muted)">Diagnosen verwalten — <?= date('d.m.Y H:i') ?></p>
</body>
</html>