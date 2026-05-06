<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id = $_SESSION["arzt_id"];

// Update status
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["termin_id"])) {
    $tid    = intval($_POST["termin_id"]);
    $status = trim($_POST["status"] ?? "");
    $stmt   = mysqli_prepare($conn, "UPDATE termin SET status=? WHERE termin_id=? AND arzt_id=?");
    mysqli_stmt_bind_param($stmt, "sii", $status, $tid, $arzt_id);
    mysqli_stmt_execute($stmt);
    $_SESSION['flash'] = "Status aktualisiert."; $_SESSION['flash_type'] = "success";
    header("Location: doctor_termine.php"); exit;
}

$flash = $flash_type = "";
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

// Filter
$filter = $_GET['filter'] ?? 'upcoming';
$search = trim($_GET['q'] ?? '');

$where = "WHERE t.arzt_id = $arzt_id";
if ($filter === 'upcoming') $where .= " AND t.datum >= NOW()";
elseif ($filter === 'past')  $where .= " AND t.datum < NOW()";
elseif ($filter === 'today') $where .= " AND DATE(t.datum) = CURDATE()";

if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (p.vorname LIKE '%$s%' OR p.nachname LIKE '%$s%')";
}

$termine = [];
$r = mysqli_query($conn,
    "SELECT t.termin_id, t.datum, t.beschreibung, t.status,
            p.patient_id, p.vorname, p.nachname, p.email AS patient_email, p.telefon
     FROM termin t
     JOIN patient p ON t.patient_id = p.patient_id
     $where
     ORDER BY t.datum " . ($filter === 'past' ? 'DESC' : 'ASC'));
while ($row = mysqli_fetch_assoc($r)) $termine[] = $row;

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id"))['c'];
$upcoming_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id AND datum >= NOW()"))['c'];
$today_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id AND DATE(datum)=CURDATE()"))['c'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Meine Termine — IDAS</title>
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
      <a class="nav-link active" href="doctor_termine.php"><span class="icon">📅</span> Termine</a>
      <a class="nav-link" href="doctor_patienten.php"><span class="icon">👤</span> Meine Patienten</a>
      <a class="nav-link" href="doctor_verfuegbarkeit.php"><span class="icon">🕐</span> Verfügbarkeit</a>
      <a class="nav-link" href="doctor_profil.php"><span class="icon">⚙️</span> Mein Profil</a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($_SESSION["arzt_name"], 0, 1)) ?></div>
        <div><div class="user-name"><?= htmlspecialchars($_SESSION["arzt_name"]) ?></div><div class="user-role">Arzt</div></div>
      </div>
      <a href="doctor_logout.php">⎋ Logout</a>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <div><div class="page-title">Meine Termine</div><div class="page-subtitle"><?= $total ?> gesamt · <?= $today_count ?> heute · <?= $upcoming_count ?> bevorstehend</div></div>
      <div class="date-badge"><?= date('D, d. M Y') ?></div>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <!-- Filter tabs -->
    <div class="filter-tabs" style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
      <?php
      $tabs = ['upcoming'=>'Bevorstehend','today'=>'Heute','all'=>'Alle','past'=>'Vergangen'];
      foreach ($tabs as $key => $label):
        $active = $filter === $key;
      ?>
      <a href="?filter=<?= $key ?><?= $search ? '&q='.urlencode($search) : '' ?>"
         class="btn <?= $active ? 'blue' : 'ghost' ?>"><?= $label ?></a>
      <?php endforeach; ?>
    </div>

    <form method="get" style="margin-bottom:16px">
      <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
      <div class="toolbar">
        <div class="search-form">
          <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Patient suchen…">
          <button class="btn blue" type="submit">Suchen</button>
          <?php if ($search): ?><a href="?filter=<?= $filter ?>" class="btn ghost">✕</a><?php endif; ?>
        </div>
        <span class="count-badge"><?= count($termine) ?> Termin<?= count($termine)!=1?'e':'' ?></span>
      </div>
    </form>

    <div class="table-wrap">
    <?php if (!$termine): ?>
      <div class="empty"><p style="font-size:40px">📅</p><p style="margin-top:10px">Keine Termine gefunden.</p></div>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>ID</th><th>Datum & Zeit</th><th>Patient</th><th>Kontakt</th><th>Beschreibung</th><th>Status</th><th>Aktion</th></tr>
      </thead>
      <tbody>
      <?php foreach ($termine as $t):
        $past = strtotime($t['datum']) < time();
        $initials = strtoupper(substr($t['vorname'],0,1).substr($t['nachname'],0,1));
        $colors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c'];
        $color = $colors[$t['patient_id'] % count($colors)];
      ?>
      <tr>
        <td style="font-family:monospace;color:var(--muted)">#<?= $t['termin_id'] ?></td>
        <td style="white-space:nowrap">
          <strong><?= date('d.m.Y', strtotime($t['datum'])) ?></strong><br>
          <span style="color:var(--muted);font-size:12px"><?= date('H:i', strtotime($t['datum'])) ?> Uhr</span>
        </td>
        <td>
          <div class="name-cell">
            <div class="avatar" style="background:<?= $color ?>"><?= $initials ?></div>
            <div style="font-weight:600"><?= htmlspecialchars($t['vorname'].' '.$t['nachname']) ?></div>
          </div>
        </td>
        <td style="font-size:12px;color:var(--muted)">
          <?= htmlspecialchars($t['patient_email']) ?><br>
          <?= htmlspecialchars($t['telefon'] ?: '—') ?>
        </td>
        <td style="font-size:12px;color:var(--muted);max-width:180px">
          <?= htmlspecialchars($t['beschreibung'] ? mb_strimwidth($t['beschreibung'],0,60,'…') : '—') ?>
        </td>
        <td>
          <span class="badge-g <?= $past ? 'badge-w' : 'badge-m' ?>">
            <?= $t['status'] ?: ($past ? 'Abgeschlossen' : 'Bevorstehend') ?>
          </span>
        </td>
        <td>
          <form method="post" style="margin:0">
            <input type="hidden" name="termin_id" value="<?= $t['termin_id'] ?>">
            <select name="status" onchange="this.form.submit()" class="filter-sel" style="font-size:12px;padding:4px 8px">
              <option value="Bevorstehend" <?= ($t['status']??'')==='Bevorstehend'?'selected':'' ?>>Bevorstehend</option>
              <option value="Bestätigt"   <?= ($t['status']??'')==='Bestätigt'  ?'selected':'' ?>>Bestätigt</option>
              <option value="Abgesagt"    <?= ($t['status']??'')==='Abgesagt'   ?'selected':'' ?>>Abgesagt</option>
              <option value="Abgeschlossen" <?= ($t['status']??'')==='Abgeschlossen'?'selected':'' ?>>Abgeschlossen</option>
            </select>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
    </div>
  </main>
</div>
</body>
</html>
