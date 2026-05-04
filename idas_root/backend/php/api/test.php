<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>IDAS API Tester</title>
<style>
body{font-family:Arial,sans-serif;max-width:900px;margin:30px auto;padding:0 20px;background:#f0f2f5}
h1{color:#2c3e50}h2{color:#2980b9;margin-top:28px;border-bottom:2px solid #ddd;padding-bottom:6px}
.box{background:#fff;border:1px solid #ddd;border-radius:8px;padding:16px;margin-bottom:16px}
label{font-size:13px;font-weight:bold;color:#555;display:block;margin-bottom:4px}
input,select,textarea{padding:7px 10px;border:1px solid #ccc;border-radius:4px;font-size:14px;width:100%;margin-bottom:10px}
button{padding:8px 18px;background:#2980b9;color:#fff;border:none;border-radius:5px;cursor:pointer;font-size:13px}
button:hover{background:#1f618d}
.result{background:#1a202c;color:#a8d8a8;padding:14px;border-radius:6px;font-family:monospace;font-size:13px;white-space:pre-wrap;margin-top:10px;min-height:60px}
.result.error{color:#f47067}
.token-display{background:#eaf7ea;border:1px solid #c3e6cb;border-radius:6px;padding:10px;margin:10px 0;font-family:monospace;font-size:12px;word-break:break-all}
.green{color:#27ae60;font-weight:bold}
.red{color:#c0392b;font-weight:bold}
</style>
</head>
<body>
<h1>🔧 IDAS API Tester</h1>
<p>Teste alle API-Endpunkte. Starte mit Login um einen Token zu bekommen.</p>

<div id="tokenBox" style="display:none">
  <div class="token-display">
    Token: <span id="tokenVal"></span>
    <button onclick="clearToken()" style="padding:3px 8px;font-size:11px;margin-left:10px">✕ Löschen</button>
  </div>
</div>

<!-- LOGIN -->
<h2>1. Login</h2>
<div class="box">
  <label>E-Mail</label>
  <input type="email" id="loginEmail" value="mohammadmirzayan20@gmail.com">
  <label>Passwort</label>
  <input type="password" id="loginPw" value="123">
  <button onclick="testLogin()">POST login.php</button>
  <div class="result" id="loginResult">—</div>
</div>

<!-- REGISTER -->
<h2>2. Register</h2>
<div class="box">
  <label>Vorname</label><input type="text" id="regVorname" value="Test">
  <label>Nachname</label><input type="text" id="regNachname" value="Patient">
  <label>E-Mail</label><input type="email" id="regEmail" value="test@test.de">
  <label>Passwort</label><input type="password" id="regPw" value="Test1234!">
  <label>Geburtsdatum</label><input type="date" id="regGeb" value="1990-01-01">
  <button onclick="testRegister()">POST register.php</button>
  <div class="result" id="registerResult">—</div>
</div>

<!-- SYMPTOME -->
<h2>3. Symptome laden</h2>
<div class="box">
  <p style="font-size:13px;color:#666">Braucht einen gültigen Token (zuerst einloggen)</p>
  <button onclick="testSymptome()">GET symptome.php</button>
  <div class="result" id="symptomeResult">—</div>
</div>

<!-- MATCHING -->
<h2>4. Matching (Arzt-Empfehlung)</h2>
<div class="box">
  <label>Symptom IDs (kommagetrennt, z.B. 1,3,11)</label>
  <input type="text" id="matchSymptome" value="1,3,11">
  <button onclick="testMatching()">GET matching.php</button>
  <div class="result" id="matchingResult">—</div>
</div>

<!-- TERMINE -->
<h2>5. Meine Termine</h2>
<div class="box">
  <button onclick="testTermine()">GET termine.php</button>
  <div class="result" id="termineResult">—</div>
</div>

<!-- TERMIN BUCHEN -->
<h2>6. Termin buchen</h2>
<div class="box">
  <label>Arzt ID</label><input type="number" id="buchArzt" value="1">
  <label>Datum & Uhrzeit</label><input type="datetime-local" id="buchDatum">
  <label>Beschreibung</label><input type="text" id="buchBeschr" value="Testtermin">
  <button onclick="testBuchen()">POST termin_buchen.php</button>
  <div class="result" id="buchenResult">—</div>
</div>

<!-- PROFIL -->
<h2>7. Profil laden</h2>
<div class="box">
  <button onclick="testProfil()">GET profil.php</button>
  <div class="result" id="profilResult">—</div>
</div>

<script>
var token = localStorage.getItem('idas_test_token') || '';
if (token) showToken();

function showToken() {
  document.getElementById('tokenBox').style.display = 'block';
  document.getElementById('tokenVal').textContent = token;
}
function clearToken() {
  token = '';
  localStorage.removeItem('idas_test_token');
  document.getElementById('tokenBox').style.display = 'none';
}

function show(id, data, isError) {
  var el = document.getElementById(id);
  el.textContent = JSON.stringify(data, null, 2);
  el.className = 'result' + (isError ? ' error' : '');
}

function getBase() {
  return window.location.href.replace('test.php','');
}

async function testLogin() {
  var fd = new FormData();
  fd.append('email',    document.getElementById('loginEmail').value);
  fd.append('passwort', document.getElementById('loginPw').value);
  try {
    var r = await fetch(getBase()+'login.php', {method:'POST', body:fd});
    var d = await r.json();
    show('loginResult', d, !d.success);
    if (d.success && d.token) {
      token = d.token;
      localStorage.setItem('idas_test_token', token);
      showToken();
    }
  } catch(e) { show('loginResult', {error: e.message}, true); }
}

async function testRegister() {
  var fd = new FormData();
  fd.append('vorname',      document.getElementById('regVorname').value);
  fd.append('nachname',     document.getElementById('regNachname').value);
  fd.append('email',        document.getElementById('regEmail').value);
  fd.append('passwort',     document.getElementById('regPw').value);
  fd.append('geburtsdatum', document.getElementById('regGeb').value);
  try {
    var r = await fetch(getBase()+'register.php', {method:'POST', body:fd});
    var d = await r.json();
    show('registerResult', d, !d.success);
  } catch(e) { show('registerResult', {error: e.message}, true); }
}

async function testSymptome() {
  try {
    var r = await fetch(getBase()+'symptome.php?token='+token);
    var d = await r.json();
    show('symptomeResult', d, !d.success);
  } catch(e) { show('symptomeResult', {error: e.message}, true); }
}

async function testMatching() {
  var ids = document.getElementById('matchSymptome').value;
  try {
    var r = await fetch(getBase()+'matching.php?symptome='+ids+'&token='+token);
    var d = await r.json();
    show('matchingResult', d, !d.success);
  } catch(e) { show('matchingResult', {error: e.message}, true); }
}

async function testTermine() {
  try {
    var r = await fetch(getBase()+'termine.php?token='+token);
    var d = await r.json();
    show('termineResult', d, !d.success);
  } catch(e) { show('termineResult', {error: e.message}, true); }
}

async function testBuchen() {
  var fd = new FormData();
  fd.append('arzt_id',      document.getElementById('buchArzt').value);
  fd.append('datum',        document.getElementById('buchDatum').value.replace('T',' ') + ':00');
  fd.append('beschreibung', document.getElementById('buchBeschr').value);
  fd.append('token',        token);
  try {
    var r = await fetch(getBase()+'termin_buchen.php', {method:'POST', body:fd});
    var d = await r.json();
    show('buchenResult', d, !d.success);
  } catch(e) { show('buchenResult', {error: e.message}, true); }
}

async function testProfil() {
  try {
    var r = await fetch(getBase()+'profil.php?token='+token);
    var d = await r.json();
    show('profilResult', d, !d.success);
  } catch(e) { show('profilResult', {error: e.message}, true); }
}

// Set default datetime
document.getElementById('buchDatum').value = new Date(Date.now()+86400000).toISOString().slice(0,16);
</script>
</body>
</html>
