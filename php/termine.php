<?php
session_start();
require_once "../includes/db_config.php";

// Zugriff nur f�r eingeloggte Nutzer
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];

// Termine aus DB laden
$sql = "
SELECT
    t.termin_id,
    t.datum,
    p.vorname,
    p.nachname,
    a.name AS arzt_name,
    f.name AS fachbereich
FROM termin t
JOIN arzt a ON t.arzt_id = a.arzt_id
JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
JOIN patient p ON t.patient_id = p.patient_id
ORDER BY t.datum ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="de">
<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Termin�bersicht</title>
  <link rel="stylesheet" href="style.css" />
  <script src="script.js" defer></script>

  <style>
    table, th, td {
      border: 1px solid black;
      border-collapse: collapse;
      padding: 6px;
      text-align: center;
    }
  </style>

</head>
<body>

<?php include 'header.php'; ?>   <!-- kopf bereich rufen -->

<main>

<h1>Termin�bersicht</h1>

<table>
<tr>
    <th>Datum</th>
    <th>Uhrzeit</th>
    <th>Arzt</th>
    <th>Fachbereich</th>
    <th>Status</th>
    <th>Aktion</th>
</tr>

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $datum = date("d.m.Y", strtotime($row['datum']));
    $zeit = date("H:i", strtotime($row['datum']));
    $status = (strtotime($row['datum']) < time()) ? "Abgeschlossen" : "Bevorstehend";
?>
<tr>
    <td><?= $datum ?></td>
    <td><?= $zeit ?></td>
    <td><?= htmlspecialchars($row['arzt_name']) ?></td>
    <td><?= htmlspecialchars($row['fachbereich']) ?></td>
    <td><?= $status ?></td>
    <td>
       <a href="termin-details.php?id=<?= $row['termin_id'] ?>">Details</a>

    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="6">Keine Termine vorhanden.</td>
</tr>
<?php endif; ?>
</table>

<br>
<a href="termin_neu.php" class="btn">+ Neuen Termin buchen</a>

</main>

<footer id="footer">
   2026 IDAS Gesundheitsportal . Hannover<br>
  Alle Rechte vorbehalten
</footer>


</body>
</html>
