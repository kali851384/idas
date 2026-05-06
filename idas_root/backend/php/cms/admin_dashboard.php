<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

function count_table($conn, $table) {
    $r = mysqli_query($conn, "SELECT COUNT(*) AS c FROM `$table`");
    return mysqli_fetch_assoc($r)['c'];
}

$stats = [
    'Patienten'  => count_table($conn, 'patient'),
    'Ärzte'      => count_table($conn, 'arzt'),
    'Symptome'   => count_table($conn, 'symptome'),
    'Termine'    => count_table($conn, 'termin'),
    'Diagnosen'  => count_table($conn, 'diagnose'),
    'Nachrichten'=> count_table($conn, 'kontakt_nachrichten'),
];

$msgs = [];
$r = mysqli_query($conn, "SELECT vorname, nachname, betreff, datum FROM kontakt_nachrichten ORDER BY datum DESC LIMIT 5");
while ($row = mysqli_fetch_assoc($r)) $msgs[] = $row;

$termine = [];
$r2 = mysqli_query($conn,
    "SELECT t.datum, p.vorname AS pv, p.nachname AS pn, a.name AS arzt
     FROM termin t
     JOIN patient p ON t.patient_id = p.patient_id
     JOIN arzt a    ON t.arzt_id    = a.arzt_id
     WHERE t.datum >= NOW()
     ORDER BY t.datum ASC LIMIT 5");
while ($row = mysqli_fetch_assoc($r2)) $termine[] = $row;

$top_symptoms = [];
$r3 = mysqli_query($conn,
    "SELECT s.name, COUNT(dd.symptom_id) AS cnt
     FROM symptome s
     JOIN diagnosedet dd ON s.symptom_id = dd.symptom_id
     GROUP BY s.symptom_id ORDER BY cnt DESC LIMIT 6");
while ($row = mysqli_fetch_assoc($r3)) $top_symptoms[] = $row;

mysqli_close($conn);

$icons = ['Patienten'=>'👤','Ärzte'=>'🩺','Symptome'=>'🔬','Termine'=>'📅','Diagnosen'=>'📋','Nachrichten'=>'✉️'];
$links = [
    'Patienten'   => 'patienten_verwaltung.php',
    'Ärzte'       => 'aerzte_verwaltung.php',
    'Symptome'    => 'symptome_verwaltung.php',
    'Termine'     => 'termine_verwaltung.php',
    'Diagnosen'   => 'diagnose_verwaltung.php',
    'Nachrichten' => 'kontakt_verwaltung.php',
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard – IDAS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="cms_style.css">
</head>
<body>
<div class="layout">

  <!-- Sidebar -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-word">ID<span>AS</span></div>
      <div class="sidebar-sub">Admin Panel</div>
    </div>
    <nav>
      <div class="nav-label">Übersicht</div>
      <a class="nav-link active" href="admin_dashboard.php"><span class="icon">🏠</span> Dashboard</a>

      <div class="nav-label">Verwaltung</div>
      <a class="nav-link" href="symptome_verwaltung.php"><span class="icon">🔬</span> Symptome</a>
      <a class="nav-link" href="aerzte_verwaltung.php"><span class="icon">🩺</span> Ärzte</a>
      <a class="nav-link" href="konto_verwaltung.php"><span class="icon">👥</span> Konten</a>
      <a class="nav-link" href="kontakt_verwaltung.php"><span class="icon">✉️</span> Nachrichten</a>
    </nav>
    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="avatar"><?= strtoupper(substr($_SESSION["admin_user"], 0, 1)) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION["admin_user"]) ?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <a href="admin_logout.php">⎋ Logout</a>
    </div>
  </aside>

  <!-- MAIN -->
  <main class="main">

    <div class="page-header">
      <div>
        <div class="page-title">Dashboard</div>
        <div class="page-subtitle">Willkommen zurück, <?= htmlspecialchars(explode('@', $_SESSION["admin_user"])[0]) ?></div>
      </div>
      <div class="date-badge"><?= date('D, d. M Y – H:i') ?></div>
    </div>

    <div class="stats-grid">
      <?php foreach ($stats as $label => $val): ?>
      <a class="stat-card" href="<?= $links[$label] ?>">
        <div class="stat-top">
          <div class="stat-label"><?= $label ?></div>
          <div class="stat-icon"><?= $icons[$label] ?></div>
        </div>
        <div class="stat-value"><?= number_format($val) ?></div>
      </a>
      <?php endforeach; ?>
    </div>

    <div class="actions-row">
      <a class="action-card" href="symptome_verwaltung.php">
        <div class="ac-icon">🔬</div><div class="ac-label">Symptom hinzufügen</div>
      </a>
      <a class="action-card" href="aerzte_verwaltung.php">
        <div class="ac-icon">🩺</div><div class="ac-label">Arzt verwalten</div>
      </a>
      <a class="action-card" href="konto_verwaltung.php">
        <div class="ac-icon">👥</div><div class="ac-label">Konto erstellen</div>
      </a>
      <a class="action-card" href="kontakt_verwaltung.php">
        <div class="ac-icon">✉️</div><div class="ac-label">Nachrichten ansehen</div>
      </a>
    </div>

    <div class="panels">

      <div class="panel">
        <div class="panel-head">
          <span class="panel-title">✉️ Letzte Nachrichten</span>
          <a class="panel-link" href="kontakt.php">Alle ansehen →</a>
        </div>
        <div class="panel-body">
          <?php if ($msgs): foreach ($msgs as $m): ?>
          <div class="list-row">
            <div class="list-dot" style="background:#38a169"></div>
            <div class="list-content">
              <div class="list-title"><?= htmlspecialchars($m['vorname'].' '.$m['nachname']) ?></div>
              <div class="list-sub"><?= htmlspecialchars($m['betreff'] ?: '(kein Betreff)') ?></div>
            </div>
            <div class="list-time"><?= date('d.m', strtotime($m['datum'])) ?></div>
          </div>
          <?php endforeach; else: ?>
          <div class="empty-msg">Keine Nachrichten vorhanden.</div>
          <?php endif; ?>
        </div>
      </div>



    </div>

    <!-- TOP SYMPTOMS -->
    <?php if ($top_symptoms): ?>
    <div class="panel">
      <div class="panel-head">
        <span class="panel-title">🔬 Häufigste Symptome in Diagnosen</span>
      </div>
      <div class="symptom-bars">
        <?php
          $max = max(array_column($top_symptoms, 'cnt')) ?: 1;
          foreach ($top_symptoms as $sym):
            $pct = round($sym['cnt'] / $max * 100);
        ?>
        <div class="bar-row">
          <div class="bar-label">
            <span><?= htmlspecialchars($sym['name']) ?></span>
            <span><?= $sym['cnt'] ?></span>
          </div>
          <div class="bar-track"><div class="bar-fill" style="width:<?= $pct ?>%"></div></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </main>
</div>
</body>
</html>
