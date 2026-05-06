<?php
session_start();
if (!isset($_SESSION["admin_id"])) { header("Location: admin_login.php"); exit; }
require_once "../includes/db_config.php";

$uploadDir = __DIR__ . '/../uploads/aerzte/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
$photoBase = '../uploads/aerzte/';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $act      = $_POST['action'] ?? '';
    $id       = intval($_POST['arzt_id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $fb       = intval($_POST['fachbereich_id'] ?? 0);
    $tel      = trim($_POST['telefonnummer'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $fax      = trim($_POST['fax'] ?? '');
    $addresse = trim($_POST['addresse'] ?? '');

    if ($act === 'delete') {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM arzt WHERE arzt_id=".intval($id)));
        if ($row && $row['foto'] && file_exists($uploadDir.$row['foto'])) unlink($uploadDir.$row['foto']);
        $s = mysqli_prepare($conn, "DELETE FROM arzt WHERE arzt_id=?");
        mysqli_stmt_bind_param($s, "i", $id); mysqli_stmt_execute($s);
        $_SESSION['flash'] = "Arzt gelöscht."; $_SESSION['flash_type'] = "success";

    } elseif ($act === 'create' || $act === 'update') {
        if ($name === '') {
            $_SESSION['flash'] = "Name ist ein Pflichtfeld."; $_SESSION['flash_type'] = "error";
        } elseif ($fb <= 0) {
            $_SESSION['flash'] = "Bitte Fachbereich wählen."; $_SESSION['flash_type'] = "error";
        } else {
            $fotoFilename = null;
            if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg','image/png','image/webp','image/gif'];
                $mime    = mime_content_type($_FILES['foto']['tmp_name']);
                if (!in_array($mime, $allowed)) {
                    $_SESSION['flash'] = "Nur JPG, PNG, WEBP oder GIF erlaubt."; $_SESSION['flash_type'] = "error";
                    header("Location: aerzte_verwaltung.php"); exit;
                }
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                $fotoFilename = 'arzt_'.($act==='update'?$id:time()).'_'.uniqid().'.'.$ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir.$fotoFilename);
            }

            if ($act === 'create') {
                $s = mysqli_prepare($conn, "INSERT INTO arzt (name,fachbereich_id,telefonnummer,email,fax,addresse,foto) VALUES (?,?,?,?,?,?,?)");
                mysqli_stmt_bind_param($s, "sisssss", $name, $fb, $tel, $email, $fax, $addresse, $fotoFilename);
                $_SESSION['flash'] = "Arzt hinzugefügt.";
            } else {
                if ($fotoFilename) {
                    $old = mysqli_fetch_assoc(mysqli_query($conn, "SELECT foto FROM arzt WHERE arzt_id=".intval($id)));
                    if ($old && $old['foto'] && file_exists($uploadDir.$old['foto'])) unlink($uploadDir.$old['foto']);
                    $s = mysqli_prepare($conn, "UPDATE arzt SET name=?,fachbereich_id=?,telefonnummer=?,email=?,fax=?,addresse=?,foto=? WHERE arzt_id=?");
                    mysqli_stmt_bind_param($s, "sisssssi", $name, $fb, $tel, $email, $fax, $addresse, $fotoFilename, $id);
                } else {
                    $s = mysqli_prepare($conn, "UPDATE arzt SET name=?,fachbereich_id=?,telefonnummer=?,email=?,fax=?,addresse=? WHERE arzt_id=?");
                    mysqli_stmt_bind_param($s, "sissssi", $name, $fb, $tel, $email, $fax, $addresse, $id);
                }
                $_SESSION['flash'] = "Arzt aktualisiert.";
            }
            mysqli_stmt_execute($s);
            $_SESSION['flash_type'] = "success";
        }
    }
    header("Location: aerzte_verwaltung.php"); exit;
}

$flash = $flash_type = "";
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash']; $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash'], $_SESSION['flash_type']);
}

$search = trim($_GET['q'] ?? '');
$where  = "";
if ($search !== '') {
    $esc   = mysqli_real_escape_string($conn, $search);
    $where = "WHERE a.name LIKE '%$esc%' OR f.name LIKE '%$esc%' OR a.email LIKE '%$esc%'";
}

$aerzte = [];
$r = mysqli_query($conn,
    "SELECT a.*, f.name AS fachbereich_name
     FROM arzt a JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
     $where ORDER BY a.name");
while ($row = mysqli_fetch_assoc($r)) $aerzte[] = $row;

$fachbereiche = [];
$r2 = mysqli_query($conn, "SELECT * FROM fachbereich ORDER BY name");
while ($row = mysqli_fetch_assoc($r2)) $fachbereiche[] = $row;

/* Admin name for sidebar */
$adminName = $_SESSION['admin_name'] ?? 'Admin';
$adminInitial = strtoupper($adminName[0] ?? 'A');

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>Ärzte — IDAS CMS</title>
<link rel="stylesheet" href="cms_style.css">
</head>
<body>

<div class="layout">

  <!-- ── SIDEBAR ── -->
  <aside class="sidebar">
    <div class="sidebar-logo">
      <div class="logo-word">ID<span>AS</span></div>
      <div class="sidebar-sub">CMS Admin</div>
    </div>

    <nav>
      <div class="nav-label">Übersicht</div>
      <a href="admin_dashboard.php" class="nav-link">
        <span class="icon">🏠</span> Dashboard
      </a>

      <div class="nav-label">Verwaltung</div>
      <a href="symptome_verwaltung.php" class="nav-link">
        <span class="icon">🔬</span> Symptome
      </a>
      <a href="aerzte_verwaltung.php" class="nav-link active">
        <span class="icon">🩺</span> Ärzte
      </a>
      <a href="patienten_verwaltung.php" class="nav-link">
        <span class="icon">👤</span> Patienten
      </a>
      <a href="termine_verwaltung.php" class="nav-link">
        <span class="icon">📅</span> Termine
      </a>
      <a href="diagnose_verwaltung.php" class="nav-link">
        <span class="icon">📋</span> Diagnosen
      </a>
      <a href="kontakt_verwaltung.php" class="nav-link">
        <span class="icon">✉️</span> Nachrichten
      </a>

      <a href="support_verwaltung.php" class="nav-link">
        <span class="icon">🎧</span> Support
      </a>
      <a href="konto_verwaltung.php" class="nav-link">
        <span class="icon">🔐</span> Konten
      </a>
    </nav>

    <div class="sidebar-footer">
      <div class="user-chip">
        <div class="user-av"><?= htmlspecialchars($adminInitial) ?></div>
        <div>
          <div class="user-name"><?= htmlspecialchars($adminName) ?></div>
          <div class="user-role">Administrator</div>
        </div>
      </div>
      <a href="admin_logout.php">⇠ Abmelden</a>
    </div>
  </aside>

  <!-- ── MAIN CONTENT ── -->
  <div class="main">

    <div class="topbar">
      <h1>🩺 Ärzte</h1>
      <button class="btn green" onclick="openModal()">✚ Arzt hinzufügen</button>
    </div>

    <?php if ($flash): ?>
    <div class="flash <?= $flash_type ?>"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="toolbar">
      <form class="search-form" method="get">
        <input type="search" name="q" value="<?= htmlspecialchars($search) ?>" placeholder="🔍 Name, Fachbereich, E-Mail…">
        <button class="btn blue" type="submit">Suchen</button>
        <?php if ($search): ?><a href="aerzte_verwaltung.php" class="btn ghost">✕</a><?php endif; ?>
      </form>
      <span class="count-badge"><?= count($aerzte) ?> Arzt/Ärzte</span>
    </div>

    <div class="table-wrap">
    <?php if (!$aerzte): ?>
      <div class="empty"><p style="font-size:40px">🩺</p><p style="margin-top:10px">Keine Ärzte gefunden.</p></div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Arzt</th>
          <th>Fachbereich</th>
          <th>Kontakt</th>
          <th>Adresse</th>
          <th>Aktionen</th>
        </tr>
      </thead>
      <tbody>
      <?php
      $avColors = ['#3498db','#27ae60','#8e44ad','#e67e22','#e74c3c','#1abc9c','#2980b9','#16a085'];
      foreach ($aerzte as $a):
        $words    = array_slice(explode(' ', $a['name']), 0, 2);
        $initials = strtoupper(implode('', array_map(function($w){ return $w[0]; }, $words)));
        $aColor   = $avColors[crc32($a['name']) % count($avColors)];
        $hasPhoto = !empty($a['foto']) && file_exists($uploadDir.$a['foto']);
        $photoUrl = $hasPhoto ? htmlspecialchars($photoBase.$a['foto']) : null;
      ?>
      <tr>
        <td style="color:var(--muted);font-family:monospace">#<?= $a['arzt_id'] ?></td>
        <td>
          <div class="name-cell">
            <?php if ($photoUrl): ?>
              <img class="doc-photo" src="<?= $photoUrl ?>" alt="">
            <?php else: ?>
              <div class="doc-initials" style="background:<?= $aColor ?>"><?= $initials ?></div>
            <?php endif; ?>
            <div>
              <div class="doc-name"><?= htmlspecialchars($a['name']) ?></div>
              <?php if ($a['email']): ?>
              <div class="doc-meta"><?= htmlspecialchars($a['email']) ?></div>
              <?php endif; ?>
            </div>
          </div>
        </td>
        <td><span class="fb-badge"><?= htmlspecialchars($a['fachbereich_name']) ?></span></td>
        <td>
          <?php if ($a['telefonnummer']): ?><div>📞 <?= htmlspecialchars($a['telefonnummer']) ?></div><?php endif; ?>
          <?php if ($a['fax']): ?><div style="color:var(--muted);font-size:12px">📠 <?= htmlspecialchars($a['fax']) ?></div><?php endif; ?>
          <?php if (!$a['telefonnummer'] && !$a['fax']): ?><span style="color:var(--muted);font-style:italic;font-size:12px">—</span><?php endif; ?>
        </td>
        <td style="font-size:12px;color:var(--muted);max-width:180px">
          <?= $a['addresse'] ? htmlspecialchars($a['addresse']) : '<span style="font-style:italic">—</span>' ?>
        </td>
        <td>
          <div class="act-cell">
            <button class="btn blue" style="padding:4px 10px;font-size:12px"
              onclick='openModal(<?= json_encode([
                "arzt_id"        => $a["arzt_id"],
                "name"           => $a["name"],
                "fachbereich_id" => $a["fachbereich_id"],
                "telefonnummer"  => $a["telefonnummer"],
                "email"          => $a["email"],
                "fax"            => $a["fax"],
                "addresse"       => $a["addresse"],
                "foto"           => $a["foto"]
              ]) ?>)'>✏️ Bearbeiten</button>
            <form method="post" onsubmit="return confirm('Arzt löschen?')" style="margin:0">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="arzt_id" value="<?= $a['arzt_id'] ?>">
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

    <p style="margin-top:20px;font-size:12px;color:var(--muted)">Ärzte verwalten — <?= date('d.m.Y H:i') ?></p>

  </div><!-- /.main -->
</div><!-- /.layout -->

<!-- CREATE / EDIT MODAL -->
<div class="ov" id="editModal">
  <div class="modal">
    <button class="xbtn" onclick="closeModal()">✕</button>
    <h3 id="modalTitle">Arzt hinzufügen</h3>
    <form method="post" enctype="multipart/form-data">
      <input type="hidden" name="action" id="formAction" value="create">
      <input type="hidden" name="arzt_id" id="formId">

      <div class="section-title">Foto</div>
      <div class="upload-area" onclick="document.getElementById('fotoInput').click()">
        <input type="file" name="foto" id="fotoInput" accept="image/*"
               onchange="previewPhoto(this)" onclick="event.stopPropagation()">
        <div id="uploadDisplay">
          <div class="upload-placeholder">📷</div>
          <div class="upload-txt"><strong>Foto hochladen</strong> oder klicken</div>
          <div class="upload-hint">JPG, PNG, WEBP — empfohlen quadratisch</div>
        </div>
      </div>

      <div class="section-title">Stammdaten</div>
      <div class="fg"><label>Name *</label><input type="text" name="name" id="fName" required maxlength="40" placeholder="Dr. Max Mustermann"></div>
      <div class="fg">
        <label>Fachbereich *</label>
        <select name="fachbereich_id" id="fFb" required>
          <option value="">— bitte wählen —</option>
          <?php foreach ($fachbereiche as $fb): ?>
          <option value="<?= $fb['fachbereich_id'] ?>"><?= htmlspecialchars($fb['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="section-title">Kontakt</div>
      <div class="grid2">
        <div class="fg"><label>E-Mail</label><input type="email" name="email" id="fEmail" placeholder="arzt@klinik.de"></div>
        <div class="fg"><label>Telefon</label><input type="text" name="telefonnummer" id="fTel" placeholder="+49 511 …"></div>
      </div>
      <div class="fg"><label>Fax</label><input type="text" name="fax" id="fFax" placeholder="+49 511 …"></div>

      <div class="section-title">Adresse</div>
      <div class="fg"><label>Adresse</label><input type="text" name="addresse" id="fAddresse" placeholder="Musterstraße 1, 30159 Hannover" maxlength="255"></div>

      <div class="modal-actions">
        <button type="button" class="btn ghost" onclick="closeModal()">Abbrechen</button>
        <button type="submit" class="btn green">Speichern</button>
      </div>
    </form>
  </div>
</div>

<script>
var PHOTO_BASE = '<?= addslashes($photoBase) ?>';

function openModal(d) {
  var create = !d;
  document.getElementById('modalTitle').textContent = create ? '✚ Arzt hinzufügen' : '✏️ Arzt bearbeiten';
  document.getElementById('formAction').value = create ? 'create' : 'update';
  document.getElementById('formId').value    = d ? d.arzt_id        : '';
  document.getElementById('fName').value     = d ? d.name           : '';
  document.getElementById('fFb').value       = d ? d.fachbereich_id : '';
  document.getElementById('fEmail').value    = d ? (d.email         || '') : '';
  document.getElementById('fTel').value      = d ? (d.telefonnummer || '') : '';
  document.getElementById('fFax').value      = d ? (d.fax           || '') : '';
  document.getElementById('fAddresse').value = d ? (d.addresse      || '') : '';
  document.getElementById('fotoInput').value = '';

  var disp = document.getElementById('uploadDisplay');
  if (d && d.foto) {
    disp.innerHTML = '<img src="'+PHOTO_BASE+d.foto+'" class="upload-preview" alt=""><div class="upload-txt">Neues Foto wählen zum <strong>Ersetzen</strong></div><div class="upload-hint">Aktuelles Foto bleibt wenn kein neues gewählt</div>';
  } else {
    disp.innerHTML = '<div class="upload-placeholder">📷</div><div class="upload-txt"><strong>Foto hochladen</strong> oder klicken</div><div class="upload-hint">JPG, PNG, WEBP — empfohlen quadratisch</div>';
  }

  document.getElementById('editModal').classList.add('on');
  setTimeout(function(){ document.getElementById('fName').focus(); }, 50);
}

function closeModal() { document.getElementById('editModal').classList.remove('on'); }
document.getElementById('editModal').addEventListener('click', function(e){ if(e.target===this) closeModal(); });
document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });

function previewPhoto(input) {
  if (!input.files || !input.files[0]) return;
  var reader = new FileReader();
  reader.onload = function(e) {
    document.getElementById('uploadDisplay').innerHTML =
      '<img src="'+e.target.result+'" class="upload-preview" alt=""><div class="upload-txt" style="color:var(--green)">✓ Foto ausgewählt</div><div class="upload-hint">Klicken um anderes Foto zu wählen</div>';
  };
  reader.readAsDataURL(input.files[0]);
}
</script>

</body>
</html>