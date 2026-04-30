<?php
require_once "../includes/db_config.php";
require "../includes/dbRead.php";
require "../includes/addressGeocoding.php"; 
session_start();


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_POST["patient_id"];
    $symptoms = $_POST["symptomSelect"];
    $days = $_POST["symptomDays"];
    $address = $_POST["symptomAddress"];
    $city = $_POST["symptomCity"];
    $plz = $_POST["symptomPlz"];
    $symptomList = array_map("intval", $symptoms);
    $symptomIds = implode(",", $symptoms);
    $result = countSymptomDet($conn, $symptomIds);
    $row = $result->fetch_assoc();
    $fb = $row['fachbereich_id'];
    $doctors = readDoctors($conn, $fb);
    $addressFull = $address . " " . $plz;
    $maxDist = 25000;
}
?>

<!DOCTYPE html>
<html>
    <head>

    </head>
    <body>
        <main>
            <div>
                
                <?php
                if ($doctors != null) {
                    echo "<form method='post' id='doctorForm'>";
                    while ($doctor = $doctors->fetch_assoc()) {
                        $docId = $doctor["arzt_id"];
                        $docName = $doctor["name"];
                        $docFb = $doctor["fachbereich_id"];
                        $docAddress = $doctor["addresse"];
                        $dist = getDistance($addressFull, $docAddress);
                        if ($dist <= $maxDist) {
                            echo "<p>{$docName} <br/> 
                                {$docAddress} <br/>
                            </p>";
                            echo " <input type='hidden' value='{$docId}'>
                            <input type='submit' value='Termin vereinbaren'>";
                            }
                        }
                    echo "</form";
                } else {
                    echo "<p>keine ärzte gefunden</>";
                }
                ?>
                
            </div>
        </main>
    </body>
</html>