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
    'Termine'     => '#',
    'Diagnosen'   => '#',
    'Nachrichten' => 'kontakt.php',
];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard – IDAS</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<style>
:root {
  --bg:      #f0f2f5;
  --surface: #ffffff;
  --border:  #e2e6ea;
  --text:    #1a202c;
  --muted:   #718096;
  --accent:  #0057ff;
  --accent2: #00c6a2;
  --danger:  #e53e3e;
  --warn:    #f6ad55;
  --radius:  12px;
  --shadow:  0 2px 12px rgba(0,0,0,.07);
}
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: 'DM Sans', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }

/* Layout */
.layout { display: grid; grid-template-columns: 220px 1fr; min-height: 100vh; }

/* Sidebar */
.sidebar {
  background: var(--text);
  color: #fff;
  padding: 28px 0;
  display: flex; flex-direction: column;
  position: sticky; top: 0; height: 100vh;
}
.sidebar-logo { padding: 0 24px 28px; border-bottom: 1px solid rgba(255,255,255,.1); }
.sidebar-logo .logo-word { font-size: 22px; font-weight: 600; letter-spacing: -.5px; }
.sidebar-logo .logo-word span { color: var(--accent2); }
.sidebar-sub { font-size: 11px; color: rgba(255,255,255,.4); letter-spacing: 1px; text-transform: uppercase; margin-top: 2px; }
.sidebar nav { padding: 20px 12px; flex: 1; }
.nav-label { font-size: 10px; letter-spacing: 1.5px; text-transform: uppercase; color: rgba(255,255,255,.3); padding: 0 12px; margin: 18px 0 6px; }
.nav-link {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px; border-radius: 8px;
  color: rgba(255,255,255,.7); text-decoration: none;
  font-size: 14px; font-weight: 400;
  transition: background .15s, color .15s;
  margin-bottom: 2px;
}
.nav-link:hover, .nav-link.active { background: rgba(255,255,255,.1); color: #fff; }
.nav-link .icon { width: 20px; text-align: center; font-size: 15px; }
.sidebar-footer { padding: 16px 24px; border-top: 1px solid rgba(255,255,255,.1); }
.sidebar-footer a { color: rgba(255,255,255,.5); font-size: 13px; text-decoration: none; }
.sidebar-footer a:hover { color: #fff; }
.user-chip { display: flex; align-items: center; gap: 10px; margin-bottom: 12px; }
.avatar { width: 34px; height: 34px; border-radius: 50%; background: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: #fff; flex-shrink: 0; }
.user-name { font-size: 13px; font-weight: 500; color: #fff; }
.user-role { font-size: 11px; color: rgba(255,255,255,.4); }

/* Main */
.main { padding: 36px 40px; overflow-y: auto; }
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; }
.page-title { font-size: 26px; font-weight: 600; letter-spacing: -.5px; }
.page-subtitle { font-size: 14px; color: var(--muted); margin-top: 3px; }
.date-badge { background: var(--surface); border: 1px solid var(--border); border-radius: 8px; padding: 8px 14px; font-size: 13px; color: var(--muted); font-family: 'DM Mono', monospace; }

.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 32px; }
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  padding: 22px 24px;
  text-decoration: none; color: inherit;
  display: block;
  transition: box-shadow .2s, transform .2s;
  position: relative; overflow: hidden;
}
.stat-card::after {
  content: '';
  position: absolute; bottom: 0; left: 0; right: 0; height: 3px;
  background: var(--accent);
  transform: scaleX(0); transform-origin: left;
  transition: transform .2s;
}
.stat-card:hover { box-shadow: var(--shadow); transform: translateY(-2px); }
.stat-card:hover::after { transform: scaleX(1); }
.stat-card:nth-child(2)::after { background: var(--accent2); }
.stat-card:nth-child(3)::after { background: #805ad5; }
.stat-card:nth-child(4)::after { background: var(--warn); }
.stat-card:nth-child(5)::after { background: #e53e3e; }
.stat-card:nth-child(6)::after { background: #38a169; }
.stat-top { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 14px; }
.stat-icon { font-size: 22px; }
.stat-label { font-size: 12px; font-weight: 500; color: var(--muted); text-transform: uppercase; letter-spacing: .8px; }
.stat-value { font-size: 36px; font-weight: 600; letter-spacing: -1.5px; font-family: 'DM Mono', monospace; line-height: 1; }

.panels { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px; }
.panel {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  overflow: hidden;
}
.panel-head { padding: 16px 20px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; }
.panel-title { font-size: 14px; font-weight: 600; }
.panel-link { font-size: 12px; color: var(--accent); text-decoration: none; }
.panel-link:hover { text-decoration: underline; }
.panel-body { padding: 0; }
.list-row { display: flex; align-items: center; gap: 12px; padding: 12px 20px; border-bottom: 1px solid var(--border); }
.list-row:last-child { border-bottom: none; }
.list-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--accent); flex-shrink: 0; }
.list-content { flex: 1; min-width: 0; }
.list-title { font-size: 13px; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.list-sub { font-size: 12px; color: var(--muted); margin-top: 1px; }
.list-time { font-size: 11px; color: var(--muted); font-family: 'DM Mono', monospace; white-space: nowrap; }
.empty-msg { padding: 24px 20px; font-size: 13px; color: var(--muted); text-align: center; }

/* Top symptoms bar chart */
.symptom-bars { padding: 16px 20px; }
.bar-row { margin-bottom: 12px; }
.bar-label { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 4px; }
.bar-label span:last-child { color: var(--muted); font-family: 'DM Mono', monospace; }
.bar-track { height: 6px; background: var(--bg); border-radius: 3px; overflow: hidden; }
.bar-fill { height: 100%; border-radius: 3px; background: linear-gradient(90deg, var(--accent), var(--accent2)); transition: width .6s cubic-bezier(.4,0,.2,1); }

.actions-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 32px; }
.action-card {
  background: var(--surface); border: 1px solid var(--border);
  border-radius: var(--radius); padding: 18px 16px;
  text-align: center; text-decoration: none; color: var(--text);
  transition: background .15s, border-color .15s;
}
.action-card:hover { background: #f7f9ff; border-color: var(--accent); }
.action-card .ac-icon { font-size: 24px; margin-bottom: 8px; }
.action-card .ac-label { font-size: 13px; font-weight: 500; }

@media (max-width: 900px) {
  .layout { grid-template-columns: 1fr; }
  .sidebar { display: none; }
  .stats-grid { grid-template-columns: repeat(2,1fr); }
  .panels { grid-template-columns: 1fr; }
  .actions-row { grid-template-columns: repeat(2,1fr); }
  .main { padding: 24px 20px; }
}
</style>
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
      <a class="nav-link active" href="funktionen.php"><span class="icon">🏠</span> Dashboard</a>

      <div class="nav-label">Verwaltung</div>
      <a class="nav-link" href="symptome_verwaltung.php"><span class="icon">🔬</span> Symptome</a>
      <a class="nav-link" href="aerzte_verwaltung.php"><span class="icon">🩺</span> Ärzte</a>
      <a class="nav-link" href="konto_manager.php"><span class="icon">👥</span> Konten</a>
      <a class="nav-link" href="kontakt.php"><span class="icon">✉️</span> Nachrichten</a>
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
      <a class="action-card" href="konto_manager.php">
        <div class="ac-icon">👥</div><div class="ac-label">Konto erstellen</div>
      </a>
      <a class="action-card" href="kontakt.php">
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