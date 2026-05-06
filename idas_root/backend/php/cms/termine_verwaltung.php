<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }

$success = $error = "";

// DELETE
if (isset($_GET["delete"])) {
    $id   = intval($_GET["delete"]);
    $stmt = mysqli_prepare($conn, "DELETE FROM termin WHERE termin_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) $success = "Termin gelöscht.";
    else $error = "Fehler beim Löschen.";
}

// SAVE EDIT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["termin_id"])) {
    $id           = intval($_POST["termin_id"]);
    $arzt_id      = intval($_POST["arzt_id"]);
    $patient_id   = intval($_POST["patient_id"]);
    $datum        = trim($_POST["datum"]);
    $beschreibung = trim($_POST["beschreibung"]);

    $stmt = mysqli_prepare($conn,
        "UPDATE termin SET arzt_id=?, patient_id=?, datum=?, beschreibung=? WHERE termin_id=?");
    mysqli_stmt_bind_param($stmt, "iissi", $arzt_id, $patient_id, $datum, $beschreibung, $id);
    if (mysqli_stmt_execute($stmt)) $success = "Termin aktualisiert.";
    else $error = "Fehler beim Speichern.";
}

// LOAD EDIT
$edit_termin = null;
if (isset($_GET["edit"])) {
    $id   = intval($_GET["edit"]);
    $stmt = mysqli_prepare($conn, "SELECT * FROM termin WHERE termin_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_termin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// LOAD ALL
$termine = mysqli_query($conn,
    "SELECT t.termin_id, t.datum, t.beschreibung,
            a.name AS arzt_name,
            CONCAT(p.vorname,' ',p.nachname) AS patient_name
     FROM termin t
     JOIN arzt a    ON t.arzt_id    = a.arzt_id
     JOIN patient p ON t.patient_id = p.patient_id
     ORDER BY t.datum DESC");

$aerzte    = mysqli_query($conn, "SELECT arzt_id, name FROM arzt ORDER BY name");
$patienten = mysqli_query($conn, "SELECT patient_id, CONCAT(vorname,' ',nachname) AS name FROM patient ORDER BY nachname");

// Stats
$total     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin"))['c'];
$upcoming  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE datum >= NOW()"))['c'];
$past      = $total - $upcoming;
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Termine — IDAS CMS</title>
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
      <a class="nav-link" href="symptome_verwaltung.php"><span class="icon">🔬</span> Symptome</a>
      <a class="nav-link" href="aerzte_verwaltung.php"><span class="icon">🩺</span> Ärzte</a>
      <a class="nav-link" href="patienten_verwaltung.php"><span class="icon">👤</span> Patienten</a>
      <a class="nav-link active" href="termine_verwaltung.php"><span class="icon">📅</span> Termine</a>
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


<div class="topbar">
  <div class="page-header"><div><div class="page-title">📅 Termine</div></div></div>
  <a href="admin_dashboard.php"      class="btn ghost">← Dashboard</a>
  <a href="kontakt_verwaltung.php"   class="btn ghost">✉️ Nachrichten</a>
  <a href="admin_logout.php"         class="btn ghost">Logout</a>
</div>

<?php if ($success): ?>
  <div class="flash success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="flash error">✕ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-row">
  <div class="stat-box blue">
    <div class="stat-num"><?= $total ?></div>
    <div class="stat-lbl">Gesamt</div>
  </div>
  <div class="stat-box green">
    <div class="stat-num"><?= $upcoming ?></div>
    <div class="stat-lbl">Bevorstehend</div>
  </div>
  <div class="stat-box">
    <div class="stat-num"><?= $past ?></div>
    <div class="stat-lbl">Abgeschlossen</div>
  </div>
</div>

<!-- EDIT FORM -->
<?php if ($edit_termin): ?>
<div class="form-box">
  <h2>✏️ Termin bearbeiten</h2>
  <form method="post">
    <input type="hidden" name="termin_id" value="<?= $edit_termin["termin_id"] ?>">

    <div class="grid2">
      <div>
        <label>Arzt</label>
        <select name="arzt_id">
          <?php mysqli_data_seek($aerzte, 0); while ($a = mysqli_fetch_assoc($aerzte)): ?>
          <option value="<?= $a["arzt_id"] ?>" <?= $a["arzt_id"]==$edit_termin["arzt_id"]?'selected':'' ?>>
            <?= htmlspecialchars($a["name"]) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div>
        <label>Patient</label>
        <select name="patient_id">
          <?php mysqli_data_seek($patienten, 0); while ($p = mysqli_fetch_assoc($patienten)): ?>
          <option value="<?= $p["patient_id"] ?>" <?= $p["patient_id"]==$edit_termin["patient_id"]?'selected':'' ?>>
            <?= htmlspecialchars($p["name"]) ?>
          </option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>

    <label>Datum &amp; Uhrzeit</label>
    <input type="datetime-local" name="datum"
      value="<?= date("Y-m-d\TH:i", strtotime($edit_termin["datum"])) ?>">

    <label>Beschreibung</label>
    <textarea name="beschreibung" rows="4"><?= htmlspecialchars($edit_termin["beschreibung"] ?? "") ?></textarea>

    <br><br>
    <button type="submit" class="btn-save">💾 Speichern</button>
    <a href="termine_verwaltung.php" class="nav-link active" class="btn ghost" style="margin-left:8px">Abbrechen</a>
  </form>
</div>
<?php endif; ?>

<!-- TABLE -->
<div class="table-wrap">
<?php if (mysqli_num_rows($termine) === 0): ?>
  <div class="empty"><p style="font-size:36px">📅</p><p style="margin-top:10px">Keine Termine vorhanden.</p></div>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Datum</th>
      <th>Arzt</th>
      <th>Patient</th>
      <th>Beschreibung</th>
      <th>Status</th>
      <th>Aktionen</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($t = mysqli_fetch_assoc($termine)):
    $isPast = strtotime($t["datum"]) < time();
  ?>
  <tr>
    <td style="font-family:monospace;color:var(--muted)">#<?= $t["termin_id"] ?></td>
    <td style="white-space:nowrap"><?= date("d.m.Y H:i", strtotime($t["datum"])) ?></td>
    <td><strong><?= htmlspecialchars($t["arzt_name"]) ?></strong></td>
    <td><?= htmlspecialchars($t["patient_name"]) ?></td>
    <td style="font-size:12px;color:var(--muted);max-width:200px">
      <?= htmlspecialchars($t["beschreibung"] ?? "—") ?>
    </td>
    <td>
      <span class="badge-g <?= $isPast ? 'badge-w' : 'badge-m' ?>">
        <?= $isPast ? 'Abgeschlossen' : 'Bevorstehend' ?>
      </span>
    </td>
    <td>
      <div class="act-cell">
        <a href="?edit=<?= $t["termin_id"] ?>" class="btn-edit">✏️ Bearbeiten</a>
        <a href="?delete=<?= $t["termin_id"] ?>" class="btn-delete"
           onclick="return confirm('Termin wirklich löschen?')">🗑 Löschen</a>
      </div>
    </td>
  </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<hr style="margin-top:40px;border:none;border-top:1px solid var(--border)">
<p style="margin-top:12px;font-size:12px;color:var(--muted)">Termine verwalten — <?= date('d.m.Y H:i') ?></p>
  </main>
</div>
</body>
</html>
