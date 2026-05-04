<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id = $_SESSION["arzt_id"];

// Ensure table exists
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS arzt_blocked_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arzt_id INT NOT NULL,
    datum DATE NOT NULL,
    von TIME DEFAULT NULL,
    bis TIME DEFAULT NULL,
    grund VARCHAR(200) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (arzt_id) REFERENCES arzt(arzt_id) ON DELETE CASCADE
)");

mysqli_query($conn, "CREATE TABLE IF NOT EXISTS arzt_arbeitszeiten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    arzt_id INT NOT NULL,
    wochentag TINYINT NOT NULL COMMENT '1=Mo, 2=Di, 3=Mi, 4=Do, 5=Fr, 6=Sa, 7=So',
    von TIME NOT NULL,
    bis TIME NOT NULL,
    UNIQUE KEY unique_day (arzt_id, wochentag),
    FOREIGN KEY (arzt_id) REFERENCES arzt(arzt_id) ON DELETE CASCADE
)");

$flash = $flash_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['action'] ?? '';

    if ($act === 'block') {
        $datum = trim($_POST['datum'] ?? '');
        $von   = trim($_POST['von'] ?? '');
        $bis   = trim($_POST['bis'] ?? '');
        $grund = trim($_POST['grund'] ?? '');
        if ($datum) {
            $s = mysqli_prepare($conn, "INSERT INTO arzt_blocked_slots (arzt_id, datum, von, bis, grund) VALUES (?,?,?,?,?)");
            $von = $von ?: null; $bis = $bis ?: null; $grund = $grund ?: null;
            mysqli_stmt_bind_param($s, "issss", $arzt_id, $datum, $von, $bis, $grund);
            mysqli_stmt_execute($s);
            $flash = "Zeit gesperrt."; $flash_type = "success";
        }

    } elseif ($act === 'unblock') {
        $id = intval($_POST['block_id']);
        $s  = mysqli_prepare($conn, "DELETE FROM arzt_blocked_slots WHERE id=? AND arzt_id=?");
        mysqli_stmt_bind_param($s, "ii", $id, $arzt_id);
        mysqli_stmt_execute($s);
        $flash = "Sperrung entfernt."; $flash_type = "success";

    } elseif ($act === 'save_hours') {
        $days = [1=>'Mo',2=>'Di',3=>'Mi',4=>'Do',5=>'Fr',6=>'Sa',7=>'So'];
        foreach ($days as $num => $day) {
            $von = trim($_POST["von_$num"] ?? '');
            $bis = trim($_POST["bis_$num"] ?? '');
            $active = isset($_POST["active_$num"]);
            if ($active && $von && $bis) {
                $s = mysqli_prepare($conn, "INSERT INTO arzt_arbeitszeiten (arzt_id,wochentag,von,bis) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE von=VALUES(von), bis=VALUES(bis)");
                mysqli_stmt_bind_param($s, "iiss", $arzt_id, $num, $von, $bis);
                mysqli_stmt_execute($s);
            } else {
                $s = mysqli_prepare($conn, "DELETE FROM arzt_arbeitszeiten WHERE arzt_id=? AND wochentag=?");
                mysqli_stmt_bind_param($s, "ii", $arzt_id, $num);
                mysqli_stmt_execute($s);
            }
        }
        $flash = "Arbeitszeiten gespeichert."; $flash_type = "success";
    }
    header("Location: doctor_verfuegbarkeit.php"); exit;
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

// Load blocked slots
$blocked = [];
$r = mysqli_query($conn, "SELECT * FROM arzt_blocked_slots WHERE arzt_id=$arzt_id AND datum >= CURDATE() ORDER BY datum ASC");
while ($row = mysqli_fetch_assoc($r)) $blocked[] = $row;

// Load working hours
$hours = [];
$r2 = mysqli_query($conn, "SELECT * FROM arzt_arbeitszeiten WHERE arzt_id=$arzt_id ORDER BY wochentag");
while ($row = mysqli_fetch_assoc($r2)) $hours[$row['wochentag']] = $row;

$days = [1=>'Montag',2=>'Dienstag',3=>'Mittwoch',4=>'Donnerstag',5=>'Freitag',6=>'Samstag',7=>'Sonntag'];

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Verfügbarkeit — IDAS</title>
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
      <a class="nav-link" href="doctor_patienten.php"><span class="icon">👤</span> Meine Patienten</a>
      <a class="nav-link active" href="doctor_verfuegbarkeit.php"><span class="icon">🕐</span> Verfügbarkeit</a>
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
      <div><div class="page-title">Verfügbarkeit verwalten</div><div class="page-subtitle">Arbeitszeiten & gesperrte Zeiten</div></div>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="panels">

      <!-- Working hours -->
      <div class="panel" style="flex:1.5">
        <div class="panel-head"><span class="panel-title">🕐 Reguläre Arbeitszeiten</span></div>
        <div class="panel-body">
          <form method="post">
            <input type="hidden" name="action" value="save_hours">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr>
                  <th style="text-align:left;padding:8px 4px;font-size:13px;color:var(--muted)">Tag</th>
                  <th style="text-align:left;padding:8px 4px;font-size:13px;color:var(--muted)">Aktiv</th>
                  <th style="text-align:left;padding:8px 4px;font-size:13px;color:var(--muted)">Von</th>
                  <th style="text-align:left;padding:8px 4px;font-size:13px;color:var(--muted)">Bis</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($days as $num => $day):
                $h = $hours[$num] ?? null;
              ?>
              <tr style="border-top:1px solid var(--border)">
                <td style="padding:10px 4px;font-weight:500"><?= $day ?></td>
                <td style="padding:10px 4px">
                  <input type="checkbox" name="active_<?= $num ?>" id="a<?= $num ?>"
                    <?= $h ? 'checked' : '' ?>
                    onchange="toggleDay(<?= $num ?>, this.checked)">
                </td>
                <td style="padding:10px 4px">
                  <input type="time" name="von_<?= $num ?>" id="von<?= $num ?>"
                    value="<?= $h ? $h['von'] : '08:00' ?>"
                    <?= !$h ? 'disabled' : '' ?>
                    style="padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:13px;width:100px">
                </td>
                <td style="padding:10px 4px">
                  <input type="time" name="bis_<?= $num ?>" id="bis<?= $num ?>"
                    value="<?= $h ? $h['bis'] : '17:00' ?>"
                    <?= !$h ? 'disabled' : '' ?>
                    style="padding:4px 8px;border:1px solid var(--border);border-radius:6px;font-size:13px;width:100px">
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <div style="margin-top:16px">
              <button type="submit" class="btn green">💾 Arbeitszeiten speichern</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Block time -->
      <div class="panel" style="flex:1">
        <div class="panel-head"><span class="panel-title">🔒 Zeit sperren</span></div>
        <div class="panel-body">
          <form method="post">
            <input type="hidden" name="action" value="block">
            <div class="fg" style="margin-bottom:12px">
              <label style="font-size:13px;font-weight:600;color:var(--muted)">Datum *</label>
              <input type="date" name="datum" required min="<?= date('Y-m-d') ?>"
                style="padding:8px;border:1px solid var(--border);border-radius:6px;width:100%;margin-top:4px">
            </div>
            <div style="display:flex;gap:8px;margin-bottom:12px">
              <div class="fg" style="flex:1">
                <label style="font-size:13px;font-weight:600;color:var(--muted)">Von</label>
                <input type="time" name="von"
                  style="padding:8px;border:1px solid var(--border);border-radius:6px;width:100%;margin-top:4px">
              </div>
              <div class="fg" style="flex:1">
                <label style="font-size:13px;font-weight:600;color:var(--muted)">Bis</label>
                <input type="time" name="bis"
                  style="padding:8px;border:1px solid var(--border);border-radius:6px;width:100%;margin-top:4px">
              </div>
            </div>
            <div class="fg" style="margin-bottom:12px">
              <label style="font-size:13px;font-weight:600;color:var(--muted)">Grund (optional)</label>
              <input type="text" name="grund" placeholder="z.B. Urlaub, Fortbildung…"
                style="padding:8px;border:1px solid var(--border);border-radius:6px;width:100%;margin-top:4px">
            </div>
            <button type="submit" class="btn red" style="width:100%">🔒 Zeit sperren</button>
          </form>
        </div>
      </div>
    </div>

    <!-- Blocked list -->
    <div class="panel">
      <div class="panel-head"><span class="panel-title">📋 Gesperrte Zeiten (ab heute)</span></div>
      <div class="panel-body">
        <?php if (!$blocked): ?>
        <div class="empty-msg">Keine gesperrten Zeiten.</div>
        <?php else: ?>
        <table style="width:100%;border-collapse:collapse">
          <thead>
            <tr>
              <th style="text-align:left;padding:8px;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border)">Datum</th>
              <th style="text-align:left;padding:8px;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border)">Zeit</th>
              <th style="text-align:left;padding:8px;font-size:13px;color:var(--muted);border-bottom:1px solid var(--border)">Grund</th>
              <th style="padding:8px;border-bottom:1px solid var(--border)"></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($blocked as $b): ?>
          <tr style="border-bottom:1px solid var(--border)">
            <td style="padding:10px 8px;font-weight:500"><?= date('d.m.Y', strtotime($b['datum'])) ?></td>
            <td style="padding:10px 8px;color:var(--muted);font-size:13px">
              <?= $b['von'] ? date('H:i', strtotime($b['von'])).' – '.date('H:i', strtotime($b['bis'])) : 'Ganzer Tag' ?>
            </td>
            <td style="padding:10px 8px;color:var(--muted);font-size:13px">
              <?= htmlspecialchars($b['grund'] ?: '—') ?>
            </td>
            <td style="padding:10px 8px">
              <form method="post" onsubmit="return confirm('Sperrung entfernen?')" style="margin:0">
                <input type="hidden" name="action" value="unblock">
                <input type="hidden" name="block_id" value="<?= $b['id'] ?>">
                <button class="btn ghost" style="padding:4px 10px;font-size:12px;color:var(--red)">🗑 Entfernen</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>

<script>
function toggleDay(num, active) {
  document.getElementById('von'+num).disabled = !active;
  document.getElementById('bis'+num).disabled = !active;
}
</script>
</body>
</html>
