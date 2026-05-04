<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id = $_SESSION["arzt_id"];
$search  = trim($_GET['q'] ?? '');

$where = "WHERE t.arzt_id = $arzt_id";
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (p.vorname LIKE '%$s%' OR p.nachname LIKE '%$s%' OR p.email LIKE '%$s%')";
}

$patienten = [];
$r = mysqli_query($conn,
    "SELECT p.patient_id, p.vorname, p.nachname, p.email, p.telefon, p.geburtsdatum, p.wohnort,
            COUNT(t.termin_id) AS termin_count,
            MAX(t.datum) AS last_termin
     FROM termin t
     JOIN patient p ON t.patient_id = p.patient_id
     $where
     GROUP BY p.patient_id
     ORDER BY p.nachname, p.vorname");
while ($row = mysqli_fetch_assoc($r)) $patienten[] = $row;

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Meine Patienten — IDAS</title>
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
      <a class="nav-link active" href="doctor_patienten.php"><span class="icon">👤</span> Meine Patienten</a>
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
      <div><div class="page-title">Meine Patienten</div>
      <div class="page-subtitle"><?= count($patienten) ?> Patient<?= count($patienten)!=1?'en':'' ?></div></div>
    </div>

    <div class="toolbar" style="margin-bottom:16px">
      <form class="search-form" method="get">
        <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Name oder E-Mail…">
        <button class="btn blue" type="submit">Suchen</button>
        <?php if ($search): ?><a href="doctor_patienten.php" class="btn ghost">✕</a><?php endif; ?>
      </form>
      <span class="count-badge"><?= count($patienten) ?> Patient<?= count($patienten)!=1?'en':'' ?></span>
    </div>

    <div class="table-wrap">
    <?php if (!$patienten): ?>
      <div class="empty"><p style="font-size:40px">👤</p><p style="margin-top:10px">Keine Patienten gefunden.</p></div>
    <?php else: ?>
    <table>
      <thead>
        <tr><th>Patient</th><th>Geburtsdatum</th><th>Kontakt</th><th>Wohnort</th><th>Termine</th><th>Letzter Termin</th></tr>
      </thead>
      <tbody>
      <?php foreach ($patienten as $p):
        $initials = strtoupper(substr($p['vorname'],0,1).substr($p['nachname'],0,1));
        $colors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c'];
        $color = $colors[$p['patient_id'] % count($colors)];
      ?>
      <tr>
        <td>
          <div class="name-cell">
            <div class="avatar" style="background:<?= $color ?>"><?= $initials ?></div>
            <div style="font-weight:600"><?= htmlspecialchars($p['vorname'].' '.$p['nachname']) ?></div>
          </div>
        </td>
        <td><?= $p['geburtsdatum'] ? date('d.m.Y', strtotime($p['geburtsdatum'])) : '—' ?></td>
        <td style="font-size:12px;color:var(--muted)"><?= htmlspecialchars($p['email']) ?><br><?= htmlspecialchars($p['telefonnummer'] ?: '—') ?></td>
        <td><?= htmlspecialchars($p['wohnort'] ?: '—') ?></td>
        <td style="text-align:center"><span class="count-badge"><?= $p['termin_count'] ?></span></td>
        <td style="color:var(--muted);font-size:13px">
          <?= $p['last_termin'] ? date('d.m.Y', strtotime($p['last_termin'])) : '—' ?>
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
