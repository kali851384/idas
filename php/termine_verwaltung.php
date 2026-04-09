<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

$success = "";
$error = "";

// LÖSCHEN 
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = mysqli_prepare($conn, "DELETE FROM termin WHERE termin_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Termin wurde gelöscht.";
    } else {
        $error = "Fehler beim Löschen.";
    }
}

// BEARBEITEN SPEICHERN 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["termin_id"])) {
    $id          = (int)$_POST["termin_id"];
    $arzt_id     = (int)$_POST["arzt_id"];
    $patient_id  = (int)$_POST["patient_id"];
    $datum       = $_POST["datum"];
    $beschreibung = trim($_POST["beschreibung"]);

    $stmt = mysqli_prepare($conn,
        "UPDATE termin SET arzt_id=?, patient_id=?, datum=?, beschreibung=? WHERE termin_id=?"
    );
    mysqli_stmt_bind_param($stmt, "iissi", $arzt_id, $patient_id, $datum, $beschreibung, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Termin wurde aktualisiert.";
    } else {
        $error = "Fehler beim Speichern.";
    }
}

// BEARBEITEN FORMULAR LADEN 
$edit_termin = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = mysqli_prepare($conn, "SELECT * FROM termin WHERE termin_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_termin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

// ALLE TERMINE LADEN 
$termine = mysqli_query($conn,
    "SELECT t.termin_id, t.datum, t.beschreibung,
            a.name AS arzt_name,
            CONCAT(p.vorname, ' ', p.nachname) AS patient_name
     FROM termin t
     JOIN arzt a ON t.arzt_id = a.arzt_id
     JOIN patient p ON t.patient_id = p.patient_id
     ORDER BY t.datum DESC"
);

// ÄRZTE & PATIENTEN FÜR DROPDOWN 
$aerzte   = mysqli_query($conn, "SELECT arzt_id, name FROM arzt ORDER BY name");
$patienten = mysqli_query($conn, "SELECT patient_id, CONCAT(vorname,' ',nachname) AS name FROM patient ORDER BY nachname");
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Termine verwalten</title>

</head>
<body>

<div class="nav">
    <a href="admin_dashboard.php">← Dashboard</a>
    <a href="kontakt_verwaltung.php">Kontakt-Nachrichten</a>
    <a href="admin_logout.php">Logout</a>
</div>

<h1>Termine verwalten</h1>

<?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
<?php if ($error):   ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

<!-- BEARBEITUNGSFORMULAR -->
<?php if ($edit_termin): ?>
<div class="form-box">
    <h2>Termin bearbeiten</h2>
    <form method="post">
        <input type="hidden" name="termin_id" value="<?php echo $edit_termin["termin_id"]; ?>">

        <label>Arzt</label>
        <select name="arzt_id">
            <?php
            mysqli_data_seek($aerzte, 0);
            while ($a = mysqli_fetch_assoc($aerzte)):
                $sel = ($a["arzt_id"] == $edit_termin["arzt_id"]) ? "selected" : "";
            ?>
            <option value="<?php echo $a["arzt_id"]; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($a["name"]); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Patient</label>
        <select name="patient_id">
            <?php
            mysqli_data_seek($patienten, 0);
            while ($p = mysqli_fetch_assoc($patienten)):
                $sel = ($p["patient_id"] == $edit_termin["patient_id"]) ? "selected" : "";
            ?>
            <option value="<?php echo $p["patient_id"]; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($p["name"]); ?></option>
            <?php endwhile; ?>
        </select>

        <label>Datum & Uhrzeit</label>
        <input type="datetime-local" name="datum"
               value="<?php echo date("Y-m-d\TH:i", strtotime($edit_termin["datum"])); ?>">

        <label>Beschreibung</label>
        <textarea name="beschreibung" rows="4"><?php echo htmlspecialchars($edit_termin["beschreibung"] ?? ""); ?></textarea>

        <br><br>
        <button type="submit" class="btn btn-save">Speichern</button>
        <a href="termine_verwaltung.php" class="btn" style="background:#aaa;color:#fff;margin-left:10px;">Abbrechen</a>
    </form>
</div>
<?php endif; ?>


<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Datum</th>
            <th>Arzt</th>
            <th>Patient</th>
            <th>Beschreibung</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($t = mysqli_fetch_assoc($termine)): ?>
    <tr>
        <td><?php echo $t["termin_id"]; ?></td>
        <td><?php echo date("d.m.Y H:i", strtotime($t["datum"])); ?></td>
        <td><?php echo htmlspecialchars($t["arzt_name"]); ?></td>
        <td><?php echo htmlspecialchars($t["patient_name"]); ?></td>
        <td><?php echo htmlspecialchars($t["beschreibung"] ?? "—"); ?></td>
        <td>
            <a href="?edit=<?php echo $t["termin_id"]; ?>" class="btn btn-edit">Bearbeiten</a>
            <a href="?delete=<?php echo $t["termin_id"]; ?>" class="btn btn-delete"
               onclick="return confirm('Termin wirklich löschen?');">Löschen</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
