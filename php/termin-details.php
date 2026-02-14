<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Ungültige Termin-ID.");
}

$termin_id = intval($_GET['id']);


$sql = "
SELECT
    t.datum, t.beschreibung,
    a.name AS arzt_name,
    a.email AS arzt_email,
    a.telefonnummer AS arzt_telefon,
    f.name AS fachbereich,
    p.vorname AS patient_vorname,
    p.nachname AS patient_nachname
FROM termin t
 JOIN arzt a ON t.arzt_id = a.arzt_id
 JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
 JOIN patient p ON t.patient_id = p.patient_id
 WHERE t.termin_id = ? AND t.patient_id = ?
";



$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $termin_id, $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$termin = $result->fetch_assoc();

if (!$termin) {
    die("Termin nicht gefunden oder kein Zugriff.");
}

// Status berechnen
$status = (strtotime($termin['datum']) < time())
    ? "Abgeschlossen"
    : "Bevorstehend";

// Termin absagen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $conn->prepare(
        "DELETE FROM termin WHERE termin_id = ? AND patient_id = ?"
    );
    $del->bind_param("ii", $termin_id, $patient_id);
    $del->execute();

    echo "<script>
        alert('Termin wurde abgesagt.');
        window.location.href='termine.php';
    </script>";
    exit;
}
?>




<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Termin Details</title>
  <link rel="stylesheet" href="style.css">


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

<div id="header">
    <a href="index.php">
        <img id="siteLogo" src="images/siteLogo.png" width="72">
    </a>
    <h1><b>IDAS</b></h1>
    <h4 id="logoBanner">Intelligent Doctor Appointment System</h4>
</div>


<main id="TerminDetails">

  <h1 id="TerminDetailsTitel">Termin Details</h1>

  <table id="TerminDetailsTabelle">

    <tbody id="TerminDetailsBody">

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Patient</th>
        <td class="TerminDetailsWert"> <?= htmlspecialchars($termin['patient_vorname'] . " " . $termin['patient_nachname']) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Datum</th>
        <td class="TerminDetailsWert"> <?= date("d.m.Y", strtotime($termin['datum'])) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Uhrzeit</th>
        <td class="TerminDetailsWert"> <?= date("H:i", strtotime($termin['datum'])) ?> Uhr
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Status</th>
        <td class="TerminDetailsWert <?= strtolower($status) ?>"> <?= $status ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Arzt</th>
        <td class="TerminDetailsWert">  <?= htmlspecialchars($termin['arzt_name']) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Fachbereich</th>
        <td class="TerminDetailsWert"><?= htmlspecialchars($termin['fachbereich']) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Arzt E-Mail</th>
        <td class="TerminDetailsWert"><?= htmlspecialchars($termin['arzt_email']) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Telefon</th>
        <td class="TerminDetailsWert"><?= htmlspecialchars($termin['arzt_telefon']) ?>
        </td>
      </tr>

      <tr class="TerminDetailsZeile">
        <th class="TerminDetailsLabel">Grund / Beschreibung</th>
        <td class="TerminDetailsWert"><?= nl2br(htmlspecialchars($termin['beschreibung'])) ?>
        </td>
      </tr>

    </tbody>
  </table>

  <div id="TerminDetailsAktion">
    <a href="termine.php" class="TerminDetailsZuruck">
      Zurück zur Übersicht
    </a>

    <form method="POST" style="display:inline;">
      <button type="submit"
              class="TerminDetailsLuschen"
              onclick="return confirm('Termin wirklich absagen?');">
        Termin absagen
      </button>
    </form>
  </div>

</main>


</body>
</html>


