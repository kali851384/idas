<?php  
session_start();
include 'db_connect.php';   // db.php
 
 
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
 ORDER BY t.datum ASC
";

$stmt = $conn->prepare($sql);             // prepare bereitet die SQL-Anfrage vor
$stmt->execute();                         // execute führt sie aus
$result = $stmt->get_result();            // get_result liefert die Ergebnisse zurück
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

<!-- HEADER -->
<div id="header">
    <a href="index.php">
        <img id="siteLogo" src="images/siteLogo.png" width="72" height="72">
    </a>
    <h1><b>IDAS</b></h1>
    <h4 id="logoBanner">Intelligent Doctor Appointment System</h4>

    <!-- Profil -->
    <div id="navProfil">
        <img id="profilImg" src="profilImg.png" onclick="dropDownButton()">
        <div id="profilMenuSignedIn" class="dropdownDiv">
            <a href="konto.php" class="dropdownSignedIn">Konto anzeigen</a>
            <a href="logout.php" class="dropdownSignedIn">Abmelden</a>
        </div>
    </div>

    <!-- Navigation -->
    <nav id="naviBereich">
        <div id="navLinks">
            <a href="index.php" class="navLink">Start</a>
            <a href="symptome.php" class="navLink">Arzt finden</a>
            <a href="patientenakte.php" class="navLink">Patientenakte</a>
            <a href="termine.php" class="navLink">Termine</a>
            <a href="konto.php" class="navLink">Konto</a>
            <a href="kontakt.php" class="navLink">Kontakt</a>
        </div>
    </nav>
</div>



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
<?php while ($row = $result->fetch_assoc()): ?>     <!-- alle Termine holen -->
<?php
    $datum = date("d.m.Y", strtotime($row['datum']));
    $zeit = date("H:i", strtotime($row['datum']));
    $status = (strtotime($row['datum']) < time()) ? "Abgeschlossen" : "Bevorstehend";
?>
   <tr class="Terminzeilen">
      <td class="TerminDatum"><?= $datum ?></td>
          <td class="TerminZeit"><?= $zeit ?></td>
          <td class="TerminArzt"><?= htmlspecialchars($row['arzt_name']) ?></td>   <!-- von HTML probleme sichern -->
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

</body>
</html>
