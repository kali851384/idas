<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id   = $_SESSION["arzt_id"];
$arzt_name = $_SESSION["arzt_name"];

// Stats
$total_termine   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id"))['c'];
$upcoming_count  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id AND datum >= NOW()"))['c'];
$today_count     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM termin WHERE arzt_id=$arzt_id AND DATE(datum) = CURDATE()"))['c'];
$patient_count   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(DISTINCT patient_id) c FROM termin WHERE arzt_id=$arzt_id"))['c'];

// Next 5 upcoming appointments
$upcoming = [];
$r = mysqli_query($conn,
    "SELECT t.termin_id, t.datum, t.beschreibung, t.status,
            p.vorname, p.nachname, p.telefon, p.email AS patient_email
     FROM termin t
     JOIN patient p ON t.patient_id = p.patient_id
     WHERE t.arzt_id = $arzt_id AND t.datum >= NOW()
     ORDER BY t.datum ASC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) $upcoming[] = $row;

// Today's appointments
$today = [];
$r2 = mysqli_query($conn,
    "SELECT t.termin_id, t.datum, t.beschreibung, t.status,
            p.vorname, p.nachname
     FROM termin t
     JOIN patient p ON t.patient_id = p.patient_id
     WHERE t.arzt_id = $arzt_id AND DATE(t.datum) = CURDATE()
     ORDER BY t.datum ASC");
while ($row = mysqli_fetch_assoc($r2)) $today[] = $row;

// Blocked slots count
$blocked_count = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) c FROM arzt_blocked_slots WHERE arzt_id=$arzt_id AND datum >= CURDATE()"))['c'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Arzt Dashboard — IDAS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="cms_style.css">
</head>
<body>
<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-word">ID<span>AS</span></div>
      <div class="sidebar-sub">Arzt Portal</div>
    </div>
    <nav>
      <div class="nav-label">Übersicht</div>
      <a class="nav-link active" href="doctor_dashboard.php"><span class="icon">🏠</span> Dashboard</a>

      <div class="nav-label">Meine Daten</div>
      <a class="nav-link" href="doctor_termine.php"><span class="icon">📅</span> Termine</a>
      <a class="nav-link" href="doctor_patienten.php"><span class="icon">👤</span> Meine Patienten</a>
      <a class="nav-link" href="doctor_verfuegbarkeit.php"><span class="icon">🕐</span> Verfügbarkeit</a>
      <a class="nav-link" href="doctor_profil.php"><span class="icon">⚙️</span> Mein Profil</a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($arzt_name, 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($arzt_name) ?></div>
          <div class="user-role">Arzt</div>
        </div>
      </div>
      <a href="doctor_logout.php">⎋ Logout</a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <div class="page-header">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Willkommen, Dr. <?= htmlspecialchars(explode(' ', $arzt_name)[count(explode(' ', $arzt_name))-1]) ?></div>
      </div>
      <div class="date-badge"><?= date('D, d. M Y – H:i') ?></div>
    </div>

    <!-- Stats -->
    <div class="stats-grid">
      <a class="stat-card" href="doctor_termine.php">
        <div class="stat-top"><div class="stat-label">Heute</div><div class="stat-icon">📋</div></div>
        <div class="stat-value"><?= $today_count ?></div>
      </a>
      <a class="stat-card" href="doctor_termine.php">
        <div class="stat-top"><div class="stat-label">Bevorstehend</div><div class="stat-icon">📅</div></div>
        <div class="stat-value"><?= $upcoming_count ?></div>
      </a>
      <a class="stat-card" href="doctor_patienten.php">
        <div class="stat-top"><div class="stat-label">Meine Patienten</div><div class="stat-icon">👤</div></div>
        <div class="stat-value"><?= $patient_count ?></div>
      </a>
      <a class="stat-card" href="doctor_verfuegbarkeit.php">
        <div class="stat-top"><div class="stat-label">Gesperrte Zeiten</div><div class="stat-icon">🔒</div></div>
        <div class="stat-value"><?= $blocked_count ?></div>
      </a>
    </div>

    <!-- Quick actions -->
    <div class="actions-row">
      <a class="action-card" href="doctor_termine.php">
        <div class="ac-icon">📅</div><div class="ac-label">Alle Termine</div>
      </a>
      <a class="action-card" href="doctor_verfuegbarkeit.php">
        <div class="ac-icon">🔒</div><div class="ac-label">Zeit sperren</div>
      </a>
      <a class="action-card" href="doctor_patienten.php">
        <div class="ac-icon">👤</div><div class="ac-label">Meine Patienten</div>
      </a>
      <a class="action-card" href="doctor_profil.php">
        <div class="ac-icon">⚙️</div><div class="ac-label">Mein Profil</div>
      </a>
    </div>

    <div class="panels">

      <!-- Today -->
      <div class="panel">
        <div class="panel-head">
          <span class="panel-title">📋 Heutige Termine</span>
          <a class="panel-link" href="doctor_termine.php">Alle →</a>
        </div>
        <div class="panel-body">
          <?php if ($today): foreach ($today as $t): ?>
          <div class="list-row">
            <div class="list-dot" style="background:<?= strtotime($t['datum']) < time() ? '#aaa' : '#38a169' ?>"></div>
            <div class="list-content">
              <div class="list-title"><?= htmlspecialchars($t['vorname'].' '.$t['nachname']) ?></div>
              <div class="list-sub"><?= date('H:i', strtotime($t['datum'])) ?> Uhr
                <?php if ($t['beschreibung']): ?> · <?= htmlspecialchars(mb_strimwidth($t['beschreibung'],0,40,'…')) ?><?php endif; ?>
              </div>
            </div>
            <div class="list-time">
              <span class="badge-g <?= strtotime($t['datum']) < time() ? 'badge-w' : 'badge-m' ?>">
                <?= strtotime($t['datum']) < time() ? 'Fertig' : 'Offen' ?>
              </span>
            </div>
          </div>
          <?php endforeach; else: ?>
          <div class="empty-msg">Keine Termine heute.</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Upcoming -->
      <div class="panel">
        <div class="panel-head">
          <span class="panel-title">📅 Nächste Termine</span>
          <a class="panel-link" href="doctor_termine.php">Alle →</a>
        </div>
        <div class="panel-body">
          <?php if ($upcoming): foreach ($upcoming as $t): ?>
          <div class="list-row">
            <div class="list-dot" style="background:#3498db"></div>
            <div class="list-content">
              <div class="list-title"><?= htmlspecialchars($t['vorname'].' '.$t['nachname']) ?></div>
              <div class="list-sub"><?= date('d.m.Y H:i', strtotime($t['datum'])) ?> Uhr</div>
            </div>
            <div class="list-time"><?= date('d.m', strtotime($t['datum'])) ?></div>
          </div>
          <?php endforeach; else: ?>
          <div class="empty-msg">Keine bevorstehenden Termine.</div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>
</div>
</body>
</html>
