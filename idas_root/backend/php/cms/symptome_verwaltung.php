<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

$msg = $msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act  = $_POST['action'] ?? '';
    $id   = intval($_POST['symptom_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $fbs  = array_map('intval', $_POST['fachbereiche'] ?? []);

    if ($act === 'delete') {
        $s = mysqli_prepare($conn, "DELETE FROM symptome WHERE symptom_id = ?");
        mysqli_stmt_bind_param($s, "i", $id);
        $msg = mysqli_stmt_execute($s) ? "Symptom gelöscht." : mysqli_error($conn);
        $msg_type = $msg === "Symptom gelöscht." ? "success" : "error";

    } elseif ($act === 'create' || $act === 'update') {
        if ($name === '')           { $msg = "Name darf nicht leer sein."; $msg_type = "error"; }
        elseif (strlen($name) > 60) { $msg = "Name max. 60 Zeichen.";      $msg_type = "error"; }
        else {
            if ($act === 'create') {
                $s = mysqli_prepare($conn, "INSERT INTO symptome (name) VALUES (?)");
                mysqli_stmt_bind_param($s, "s", $name);
                mysqli_stmt_execute($s);
                $id = mysqli_insert_id($conn);
                $msg = "Symptom erstellt.";
            } else {
                $s = mysqli_prepare($conn, "UPDATE symptome SET name=? WHERE symptom_id=?");
                mysqli_stmt_bind_param($s, "si", $name, $id);
                mysqli_stmt_execute($s);
                $msg = "Symptom aktualisiert.";
            }
            $msg_type = "success";
            // Sync fachbereich assignments
            $d = mysqli_prepare($conn, "DELETE FROM symptomdet WHERE symptom_id=?");
            mysqli_stmt_bind_param($d, "i", $id); mysqli_stmt_execute($d);
            if ($fbs) {
                $ins = mysqli_prepare($conn, "INSERT INTO symptomdet (symptom_id, fachbereich_id) VALUES (?,?)");
                foreach ($fbs as $fb) { mysqli_stmt_bind_param($ins, "ii", $id, $fb); mysqli_stmt_execute($ins); }
            }
        }
    }
}

// Load data
$symptoms = [];
$r = mysqli_query($conn,
    "SELECT s.symptom_id, s.name,
            GROUP_CONCAT(f.name      ORDER BY f.name SEPARATOR ', ') AS fachbereiche,
            GROUP_CONCAT(f.fachbereich_id ORDER BY f.name SEPARATOR ',') AS fb_ids
     FROM symptome s
     LEFT JOIN symptomdet sd ON s.symptom_id = sd.symptom_id
     LEFT JOIN fachbereich f  ON sd.fachbereich_id = f.fachbereich_id
     GROUP BY s.symptom_id ORDER BY s.symptom_id");
while ($row = mysqli_fetch_assoc($r)) $symptoms[] = $row;

$fachbereiche = [];
$r2 = mysqli_query($conn, "SELECT * FROM fachbereich ORDER BY name");
while ($row = mysqli_fetch_assoc($r2)) $fachbereiche[] = $row;
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Symptome verwalten</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body>
<div class="layout">
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-word">ID<span>AS</span></div>
      <div class="sidebar-sub">Admin Panel</div>
    </div>
    <nav>
      <div class="nav-label">Übersicht</div>
      <a class="nav-link" href="admin_dashboard.php"><span class="icon">🏠</span> Dashboard</a>
      <div class="nav-label">Verwaltung</div>
      <a class="nav-link active" href="symptome_verwaltung.php"><span class="icon">🔬</span> Symptome</a>
      <a class="nav-link" href="aerzte_verwaltung.php"><span class="icon">🩺</span> Ärzte</a>
      <a class="nav-link" href="patienten_verwaltung.php"><span class="icon">👤</span> Patienten</a>
      <a class="nav-link" href="termine_verwaltung.php"><span class="icon">📅</span> Termine</a>
      <a class="nav-link" href="diagnose_verwaltung.php"><span class="icon">🏥</span> Diagnosen</a>
      <a class="nav-link" href="kontakt_verwaltung.php"><span class="icon">✉️</span> Nachrichten</a>
      <a class="nav-link" href="support_verwaltung.php"><span class="icon">🎧</span> Support</a>
      <a class="nav-link" href="konto_verwaltung.php"><span class="icon">🔐</span> Konten</a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($_SESSION["admin_user"] ?? "A", 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION["admin_user"] ?? "") ?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <a href="admin_logout.php">⎋ Logout</a>
    </div>
  </aside>
  <main class="main">


<div class="page-header"><div><div class="page-title">🩺 Symptome verwalten</div></div></div>
<?php if ($msg): ?>
  <div class="flash <?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>Alle Symptome</h2>
<div class="search-wrap">
  <input type="search" id="q" placeholder="🔍 Suchen…" oninput="filter()">
  <span id="countLabel"></span>
  <button class="btn green" onclick="openModal()">✚ Neues Symptom</button>
</div>

<table id="tbl">
  <thead><tr><th>ID</th><th>Name</th><th>Fachbereiche</th><th>Aktionen</th></tr></thead>
  <tbody>
  <?php foreach ($symptoms as $s): ?>
  <tr data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>">
    <td><?= $s['symptom_id'] ?></td>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?php
      if ($s['fachbereiche'])
        foreach (explode(', ', $s['fachbereiche']) as $t) echo "<span class='tag'>".htmlspecialchars($t)."</span>";
      else echo "<span class='none'>Keine</span>";
    ?></td>
    <td style="display:flex;gap:6px">
      <button class="btn blue" onclick='openModal(<?= $s["symptom_id"] ?>,<?= json_encode($s["name"]) ?>,<?= json_encode($s["fb_ids"] ?? "") ?>)'>✏️ Bearbeiten</button>
      <form method="post" onsubmit="return confirm('Symptom löschen?')">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="symptom_id" value="<?= $s['symptom_id'] ?>">
        <button class="btn red">🗑 Löschen</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<div class="modal-overlay" id="modal">
  <div class="modal">
    <button class="close-btn" onclick="closeModal()" type="button">✕</button>
    <h3 id="modalTitle">Symptom</h3>
    <form method="post">
      <input type="hidden" name="action" id="formAction" value="create">
      <input type="hidden" name="symptom_id" id="formId">
      <label style="font-size:13px;font-weight:bold">Name (max. 60 Zeichen)</label>
      <input type="text" name="name" id="formName" required maxlength="60" style="margin-top:5px;margin-bottom:12px">
      <label style="font-size:13px;font-weight:bold">Fachbereiche</label>
      <div class="small-btns">
        <button type="button" onclick="toggleAll(true)">Alle</button>
        <button type="button" onclick="toggleAll(false)">Keine</button>
      </div>
      <div class="cb-grid" id="cbGrid">
        <?php foreach ($fachbereiche as $fb): ?>
        <label><input type="checkbox" class="fb-cb" name="fachbereiche[]" value="<?= $fb['fachbereich_id'] ?>"> <?= htmlspecialchars($fb['name']) ?></label>
        <?php endforeach; ?>
      </div>
      <div class="row">
        <button type="button" class="btn grey" onclick="closeModal()">Abbrechen</button>
        <button type="submit" class="btn blue" id="submitBtn">Speichern</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id, name, fbIds) {
  const create = !id;
  document.getElementById('modalTitle').textContent = create ? '✚ Neues Symptom' : '✏️ Symptom bearbeiten';
  document.getElementById('formAction').value = create ? 'create' : 'update';
  document.getElementById('formId').value    = id   || '';
  document.getElementById('formName').value  = name || '';
  const assigned = fbIds ? new Set(fbIds.split(',').map(Number)) : new Set();
  document.querySelectorAll('.fb-cb').forEach(cb => cb.checked = assigned.has(+cb.value));
  document.getElementById('modal').classList.add('active');
  setTimeout(() => document.getElementById('formName').focus(), 50);
}
function closeModal() { document.getElementById('modal').classList.remove('active'); }
document.getElementById('modal').addEventListener('click', e => { if (e.target === document.getElementById('modal')) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
function toggleAll(v) { document.querySelectorAll('.fb-cb').forEach(cb => cb.checked = v); }
function filter() {
  const q = document.getElementById('q').value.toLowerCase();
  let shown = 0;
  document.querySelectorAll('#tbl tbody tr').forEach(tr => {
    const match = tr.dataset.name.includes(q);
    tr.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('countLabel').textContent = q ? `${shown} von ${<?= count($symptoms) ?>} Symptomen` : `${<?= count($symptoms) ?>} Symptome`;
}
filter();
</script>

<hr style="margin-top:40px">
<p><small>Symptome verwalten – <?= date('d.m.Y H:i') ?></small></p>
  </main>
</div>
</body>
</html>
