<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }

$success = $error = "";

// DELETE
if (isset($_GET["delete"])) {
    $id   = intval($_GET["delete"]);
    $stmt = mysqli_prepare($conn, "DELETE FROM kontakt_nachrichten WHERE kontakt_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt) ? "Nachricht gelöscht." : "Fehler beim Löschen.";
    if (!mysqli_stmt_execute($stmt)) $error = "Fehler beim Löschen.";
}

// SAVE EDIT
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nachricht_id"])) {
    $id       = intval($_POST["nachricht_id"]);
    $nachricht = trim($_POST["nachricht"]);
    $stmt = mysqli_prepare($conn, "UPDATE kontakt_nachrichten SET nachricht = ? WHERE kontakt_id = ?");
    mysqli_stmt_bind_param($stmt, "si", $nachricht, $id);
    $success = mysqli_stmt_execute($stmt) ? "Nachricht aktualisiert." : "Fehler beim Speichern.";
}

// LOAD EDIT
$edit_msg = null;
if (isset($_GET["edit"])) {
    $id   = intval($_GET["edit"]);
    $stmt = mysqli_prepare($conn, "SELECT * FROM kontakt_nachrichten WHERE kontakt_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_msg = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// LOAD ALL
$nachrichten = mysqli_query($conn, "SELECT * FROM kontakt_nachrichten ORDER BY datum DESC");
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kontakt-Nachrichten — IDAS CMS</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body class="padded">

<div class="topbar">
  <h1>✉️ Kontakt-Nachrichten</h1>
  <a href="admin_dashboard.php" class="btn ghost">← Dashboard</a>
  <a href="termine_verwaltung.php" class="btn ghost">📅 Termine</a>
  <a href="admin_logout.php" class="btn ghost">Logout</a>
</div>

<?php if ($success): ?>
  <div class="flash success">✓ <?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
  <div class="flash error">✕ <?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<!-- EDIT FORM -->
<?php if ($edit_msg): ?>
<div class="form-box">
  <h2>✏️ Nachricht bearbeiten</h2>
  <p class="meta">
    Von: <strong><?= htmlspecialchars($edit_msg["vorname"]." ".$edit_msg["nachname"]) ?></strong>
    &nbsp;·&nbsp; <?= htmlspecialchars($edit_msg["email"]) ?>
    &nbsp;·&nbsp; <?= date("d.m.Y H:i", strtotime($edit_msg["datum"])) ?>
  </p>
  <form method="post">
    <input type="hidden" name="nachricht_id" value="<?= $edit_msg["kontakt_id"] ?>">
    <label>Nachrichtentext</label>
    <textarea name="nachricht" rows="6"><?= htmlspecialchars($edit_msg["nachricht"]) ?></textarea>
    <br><br>
    <button type="submit" class="btn-save">💾 Speichern</button>
    <a href="kontakt_verwaltung.php" class="btn ghost" style="margin-left:8px">Abbrechen</a>
  </form>
</div>
<?php endif; ?>

<!-- TABLE -->
<div class="table-wrap">
<?php if (mysqli_num_rows($nachrichten) === 0): ?>
  <div class="empty"><p style="font-size:36px">✉️</p><p style="margin-top:10px">Keine Nachrichten vorhanden.</p></div>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Datum</th>
      <th>Name</th>
      <th>E-Mail</th>
      <th>Betreff</th>
      <th>Nachricht</th>
      <th>Aktionen</th>
    </tr>
  </thead>
  <tbody>
  <?php while ($n = mysqli_fetch_assoc($nachrichten)): ?>
  <tr>
    <td style="font-family:monospace;color:var(--muted)">#<?= $n["kontakt_id"] ?></td>
    <td style="white-space:nowrap"><?= date("d.m.Y H:i", strtotime($n["datum"])) ?></td>
    <td><strong><?= htmlspecialchars($n["vorname"]." ".$n["nachname"]) ?></strong></td>
    <td><?= htmlspecialchars($n["email"]) ?></td>
    <td><?= htmlspecialchars($n["betreff"] ?? "—") ?></td>
    <td style="max-width:280px;color:var(--muted);font-size:12px">
      <?= nl2br(htmlspecialchars(mb_strimwidth($n["nachricht"], 0, 80, "…"))) ?>
    </td>
    <td>
      <div class="act-cell">
        <a href="?edit=<?= $n["kontakt_id"] ?>" class="btn-edit">✏️ Bearbeiten</a>
        <a href="?delete=<?= $n["kontakt_id"] ?>" class="btn-delete"
           onclick="return confirm('Nachricht wirklich löschen?')">🗑 Löschen</a>
      </div>
    </td>
  </tr>
  <?php endwhile; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<hr style="margin-top:40px;border:none;border-top:1px solid var(--border)">
<p style="margin-top:12px;font-size:12px;color:var(--muted)">Kontakt-Nachrichten — <?= date('d.m.Y H:i') ?></p>
</body>
</html>
