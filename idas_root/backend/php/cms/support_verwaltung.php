<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";



/* ── POST ── */
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act = $_POST['action'] ?? '';
    $tid = intval($_POST['ticket_id'] ?? 0);

    if ($act === 'update_status') {
        $status      = $_POST['status'] ?? 'offen';
        $mitarbeiter = intval($_POST['mitarbeiter_id'] ?? 0) ?: null;
        $antwort     = trim($_POST['antwort'] ?? '');
        $allowed     = ['offen', 'in_bearbeitung', 'geschlossen'];
        if (!in_array($status, $allowed)) $status = 'offen';

        $s = mysqli_prepare($conn,
            "UPDATE support SET status=?, mitarbeiter_id=?, antwort=? WHERE ticket_id=?");
        mysqli_stmt_bind_param($s, "sisi", $status, $mitarbeiter, $antwort, $tid);
        mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Ticket #$tid aktualisiert.";
        $_SESSION['flash_type'] = "success";

    } elseif ($act === 'delete') {
        $s = mysqli_prepare($conn, "DELETE FROM support WHERE ticket_id=?");
        mysqli_stmt_bind_param($s, "i", $tid);
        mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Ticket #$tid gelöscht.";
        $_SESSION['flash_type'] = "success";
    }

    header("Location: support_verwaltung.php"); exit;
}

/* ── Flash ── */
$flash = $flash_type = "";
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

/* ── Filter ── */
$filterStatus = trim($_GET['status'] ?? '');
$search       = trim($_GET['q']      ?? '');
$conds = ["1=1"];
if (in_array($filterStatus, ['offen','in_bearbeitung','geschlossen'])) {
    $conds[] = "s.status = '$filterStatus'";
}
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $conds[] = "(p.vorname LIKE '%$esc%' OR p.nachname LIKE '%$esc%' OR s.problembeschreibung LIKE '%$esc%')";
}
$where = "WHERE " . implode(" AND ", $conds);

/* ── Load tickets ── */
$tickets = [];
$r = mysqli_query($conn,
    "SELECT s.ticket_id, s.problembeschreibung, s.betreff, s.status, s.datum, s.antwort,
            s.patient_id, s.mitarbeiter_id,
            p.vorname AS p_vorname, p.nachname AS p_nachname, p.email AS p_email,
            m.vorname AS m_vorname, m.nachname AS m_nachname
     FROM support s
     LEFT JOIN patient    p ON s.patient_id    = p.patient_id
     LEFT JOIN mitarbeiter m ON s.mitarbeiter_id = m.mitarbeiter_id
     $where
     ORDER BY
       CASE s.status WHEN 'offen' THEN 0 WHEN 'in_bearbeitung' THEN 1 ELSE 2 END,
       s.datum DESC");
while ($row = mysqli_fetch_assoc($r)) $tickets[] = $row;

/* ── Mitarbeiter for dropdown ── */
$mitarbeiter = [];
$r2 = mysqli_query($conn, "SELECT mitarbeiter_id, vorname, nachname FROM mitarbeiter ORDER BY nachname");
while ($row = mysqli_fetch_assoc($r2)) $mitarbeiter[] = $row;

/* ── Stats ── */
$stats = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT
        COUNT(*) AS gesamt,
        SUM(status='offen') AS offen,
        SUM(status='in_bearbeitung') AS in_bearbeitung,
        SUM(status='geschlossen') AS geschlossen
     FROM support"));

mysqli_close($conn);

// Status labels & colors
$statusLabel = ['offen'=>'Offen','in_bearbeitung'=>'In Bearbeitung','geschlossen'=>'Geschlossen'];
$statusColor = ['offen'=>'#e74c3c','in_bearbeitung'=>'#e67e22','geschlossen'=>'#27ae60'];
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Support-Tickets — IDAS CMS</title>
<link rel="stylesheet" href="cms_style.css">
<style>
.status-badge {
  display: inline-block; font-size: 11px; font-weight: 700;
  padding: 3px 9px; border-radius: 10px; white-space: nowrap;
}
.status-offen         { background: #fde8e8; color: #c0392b; }
.status-in_bearbeitung{ background: #fef3e2; color: #e67e22; }
.status-geschlossen   { background: #d4edda; color: #155724; }

.filter-pills { display: flex; gap: 6px; flex-wrap: wrap; }
.pill {
  padding: 5px 14px; border-radius: 20px; font-size: 12px; cursor: pointer;
  border: 1px solid var(--border); background: var(--surface);
  color: var(--muted); text-decoration: none; transition: all .15s;
}
.pill:hover { border-color: var(--blue); color: var(--blue); }
.pill.active { background: var(--blue); border-color: var(--blue); color: #fff; font-weight: 600; }
.pill.offen.active         { background: #c0392b; border-color: #c0392b; }
.pill.in_bearbeitung.active{ background: #e67e22; border-color: #e67e22; }
.pill.geschlossen.active   { background: #27ae60; border-color: #27ae60; }

.ticket-preview {
  font-size: 12px; color: var(--muted); max-width: 260px;
  white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}

/* Detail modal specific */
.detail-section { margin-bottom: 18px; }
.detail-section h4 { font-size: 12px; text-transform: uppercase; letter-spacing: 1px;
  color: var(--muted); margin-bottom: 8px; padding-bottom: 5px; border-bottom: 1px solid var(--border); }
.info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
.info-box { background: var(--bg); border-radius: 6px; padding: 9px 12px; }
.info-lbl { font-size: 11px; text-transform: uppercase; letter-spacing: .5px; color: var(--muted); }
.info-val { font-size: 13px; font-weight: 500; margin-top: 2px; }
.problem-text { background: var(--bg); border-radius: 6px; padding: 12px;
  font-size: 13px; line-height: 1.6; white-space: pre-wrap; }
</style>
</head>
<body class="padded">

<div class="topbar">
  <h1>🎫 Support-Tickets</h1>
  <a href="admin_dashboard.php"    class="btn ghost">← Dashboard</a>
  <a href="patienten_verwaltung.php" class="btn ghost">👤 Patienten</a>
  <a href="admin_logout.php"       class="btn ghost">Logout</a>
</div>

<?php if ($flash): ?>
<div class="flash <?= $flash_type ?>">
  <?= $flash_type==='success'?'✓':'✕' ?> <?= htmlspecialchars($flash) ?>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-row">
  <div class="stat-box">
    <div class="stat-num"><?= $stats['gesamt'] ?></div>
    <div class="stat-lbl">Gesamt</div>
  </div>
  <div class="stat-box" style="border-left: 3px solid #c0392b">
    <div class="stat-num" style="color:#c0392b"><?= $stats['offen'] ?></div>
    <div class="stat-lbl">Offen</div>
  </div>
  <div class="stat-box" style="border-left: 3px solid #e67e22">
    <div class="stat-num" style="color:#e67e22"><?= $stats['in_bearbeitung'] ?></div>
    <div class="stat-lbl">In Bearbeitung</div>
  </div>
  <div class="stat-box green">
    <div class="stat-num"><?= $stats['geschlossen'] ?></div>
    <div class="stat-lbl">Geschlossen</div>
  </div>
</div>

<!-- Filters -->
<form method="get" style="margin:0">
<div class="toolbar">
  <div class="search-form">
    <input type="search" name="q" value="<?= htmlspecialchars($search) ?>"
      placeholder="🔍 Patient oder Problembeschreibung…">
    <?php if ($filterStatus): ?>
      <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
    <?php endif; ?>
    <button class="btn blue" type="submit">Suchen</button>
  </div>
  <div class="filter-pills">
    <a href="support_verwaltung.php<?= $search ? '?q='.urlencode($search) : '' ?>"
       class="pill <?= !$filterStatus ? 'active' : '' ?>">Alle</a>
    <a href="?status=offen<?= $search ? '&q='.urlencode($search) : '' ?>"
       class="pill offen <?= $filterStatus==='offen' ? 'active' : '' ?>">🔴 Offen</a>
    <a href="?status=in_bearbeitung<?= $search ? '&q='.urlencode($search) : '' ?>"
       class="pill in_bearbeitung <?= $filterStatus==='in_bearbeitung' ? 'active' : '' ?>">🟠 In Bearbeitung</a>
    <a href="?status=geschlossen<?= $search ? '&q='.urlencode($search) : '' ?>"
       class="pill geschlossen <?= $filterStatus==='geschlossen' ? 'active' : '' ?>">🟢 Geschlossen</a>
  </div>
  <?php if ($search || $filterStatus): ?>
    <a href="support_verwaltung.php" class="btn ghost">✕ Zurücksetzen</a>
  <?php endif; ?>
  <span class="count-badge"><?= count($tickets) ?> Ticket<?= count($tickets)!==1?'s':'' ?></span>
</div>
</form>

<!-- Table -->
<div class="table-wrap">
<?php if (!$tickets): ?>
  <div class="empty">
    <p style="font-size:40px">🎫</p>
    <p style="margin-top:10px"><?= ($search||$filterStatus) ? 'Keine Tickets gefunden.' : 'Noch keine Support-Tickets vorhanden.' ?></p>
  </div>
<?php else: ?>
<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Patient</th>
      <th>Betreff</th>
      <th>Problem</th>
      <th>Status</th>
      <th>Datum</th>
      <th>Zugewiesen</th>
      <th>Aktionen</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $avColors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c','#1abc9c','#2980b9','#16a085'];
  foreach ($tickets as $t):
    $initials = $t['p_vorname'] ? strtoupper(substr($t['p_vorname'],0,1).substr($t['p_nachname'],0,1)) : '?';
    $aColor   = $t['patient_id'] ? $avColors[$t['patient_id'] % count($avColors)] : '#aaa';
  ?>
  <tr>
    <td><span class="id-chip">#<?= $t['ticket_id'] ?></span></td>
    <td>
      <div class="name-cell">
        <div class="avatar" style="background:<?= $aColor ?>"><?= $initials ?></div>
        <div>
          <div class="pat-name">
            <?= $t['p_vorname'] ? htmlspecialchars($t['p_vorname'].' '.$t['p_nachname']) : '<span style="color:var(--muted);font-style:italic">Unbekannt</span>' ?>
          </div>
          <?php if ($t['p_email']): ?>
          <div class="pat-meta"><?= htmlspecialchars($t['p_email']) ?></div>
          <?php endif; ?>
        </div>
      </div>
    </td>
    <td><?= $t['betreff'] ? htmlspecialchars($t['betreff']) : '<span style="color:var(--muted);font-style:italic">—</span>' ?></td>
    <td><div class="ticket-preview"><?= htmlspecialchars($t['problembeschreibung']) ?></div></td>
    <td>
      <span class="status-badge status-<?= $t['status'] ?>">
        <?= $statusLabel[$t['status']] ?>
      </span>
    </td>
    <td style="white-space:nowrap;font-size:12px;color:var(--muted)">
      <?= $t['datum'] ? date('d.m.Y H:i', strtotime($t['datum'])) : '—' ?>
    </td>
    <td style="font-size:12px">
      <?= $t['m_vorname'] ? htmlspecialchars($t['m_vorname'].' '.$t['m_nachname']) : '<span style="color:var(--muted);font-style:italic">Niemand</span>' ?>
    </td>
    <td>
      <div class="act-cell">
        <button class="btn blue" style="padding:4px 10px;font-size:12px"
          onclick='openTicket(<?= json_encode([
            "ticket_id"          => $t["ticket_id"],
            "p_name"             => $t["p_vorname"] ? $t["p_vorname"]." ".$t["p_nachname"] : "Unbekannt",
            "p_email"            => $t["p_email"] ?? "",
            "betreff"            => $t["betreff"] ?? "",
            "problembeschreibung"=> $t["problembeschreibung"],
            "status"             => $t["status"],
            "mitarbeiter_id"     => $t["mitarbeiter_id"] ?? "",
            "antwort"            => $t["antwort"] ?? "",
            "datum"              => $t["datum"] ?? ""
          ]) ?>)'>👁 Öffnen</button>
        <form method="post" onsubmit="return confirm('Ticket #<?= $t['ticket_id'] ?> löschen?')" style="margin:0">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="ticket_id" value="<?= $t['ticket_id'] ?>">
          <button class="btn ghost" style="padding:4px 10px;font-size:12px;color:var(--red);border-color:#f5c6cb">🗑</button>
        </form>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php endif; ?>
</div>

<!-- TICKET MODAL -->
<div class="ov" id="ticketModal">
  <div class="modal" style="max-width:620px">
    <button class="xbtn" id="btnClose">✕</button>
    <h3>🎫 Ticket <span id="mTicketId" style="color:var(--blue)"></span></h3>

    <form method="post">
      <input type="hidden" name="action" value="update_status">
      <input type="hidden" name="ticket_id" id="mTid">

      <div class="detail-section">
        <h4>Patient</h4>
        <div class="info-grid">
          <div class="info-box">
            <div class="info-lbl">Name</div>
            <div class="info-val" id="mPName"></div>
          </div>
          <div class="info-box">
            <div class="info-lbl">E-Mail</div>
            <div class="info-val" id="mPEmail"></div>
          </div>
        </div>
      </div>

      <div class="detail-section">
        <h4>Ticket</h4>
        <div class="info-grid" style="margin-bottom:10px">
          <div class="info-box">
            <div class="info-lbl">Betreff</div>
            <div class="info-val" id="mBetreff"></div>
          </div>
          <div class="info-box">
            <div class="info-lbl">Eingereicht am</div>
            <div class="info-val" id="mDatum"></div>
          </div>
        </div>
        <div class="info-lbl" style="margin-bottom:6px">Problembeschreibung</div>
        <div class="problem-text" id="mProblem"></div>
      </div>

      <div class="detail-section">
        <h4>Bearbeitung</h4>
        <div class="grid2" style="margin-bottom:12px">
          <div class="fg">
            <label>Status</label>
            <select name="status" id="mStatus">
              <option value="offen">Offen</option>
              <option value="in_bearbeitung">In Bearbeitung</option>
              <option value="geschlossen">Geschlossen</option>
            </select>
          </div>
          <div class="fg">
            <label>Zuweisen an Mitarbeiter</label>
            <select name="mitarbeiter_id" id="mMitarbeiter">
              <option value="">— Niemand —</option>
              <?php foreach ($mitarbeiter as $m): ?>
              <option value="<?= $m['mitarbeiter_id'] ?>">
                <?= htmlspecialchars($m['vorname'].' '.$m['nachname']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="fg">
          <label>Antwort / Notiz</label>
          <textarea name="antwort" id="mAntwort" rows="4"
            placeholder="Antwort an den Patienten oder interne Notiz…"></textarea>
        </div>
      </div>

      <div class="modal-actions">
        <button type="button" class="btn ghost" id="btnAbbrechen">Abbrechen</button>
        <button type="submit" class="btn green">✓ Speichern</button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var modal = document.getElementById('ticketModal');

  function formatDate(d) {
    if (!d) return '—';
    var dt = new Date(d);
    return dt.toLocaleDateString('de-DE') + ' ' + dt.toLocaleTimeString('de-DE', {hour:'2-digit',minute:'2-digit'});
  }

  window.openTicket = function(t) {
    document.getElementById('mTicketId').textContent  = '#' + t.ticket_id;
    document.getElementById('mTid').value             = t.ticket_id;
    document.getElementById('mPName').textContent     = t.p_name;
    document.getElementById('mPEmail').textContent    = t.p_email || '—';
    document.getElementById('mBetreff').textContent   = t.betreff || '—';
    document.getElementById('mDatum').textContent     = formatDate(t.datum);
    document.getElementById('mProblem').textContent   = t.problembeschreibung;
    document.getElementById('mStatus').value          = t.status;
    document.getElementById('mMitarbeiter').value     = t.mitarbeiter_id || '';
    document.getElementById('mAntwort').value         = t.antwort || '';
    modal.classList.add('on');
  };

  function closeModal() { modal.classList.remove('on'); }
  document.getElementById('btnClose').addEventListener('click', closeModal);
  document.getElementById('btnAbbrechen').addEventListener('click', closeModal);
  modal.addEventListener('click', function(e){ if(e.target===modal) closeModal(); });
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
}); // end DOMContentLoaded
</script>

<hr style="margin-top:40px;border:none;border-top:1px solid var(--border)">
<p style="margin-top:12px;font-size:12px;color:var(--muted)">Support-Tickets — <?= date('d.m.Y H:i') ?></p>
</body>
</html>
