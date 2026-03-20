<?php
session_start();
require_once "../includes/db_config.php";


// Zugriff nur für eingeloggte nutzer
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];

$sql = "
SELECT
    t.termin_id, t.datum,
    a.name AS arzt_name,
    f.name AS fachbereich
FROM termin t
 JOIN arzt a ON t.arzt_id = a.arzt_id
 JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
 WHERE t.patient_id = ?
 ORDER BY t.datum ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result=$stmt->get_result();
?>



<!DOCTYPE html>
<html lang="de">
<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Terminübersicht</title>
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

<main id="TerminMain">

<h1 id="TerminTitel">Terminübersicht</h1>

  <table id="TerminTabelle">

 <thead id="TerminHead">
<tr>
    <th>Datum</th>
    <th>Uhrzeit</th>
    <th>Arzt</th>
    <th>Fachbereich</th>
    <th>Status</th>
    <th>Aktion</th>
</tr>
 </thead>

   <tbody id="TerminBody">

<?php if ($result->num_rows > 0): ?>
<?php while ($row = $result->fetch_assoc()): ?>
<?php
    $datum = date("d.m.Y", strtotime($row['datum']));
    $zeit = date("H:i", strtotime($row['datum']));
    $status = (strtotime($row['datum']) < time()) ? "Abgeschlossen" : "Bevorstehend";
?>
   <tr class="Terminzeilen">
      <td class="TerminDatum"><?= $datum ?></td>
          <td class="TerminZeit"><?= $zeit ?></td>
          <td class="TerminArzt"><?= htmlspecialchars($row['arzt_name']) ?></td>
          <td class="TerminFachbereich"><?= htmlspecialchars($row['fachbereich']) ?></td>
          <td class="TerminStatus"><?= $status ?></td>
          <td class="TerminAktion"> <a href="termin-details.php?id=<?= $row['termin_id'] ?>" class="TerminDetails"> Details </a>
          </td>
   </tr>
      <?php endwhile; ?>
    <?php else: ?>
      <tr class="TerminzeilenLeer">
        <td colspan="6">Keine Termine vorhanden.</td>
      </tr>
    <?php endif; ?>

    </tbody>

  </table>

</main>

    
<footer id="footer">
   2026 IDAS Gesundheitsportal . Hannover<br>
  Alle Rechte vorbehalten
</footer>

</body>
</html>


