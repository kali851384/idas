<?php
require_once "../includes/db_config.php";
require "../includes/dbRead.php";
require "../includes/addressGeocoding.php"; 
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_POST["patient_id"];
    $symptoms = $_POST["symptomSelect"];
    $days = $_POST["symptomDays"];
    $address = $_POST["symptomAddress"];
    $city = $_POST["symptomCity"];
    $plz = $_POST["symptomPlz"];
    $maxDist = $_POST["symptomDist"];
    $symptomList = array_map("intval", $symptoms);
    $symptomIds = implode(",", $symptoms);
    $result = countSymptomDet($conn, $symptomIds);
    $row = $result->fetch_assoc();
    $fb = $row['fachbereich_id'];
    $doctors = readDoctors($conn, $fb);
    $addressFull = $address . " " . $plz;
    $fbName = readFbById($conn, $fb);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="../../../forend/css/style.css" /> 
		<link href="../../../forend/css/choices.css" " rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
    </head>
    <body>
        <?php include 'header.php';?> 
        
        <main>
            <div id="docDiv">
                <h1 class="symptomTitle">Arzt auswählen</h1>
				<p class="symptomDesc">Wählen Sie hier den gewünschten Arzt aus. Ihre Terminanfrage wird dann an den Arzt weitergeleitet</p>
                <?php
                if ($doctors != null) {
                    
                    while ($doctor = $doctors->fetch_assoc()) {
                        
                        $docId = $doctor["arzt_id"];
                        $docName = $doctor["name"];
                        $docFb = $fbName;
                        $docAddress = $doctor["addresse"];
                        $dist = getDistance($addressFull, $docAddress);
                        $docDist = round($dist / 1000, 0);
                        if ($dist <= $maxDist) {
                            echo "<form method='post' action='doctorSubmit.php' id='doctorForm{$docId}' class='doctorForm'>";
                            echo "<p><b>{$docName}</b> <br/> 
                                {$docFb} <br/>
                                {$docAddress} <br/>
                                {$docDist}km 
                            </p>";

                            echo " 
                            <input type='hidden' value='{$patient_id}' name='patientId'>
                            <input type='hidden' value='{$symptomIds}' name='symptoms'>
                            <input type='hidden' value='{$days}' name='days'>
                            <input type='hidden' value='{$addressFull}' name='address'>
                            <input type='hidden' value='{$docId}' name='docId'>
                            <input type='submit' value='Termin vereinbaren' class='docInputSubmit'>";
                            echo "</form";
                        }
                        
                    }
                    
                } else {
                    echo "<p>keine Ärzte gefunden<p/>";
                }
                ?>
                
            </div>
        </main>
        <footer id="footer">
   2026 IDAS Gesundheitsportal . Hannover<br>
  Alle Rechte vorbehalten
</footer>


<script src="../../JS/script.js" defer></script> <!-- JS teil um profil menü zu passen -->
    </body>
</html>