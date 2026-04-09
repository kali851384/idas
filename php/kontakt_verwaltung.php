<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

$success = "";
$error = "";

//LÖSCHEN 
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    $stmt = mysqli_prepare($conn, "DELETE FROM kontakt_nachrichten WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Nachricht wurde gelöscht.";
    } else {
        $error = "Fehler beim Löschen.";
    }
}

//NACHRICHT SPEICHERN 
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nachricht_id"])) {
    $id        = (int)$_POST["nachricht_id"];
    $nachricht = trim($_POST["nachricht"]);

    $stmt = mysqli_prepare($conn, "UPDATE kontakt_nachrichten SET nachricht = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "si", $nachricht, $id);
    if (mysqli_stmt_execute($stmt)) {
        $success = "Nachricht wurde aktualisiert.";
    } else {
        $error = "Fehler beim Speichern.";
    }
}

//BEARBEITEN LADEN 
$edit_msg = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = mysqli_prepare($conn, "SELECT * FROM kontakt_nachrichten WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $edit_msg = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

//ALLE NACHRICHTEN LADEN 
$nachrichten = mysqli_query($conn,
    "SELECT * FROM kontakt_nachrichten ORDER BY datum DESC"
);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Kontakt-Nachrichten</title>
</head>
<body>

<div class="nav">
    <a href="admin_dashboard.php">← Dashboard</a>
    <a href="termine_verwaltung.php">Termine</a>
    <a href="admin_logout.php">Logout</a>
</div>

<h1>Kontakt-Nachrichten</h1>

<?php if ($success): ?><p class="success"><?php echo htmlspecialchars($success); ?></p><?php endif; ?>
<?php if ($error):   ?><p class="error"><?php echo htmlspecialchars($error); ?></p><?php endif; ?>

<!-- BEARBEITUNGSFORMULAR -->
<?php if ($edit_msg): ?>
<div class="form-box">
    <h2>Nachricht bearbeiten</h2>
    <p class="meta">
        Von: <strong><?php echo htmlspecialchars($edit_msg["vorname"] . " " . $edit_msg["nachname"]); ?></strong>
        &nbsp;|&nbsp; <?php echo htmlspecialchars($edit_msg["email"]); ?>
        &nbsp;|&nbsp; <?php echo date("d.m.Y H:i", strtotime($edit_msg["datum"])); ?>
    </p>
    <form method="post">
        <input type="hidden" name="nachricht_id" value="<?php echo $edit_msg["id"]; ?>">

        <label>Nachrichtentext</label>
        <textarea name="nachricht" rows="6"><?php echo htmlspecialchars($edit_msg["nachricht"]); ?></textarea>

        <br><br>
        <button type="submit" class="btn btn-save">Speichern</button>
        <a href="kontakt_verwaltung.php" class="btn" style="background:#aaa;color:#fff;margin-left:10px;">Abbrechen</a>
    </form>
</div>
<?php endif; ?>

<!-- NACHRICHTENLISTE -->
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Datum</th>
            <th>Name</th>
            <th>E-Mail</th>
            <th>Betreff</th>
            <th>Nachricht</th>
            <th>Aktionen</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($n = mysqli_fetch_assoc($nachrichten)): ?>
    <tr>
        <td><?php echo $n["id"]; ?></td>
        <td><?php echo date("d.m.Y H:i", strtotime($n["datum"])); ?></td>
        <td><?php echo htmlspecialchars($n["vorname"] . " " . $n["nachname"]); ?></td>
        <td><?php echo htmlspecialchars($n["email"]); ?></td>
        <td><?php echo htmlspecialchars($n["betreff"] ?? "—"); ?></td>
        <td><?php echo nl2br(htmlspecialchars(mb_strimwidth($n["nachricht"], 0, 80, "…"))); ?></td>
        <td>
            <a href="?edit=<?php echo $n["id"]; ?>" class="btn btn-edit">Bearbeiten</a>
            <a href="?delete=<?php echo $n["id"]; ?>" class="btn btn-delete"
               onclick="return confirm('Nachricht wirklich löschen?');">Löschen</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
