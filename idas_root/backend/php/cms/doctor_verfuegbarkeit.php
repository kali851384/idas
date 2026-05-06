<?php
session_start();
if (!isset($_SESSION["arzt_id"])) { header("Location: doctor_login.php"); exit; }
require_once "../includes/db_config.php";

$arzt_id = $_SESSION["arzt_id"];
$flash = $flash_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['action'] ?? '';

    if ($act === 'block') {
        $datum = trim($_POST['datum'] ?? '');
        $von   = trim($_POST['von'] ?? '') ?: null;
        $bis   = trim($_POST['bis'] ?? '') ?: null;
        $grund = trim($_POST['grund'] ?? '') ?: null;
        if ($datum) {
            $s = mysqli_prepare($conn, "INSERT INTO arzt_blocked_slots (arzt_id, datum, von, bis, grund) VALUES (?,?,?,?,?)");
            mysqli_stmt_bind_param($s, "issss", $arzt_id, $datum, $von, $bis, $grund);
            mysqli_stmt_execute($s);
            $_SESSION['flash'] = "Zeit gesperrt."; $_SESSION['flash_type'] = "success";
        }

    } elseif ($act === 'unblock') {
        $id = intval($_POST['block_id']);
        $s  = mysqli_prepare($conn, "DELETE FROM arzt_blocked_slots WHERE id=? AND arzt_id=?");
        mysqli_stmt_bind_param($s, "ii", $id, $arzt_id);
        mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Sperrung entfernt."; $_SESSION['flash_type'] = "success";

    } elseif ($act === 'save_hours') {
        $days = [1=>'Mo',2=>'Di',3=>'Mi',4=>'Do',5=>'Fr',6=>'Sa',7=>'So'];
        foreach ($days as $num => $day) {
            $von    = trim($_POST["von_$num"] ?? '');
            $bis    = trim($_POST["bis_$num"] ?? '');
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
        $_SESSION['flash'] = "Arbeitszeiten gespeichert."; $_SESSION['flash_type'] = "success";
    }
    header("Location: doctor_verfuegbarkeit.php"); exit;
}

if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

// Load blocked slots
$blocked = [];
$r = mysqli_prepare($conn, "SELECT * FROM arzt_blocked_slots WHERE arzt_id=? AND datum >= CURDATE() ORDER BY datum ASC");
mysqli_stmt_bind_param($r, "i", $arzt_id);
mysqli_stmt_execute($r);
$res = mysqli_stmt_get_result($r);
while ($row = mysqli_fetch_assoc($res)) $blocked[] = $row;

// Load working hours
$hours = [];
$r2 = mysqli_prepare($conn, "SELECT * FROM arzt_arbeitszeiten WHERE arzt_id=? ORDER BY wochentag");
mysqli_stmt_bind_param($r2, "i", $arzt_id);
mysqli_stmt_execute($r2);
$res2 = mysqli_stmt_get_result($r2);
while ($row = mysqli_fetch_assoc($res2)) $hours[$row['wochentag']] = $row;

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
        <div>
          <div class="user-name"><?= htmlspecialchars($_SESSION["arzt_name"]) ?></div>
          <div class="user-role">Arzt</div>
        </div>
      </div>
      <a href="doctor_logout.php">⎋ Logout</a>
    </div>
  </aside>

  <main class="main">
    <div class="page-header">
      <div>
        <div class="page-title">Verfügbarkeit verwalten</div>
        <div class="page-subtitle">Arbeitszeiten &amp; gesperrte Zeiten</div>
      </div>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="panels">

      <!-- Working hours -->
      <div class="panel" style="flex:1.5">
        <div class="panel-head"><span class="panel-title">🕐 Reguläre Arbeitszeiten</span></div>
        <div class="panel-body" style="padding:20px">
          <form method="post">
            <input type="hidden" name="action" value="save_hours">
            <table style="width:100%;border-collapse:collapse">
              <thead>
                <tr>
                  <th style="text-align:left;padding:8px 12px;font-size:12px;color:var(--muted);background:var(--bg);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Tag</th>
                  <th style="text-align:center;padding:8px 12px;font-size:12px;color:var(--muted);background:var(--bg);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Aktiv</th>
                  <th style="text-align:left;padding:8px 12px;font-size:12px;color:var(--muted);background:var(--bg);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Von</th>
                  <th style="text-align:left;padding:8px 12px;font-size:12px;color:var(--muted);background:var(--bg);font-weight:600;text-transform:uppercase;letter-spacing:.5px">Bis</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($days as $num => $day):
                $h = $hours[$num] ?? null;
              ?>
              <tr style="border-top:1px solid var(--border)">
                <td style="padding:12px;font-weight:500;font-size:14px"><?= $day ?></td>
                <td style="padding:12px;text-align:center">
                  <input type="checkbox" name="active_<?= $num ?>" id="a<?= $num ?>"
                    <?= $h ? 'checked' : '' ?>
                    onchange="toggleDay(<?= $num ?>, this.checked)">
                </td>
                <td style="padding:12px">
                  <input type="time" name="von_<?= $num ?>" id="von<?= $num ?>"
                    value="<?= $h ? substr($h['von'],0,5) : '08:00' ?>"
                    <?= !$h ? 'disabled' : '' ?>
                    class="filter-sel">
                </td>
                <td style="padding:12px">
                  <input type="time" name="bis_<?= $num ?>" id="bis<?= $num ?>"
                    value="<?= $h ? substr($h['bis'],0,5) : '17:00' ?>"
                    <?= !$h ? 'disabled' : '' ?>
                    class="filter-sel">
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
            <div style="margin-top:20px">
              <button type="submit" class="btn green">💾 Arbeitszeiten speichern</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Block time -->
      <div class="panel" style="flex:1">
        <div class="panel-head"><span class="panel-title">🔒 Zeit sperren</span></div>
        <div class="panel-body" style="padding:20px">
          <form method="post">
            <input type="hidden" name="action" value="block">
            <div class="fg">
              <label>Datum *</label>
              <input type="date" name="datum" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="grid2">
              <div class="fg">
                <label>Von</label>
                <input type="time" name="von">
              </div>
              <div class="fg">
                <label>Bis</label>
                <input type="time" name="bis">
              </div>
            </div>
            <div class="fg">
              <label>Grund (optional)</label>
              <input type="text" name="grund" placeholder="z.B. Urlaub, Fortbildung…">
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
        <table>
          <thead>
            <tr>
              <th>Datum</th><th>Zeit</th><th>Grund</th><th></th>
            </tr>
          </thead>
          <tbody>
          <?php foreach ($blocked as $b): ?>
          <tr>
            <td><strong><?= date('d.m.Y', strtotime($b['datum'])) ?></strong></td>
            <td><?= $b['von'] ? date('H:i', strtotime($b['von'])).' – '.date('H:i', strtotime($b['bis'])) : 'Ganzer Tag' ?></td>
            <td><?= htmlspecialchars($b['grund'] ?: '—') ?></td>
            <td>
              <form method="post" onsubmit="return confirm('Sperrung entfernen?')" style="margin:0">
                <input type="hidden" name="action" value="unblock">
                <input type="hidden" name="block_id" value="<?= $b['id'] ?>">
                <button class="btn ghost" style="color:var(--red)">🗑 Entfernen</button>
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