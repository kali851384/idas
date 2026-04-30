<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

$msg = $msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act  = $_POST['action'] ?? '';
    $id   = intval($_POST['symptom_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $fbs  = array_map('intval', $_POST['fachbereiche'] ?? []);

    if ($act === 'delete') {
        $s = mysqli_prepare($conn, "DELETE FROM symptome WHERE symptom_id = ?");
        mysqli_stmt_bind_param($s, "i", $id);
        $msg = mysqli_stmt_execute($s) ? "Symptom gelöscht." : mysqli_error($conn);
        $msg_type = $msg === "Symptom gelöscht." ? "success" : "error";

    } elseif ($act === 'create' || $act === 'update') {
        if ($name === '')           { $msg = "Name darf nicht leer sein."; $msg_type = "error"; }
        elseif (strlen($name) > 60) { $msg = "Name max. 60 Zeichen.";      $msg_type = "error"; }
        else {
            if ($act === 'create') {
                $s = mysqli_prepare($conn, "INSERT INTO symptome (name) VALUES (?)");
                mysqli_stmt_bind_param($s, "s", $name);
                mysqli_stmt_execute($s);
                $id = mysqli_insert_id($conn);
                $msg = "Symptom erstellt.";
            } else {
                $s = mysqli_prepare($conn, "UPDATE symptome SET name=? WHERE symptom_id=?");
                mysqli_stmt_bind_param($s, "si", $name, $id);
                mysqli_stmt_execute($s);
                $msg = "Symptom aktualisiert.";
            }
            $msg_type = "success";
            $d = mysqli_prepare($conn, "DELETE FROM symptomdet WHERE symptom_id=?");
            mysqli_stmt_bind_param($d, "i", $id); mysqli_stmt_execute($d);
            if ($fbs) {
                $ins = mysqli_prepare($conn, "INSERT INTO symptomdet (symptom_id, fachbereich_id) VALUES (?,?)");
                foreach ($fbs as $fb) { mysqli_stmt_bind_param($ins, "ii", $id, $fb); mysqli_stmt_execute($ins); }
            }
        }
    }
}

$symptoms = [];
$r = mysqli_query($conn,
    "SELECT s.symptom_id, s.name,
            GROUP_CONCAT(f.name ORDER BY f.name SEPARATOR ', ') AS fachbereiche,
            GROUP_CONCAT(f.fachbereich_id ORDER BY f.name SEPARATOR ',') AS fb_ids
     FROM symptome s
     LEFT JOIN symptomdet sd ON s.symptom_id = sd.symptom_id
     LEFT JOIN fachbereich f  ON sd.fachbereich_id = f.fachbereich_id
     GROUP BY s.symptom_id ORDER BY s.name");
while ($row = mysqli_fetch_assoc($r)) $symptoms[] = $row;

$fachbereiche = [];
$r2 = mysqli_query($conn, "SELECT * FROM fachbereich ORDER BY name");
while ($row = mysqli_fetch_assoc($r2)) $fachbereiche[] = $row;
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Symptome verwalten</title>
<style>
  *{box-sizing:border-box} body{font-family:Arial,sans-serif;max-width:1000px;margin:30px auto;padding:0 20px;color:#333}
  h1{color:#2c3e50} h2{color:#34495e;border-bottom:2px solid #eee;padding-bottom:5px;margin-top:28px}
  .msg{padding:10px 16px;border-radius:4px;margin-bottom:16px}
  .success{background:#d4edda;color:#155724;border:1px solid #c3e6cb}
  .error{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb}
  table{width:100%;border-collapse:collapse;margin-top:8px}
  th{background:#2c3e50;color:#fff;padding:9px 12px;text-align:left}
  td{padding:8px 12px;border-bottom:1px solid #ddd;vertical-align:middle}
  tr:hover td{background:#f5f5f5}
  tr.voice-match td{background:#f0fff4 !important}
  tr.voice-match td:first-child{border-left:3px solid #27ae60}
  .tag{display:inline-block;background:#eaf4fb;color:#1a6a9a;border:1px solid #aad4ef;font-size:12px;padding:2px 7px;border-radius:10px;margin:2px}
  .none{color:#aaa;font-style:italic;font-size:13px}
  .btn{padding:6px 14px;border:none;border-radius:4px;cursor:pointer;font-size:13px}
  .blue{background:#2980b9;color:#fff} .blue:hover{background:#1f618d}
  .green{background:#27ae60;color:#fff} .green:hover{background:#1e8449}
  .red{background:#c0392b;color:#fff} .red:hover{background:#96281b}
  .grey{background:#7f8c8d;color:#fff} .grey:hover{background:#626567}
  input[type=text],input[type=search]{padding:7px 10px;border:1px solid #ccc;border-radius:4px;font-size:14px;width:100%}
  .cb-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(190px,1fr));gap:5px;max-height:200px;overflow-y:auto;padding:8px;background:#fff;border:1px solid #ccc;border-radius:4px}
  .cb-grid label{display:flex;align-items:center;gap:6px;font-size:13px;cursor:pointer}
  .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:100;justify-content:center;align-items:center}
  .modal-overlay.active{display:flex}
  .modal{background:#fff;border-radius:8px;padding:24px;width:100%;max-width:540px;max-height:90vh;overflow-y:auto;position:relative;box-shadow:0 8px 32px rgba(0,0,0,.25)}
  .modal h3{margin:0 0 16px;color:#2c3e50}
  .close-btn{position:absolute;top:10px;right:14px;background:none;border:none;font-size:22px;cursor:pointer;color:#aaa}
  .close-btn:hover{color:#333}
  .row{display:flex;gap:8px;margin-top:16px;justify-content:flex-end}
  .small-btns{display:flex;gap:6px;margin-bottom:5px}
  .small-btns button{font-size:12px;padding:2px 9px;background:#ecf0f1;border:1px solid #bdc3c7;border-radius:3px;cursor:pointer}

  /* Search bar */
  .search-wrap{display:flex;gap:8px;align-items:center;margin-bottom:10px}
  .search-wrap input{max-width:300px}
  #countLabel{font-size:13px;color:#666;flex:1}

  /* Mic button */
  .mic-btn{
    width:36px;height:36px;border-radius:50%;border:2px solid #ccc;
    background:#fff;font-size:16px;cursor:pointer;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;transition:all .2s;position:relative;
  }
  .mic-btn:hover{border-color:#2980b9;background:#f0f7ff}
  .mic-btn.listening{
    border-color:#e53e3e;background:#fff5f5;
    animation:mic-pulse 1s infinite;
  }
  @keyframes mic-pulse{
    0%,100%{box-shadow:0 0 0 0 rgba(229,62,62,.3)}
    50%    {box-shadow:0 0 0 7px rgba(229,62,62,0)}
  }

  /* Voice status toast */
  #voiceToast{
    display:none;position:fixed;bottom:28px;left:50%;transform:translateX(-50%);
    background:#1a202c;color:#fff;padding:10px 20px;border-radius:24px;
    font-size:14px;z-index:999;align-items:center;gap:10px;
    box-shadow:0 4px 20px rgba(0,0,0,.3);
  }
  #voiceToast.show{display:flex}
  .toast-dot{width:8px;height:8px;border-radius:50%;background:#e53e3e;animation:mic-pulse 1s infinite}

  /* Voice match banner */
  #voiceBanner{
    display:none;background:#f0fff4;border:1px solid #9ae6b4;border-radius:8px;
    padding:10px 16px;margin-bottom:10px;font-size:13px;color:#276749;
    align-items:center;gap:10px;
  }
  #voiceBanner.show{display:flex}
  #voiceBanner button{margin-left:auto;background:none;border:none;cursor:pointer;font-size:16px;color:#276749}

  /* No-support warning */
  .no-support{background:#fff3cd;border:1px solid #f6ad55;border-radius:6px;padding:8px 14px;font-size:13px;color:#7b4f00;margin-bottom:10px;display:none}
</style>
</head>
<body>

<h1>🩺 Symptome verwalten</h1>
<p>Eingeloggt als <strong><?= htmlspecialchars($_SESSION["admin_user"]) ?></strong> &nbsp;|&nbsp;
   <a href="admin_dashboard.php">← Dashboard</a> &nbsp;|&nbsp; <a href="admin_logout.php">Logout</a></p>

<?php if ($msg): ?>
  <div class="msg <?= $msg_type ?>"><?= htmlspecialchars($msg) ?></div>
<?php endif; ?>

<h2>Alle Symptome</h2>

<div class="no-support" id="noSupport">
  ⚠️ Spracheingabe wird in diesem Browser nicht unterstützt. Bitte Chrome oder Edge verwenden.
</div>

<!-- Voice match banner -->
<div id="voiceBanner">
  <span>🎤</span>
  <span id="bannerText"></span>
  <button onclick="clearVoice()" title="Zurücksetzen">✕</button>
</div>

<div class="search-wrap">
  <input type="search" id="q" placeholder="🔍 Symptom suchen…" oninput="filter(this.value)">
  <button class="mic-btn" id="micBtn" onclick="toggleVoice()" title="Spracheingabe">🎤</button>
  <span id="countLabel"></span>
  <button class="btn green" onclick="openModal()">✚ Neues Symptom</button>
</div>

<table id="tbl">
  <thead><tr><th>ID</th><th>Name</th><th>Fachbereiche</th><th>Aktionen</th></tr></thead>
  <tbody>
  <?php foreach ($symptoms as $s): ?>
  <tr data-name="<?= strtolower(htmlspecialchars($s['name'])) ?>" data-id="<?= $s['symptom_id'] ?>">
    <td><?= $s['symptom_id'] ?></td>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?php
      if ($s['fachbereiche'])
        foreach (explode(', ', $s['fachbereiche']) as $t) echo "<span class='tag'>".htmlspecialchars($t)."</span>";
      else echo "<span class='none'>Keine</span>";
    ?></td>
    <td style="display:flex;gap:6px">
      <button class="btn blue" onclick='openModal(<?= $s["symptom_id"] ?>,<?= json_encode($s["name"]) ?>,<?= json_encode($s["fb_ids"] ?? "") ?>)'>✏️ Bearbeiten</button>
      <form method="post" onsubmit="return confirm('Symptom löschen?')">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="symptom_id" value="<?= $s['symptom_id'] ?>">
        <button class="btn red">🗑 Löschen</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
  </tbody>
</table>

<!-- MODAL -->
<div class="modal-overlay" id="modal">
  <div class="modal">
    <button class="close-btn" onclick="closeModal()" type="button">✕</button>
    <h3 id="modalTitle">Symptom</h3>
    <form method="post">
      <input type="hidden" name="action" id="formAction" value="create">
      <input type="hidden" name="symptom_id" id="formId">
      <label style="font-size:13px;font-weight:bold">Name (max. 60 Zeichen)</label>
      <input type="text" name="name" id="formName" required maxlength="60" style="margin-top:5px;margin-bottom:12px">
      <label style="font-size:13px;font-weight:bold">Fachbereiche</label>
      <div class="small-btns">
        <button type="button" onclick="toggleAll(true)">Alle</button>
        <button type="button" onclick="toggleAll(false)">Keine</button>
      </div>
      <div class="cb-grid" id="cbGrid">
        <?php foreach ($fachbereiche as $fb): ?>
        <label><input type="checkbox" class="fb-cb" name="fachbereiche[]" value="<?= $fb['fachbereich_id'] ?>"> <?= htmlspecialchars($fb['name']) ?></label>
        <?php endforeach; ?>
      </div>
      <div class="row">
        <button type="button" class="btn grey" onclick="closeModal()">Abbrechen</button>
        <button type="submit" class="btn blue">Speichern</button>
      </div>
    </form>
  </div>
</div>

<!-- Voice toast -->
<div id="voiceToast">
  <div class="toast-dot"></div>
  <span id="toastText">Sprechen Sie jetzt…</span>
</div>

<script>
// ── Filter ──────────────────────────────────────────────
const TOTAL = <?= count($symptoms) ?>;

function filter(q) {
  q = (q || '').toLowerCase().trim();
  let shown = 0;
  document.querySelectorAll('#tbl tbody tr').forEach(tr => {
    const name = tr.dataset.name;
    const match = !q || name.includes(q);
    tr.style.display = match ? '' : 'none';
    if (match) shown++;
  });
  document.getElementById('countLabel').textContent =
    q ? `${shown} von ${TOTAL} Symptomen` : `${TOTAL} Symptome`;
}
filter('');

// ── Modal ────────────────────────────────────────────────
function openModal(id, name, fbIds) {
  const create = !id;
  document.getElementById('modalTitle').textContent = create ? '✚ Neues Symptom' : '✏️ Symptom bearbeiten';
  document.getElementById('formAction').value = create ? 'create' : 'update';
  document.getElementById('formId').value    = id   || '';
  document.getElementById('formName').value  = name || '';
  const assigned = fbIds ? new Set(fbIds.split(',').map(Number)) : new Set();
  document.querySelectorAll('.fb-cb').forEach(cb => cb.checked = assigned.has(+cb.value));
  document.getElementById('modal').classList.add('active');
  setTimeout(() => document.getElementById('formName').focus(), 50);
}
function closeModal() { document.getElementById('modal').classList.remove('active'); }
document.getElementById('modal').addEventListener('click', e => { if (e.target === document.getElementById('modal')) closeModal(); });
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });
function toggleAll(v) { document.querySelectorAll('.fb-cb').forEach(cb => cb.checked = v); }

// ── Voice Search ─────────────────────────────────────────
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
let recognition = null, listening = false;

if (!SpeechRecognition) {
  document.getElementById('noSupport').style.display = 'block';
  document.getElementById('micBtn').style.display = 'none';
} else {
  recognition = new SpeechRecognition();
  recognition.lang = 'de-DE';
  recognition.continuous = false;
  recognition.interimResults = true;

  recognition.onresult = function(e) {
    let interim = '', final = '';
    for (let i = e.resultIndex; i < e.results.length; i++) {
      const t = e.results[i][0].transcript;
      e.results[i].isFinal ? final += t : interim += t;
    }
    // Show live interim in toast
    if (interim) document.getElementById('toastText').textContent = '🎤 ' + interim;
    if (final)   handleVoiceResult(final.trim());
  };

  recognition.onerror = function(e) {
    stopVoice();
    if (e.error !== 'no-speech') showToast('Fehler: ' + e.error, 2000);
  };

  recognition.onend = stopVoice;
}

function toggleVoice() { listening ? stopVoice() : startVoice(); }

function startVoice() {
  if (!recognition) return;
  clearVoice();
  recognition.start();
  listening = true;
  document.getElementById('micBtn').classList.add('listening');
  showToast('Sprechen Sie jetzt…');
}

function stopVoice() {
  if (recognition && listening) try { recognition.stop(); } catch(e) {}
  listening = false;
  document.getElementById('micBtn').classList.remove('listening');
  document.getElementById('micBtn').textContent = '🎤';
  hideToast();
}

function handleVoiceResult(spoken) {
  stopVoice();
  if (!spoken) return;

  const spokenLower = spoken.toLowerCase();

  // 1. Try exact / contains match first
  const rows = [...document.querySelectorAll('#tbl tbody tr')];
  let bestRow = null, bestScore = -1;

  rows.forEach(tr => {
    const name = tr.dataset.name; // already lowercase
    let score = 0;

    if (name === spokenLower)           score = 100;      // exact
    else if (name.includes(spokenLower)) score = 80;      // spoken is substring of name
    else if (spokenLower.includes(name)) score = 70;      // name is substring of spoken
    else {
      // Word-level fuzzy: count matching words
      const spokenWords = spokenLower.split(/\s+/);
      const nameWords   = name.split(/\s+/);
      const hits = spokenWords.filter(w => nameWords.some(n => n.includes(w) || w.includes(n)));
      if (hits.length > 0) score = 50 + hits.length * 10;

      // Partial char match (handles slight mispronunciation)
      if (score === 0) {
        const overlap = longestCommonSubstring(spokenLower, name);
        if (overlap >= 4) score = overlap * 3;
      }
    }

    if (score > bestScore) { bestScore = score; bestRow = tr; }
  });

  const THRESHOLD = 20; // minimum score to count as a match

  if (bestRow && bestScore >= THRESHOLD) {
    const matchedName = bestRow.cells[1].textContent;

    // Highlight matched row
    rows.forEach(tr => tr.classList.remove('voice-match'));
    bestRow.classList.add('voice-match');

    // Put into search bar and filter
    const input = document.getElementById('q');
    input.value = matchedName;
    filter(matchedName);

    // Scroll to row
    bestRow.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Show banner
    showBanner(`"${spoken}" → ${matchedName} gefunden`);

    // Speak confirmation
    speak(`${matchedName} gefunden`);

  } else {
    // No match — still filter by what was said
    document.getElementById('q').value = spoken;
    filter(spoken);
    showBanner(`"${spoken}" — kein direkter Treffer, Suche läuft…`);
  }
}

// Longest common substring length (for fuzzy matching)
function longestCommonSubstring(a, b) {
  let max = 0;
  for (let i = 0; i < a.length; i++) {
    for (let j = 0; j < b.length; j++) {
      let len = 0;
      while (i + len < a.length && j + len < b.length && a[i + len] === b[j + len]) len++;
      if (len > max) max = len;
    }
  }
  return max;
}

function clearVoice() {
  document.querySelectorAll('#tbl tbody tr').forEach(tr => tr.classList.remove('voice-match'));
  document.getElementById('q').value = '';
  filter('');
  hideBanner();
}

// ── Toast helpers ────────────────────────────────────────
let toastTimer = null;
function showToast(text, autohideMs) {
  const t = document.getElementById('voiceToast');
  document.getElementById('toastText').textContent = text;
  t.classList.add('show');
  clearTimeout(toastTimer);
  if (autohideMs) toastTimer = setTimeout(hideToast, autohideMs);
}
function hideToast() {
  document.getElementById('voiceToast').classList.remove('show');
}

// ── Banner helpers ───────────────────────────────────────
function showBanner(text) {
  document.getElementById('bannerText').textContent = text;
  document.getElementById('voiceBanner').classList.add('show');
}
function hideBanner() {
  document.getElementById('voiceBanner').classList.remove('show');
}

// ── TTS confirmation ─────────────────────────────────────
function speak(text) {
  if (!window.speechSynthesis) return;
  speechSynthesis.cancel();
  const u = new SpeechSynthesisUtterance(text);
  u.lang = 'de-DE'; u.rate = 1.05;
  speechSynthesis.speak(u);
}
</script>

<hr style="margin-top:40px">
<p><small>Symptome verwalten – <?= date('d.m.Y H:i') ?></small></p>
</body>
</html>