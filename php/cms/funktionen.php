<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

require_once "../includes/db_config.php";

$action = isset($_GET['action']) ? $_GET['action'] : 'start';
$module = isset($_GET['module']) ? $_GET['module'] : '';

$allowed_actions = ['start', 'dashboard', 'create', 'edit', 'delete', 'list'];
if (!in_array($action, $allowed_actions)) {
    $action = 'start';
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Bereich – Funktionen</title>
</head>
<body>

<h1>Admin Bereich</h1>

<p>Eingeloggt als: <?php echo htmlspecialchars($_SESSION["admin_user"]); ?></p>

<p>
    <a href="admin_logout.php">Logout</a> | 
    <a href="Funktionen.php">Zurück zur Übersicht</a>
</p>

<hr>

<?php if ($action === 'start' || $action === 'dashboard'): ?>

    <h2>Verfügbare Verwaltungsfunktionen</h2>

    <ul>
        <li><a href="Funktionen.php?module=aerzte&action=list">Ärzte & Fachbereiche verwalten</a></li>
        <li><a href="Funktionen.php?module=symptome&action=list">Symptome verwalten</a></li>
        <li><a href="konto_manager.php">Konten verwalten</a></li>
        <li><a href="kontakt.php">Kontakt-Nachrichten ansehen</a></li>
        <li><a href="Dashboard.php">Dashboard anzeigen</a></li>
    </ul>

<?php else: ?>

    <?php

    $include_file = '';

    if ($action === 'create' || $action === 'edit' || $action === 'delete') {
        $include_file = $action . '.php';
    } elseif ($action === 'list') {
        if ($module === 'aerzte') {
            $include_file = 'aerzte_verwaltung.php';
        } elseif ($module === 'symptome') {
            $include_file = 'symptome_verwaltung.php';
        }
    }

    if ($include_file && file_exists($include_file)) {
        include $include_file;
    } else {
        echo "<p style='color:red;'>Fehler: Datei nicht gefunden oder ungültige Anfrage.</p>";
        echo "<p>action = " . htmlspecialchars($action) . " | module = " . htmlspecialchars($module) . "</p>";
    }
    ?>

<?php endif; ?>

<hr>

<p><small>Admin Bereich – <?php echo date('d.m.Y H:i'); ?></small></p>

</body>
</html>