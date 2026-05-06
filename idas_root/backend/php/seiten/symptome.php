<?php	
session_start();
require "../includes/db_config.php";
require "../includes/dbRead.php";
require "../includes/addressGeocoding.php";
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
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
		<?php include 'header.php'; ?> 
		
		<main>
            <div id="symptomWrapper">
				<h1 class="symptomTitle">Symptome Eingeben</h1>
				<p class="symptomDesc">Geben sie Ihre Symptome ein. Danach können sie einen Arzt auswählen, mit dem Sie einen Termin vereinbaren wollen.</p>
 				<form method="post" action="findDoctor.php" id="symptomForm" class="">	<!--Übergibt patient id mit absenden des Formulars-->
					<input type="hidden" name="patient_id" value="<?php echo $_SESSION['patient_id']; ?>" id="symptomIdInput">
					<label for="symptomSelectInput" id="symtomSelectLabel" class="symptomFormLabel">Symptome</label> <br />
					<select name="symptomSelect[]" id="symptomSelectInput" class="symptomInput" multiple> 
						<?php
						$options = readSymptoms($conn);		//Symptome aus Datenbank auslesen
						while ($option = $options->fetch_assoc()) {
							$id = $option["symptom_id"];
							$name = $option["name"];
                            echo "<option value='{$id}'>" . htmlspecialchars($name) . "</option>"; //Symptome in select einsetzen
						}
						?>
					</select> <br />
					<label id="symptomRadioLabel" class="symptomFormLabel">Tage</label> <br />
					<label for="symptomInputDays_mo" class="symptomFormSublabel">Montag</label>
					<input type="checkbox" name="symptomDays" value="1" id="symptomInputDays_mo"/> <br />
					<label for="symptomInputDays_di" class="symptomFormSublabel">Dienstag</label>
					<input type="checkbox" name="symptomDays" value="2" id="symptomInputDays_di"/> <br />
					<label for="symptomInputDays_mi" class="symptomFormSublabel">Mittwoch</label>
					<input type="checkbox" name="symptomDays" value="3" id="symptomInputDays_mi"/> <br />
					<label for="symptomInputDays_do" class="symptomFormSublabel">Donnerstag</label>
					<input type="checkbox" name="symptomDays" value="4" id="symptomInputDays_do"/> <br />
					<label for="symptomInputDays_fr" class="symptomFormSublabel">Freitag</label>
					<input type="checkbox" name="symptomDays" value="5" id="symptomInputDays_fr"/> <br />
					<label for="symptomInputDays_sa" class="symptomFormSublabel">Samstag</label>
					<input type="checkbox" name="symptomDays" value="6" id="symptomInputDays_sa"/> <br />

					<label for="symptomAddressInput" id="symptomAddressLabel" class="symptomFormLabel">Adresse</label> <br />
					<input type="text" name="symptomAddress" id="symptomAddressInput" class="symptomInput"/> <br />

					<label for="symptomCityInput" id="symptomCityLabel" class="symptomFormLabel">Stadt</label> <br />
					<input type="text" name="symptomCity" id="symptomCityInput" class="symptomInput"/> <br />

					<label for="symptomPlzInput" id="symptomPlzLabel" class="symptomFormLabel">PLZ</label> <br />
					<input type="text" name="symptomPlz" id="symptomPlzInput" class="symptomInput"/> <br />
					<label class="symptomFormLabel">Entfernung</label > <br />
					<select name="symptomDist" class="symptomInput">
						<option value="5000">5km</option>
						<option value="10000">10km</option>
						<option value="25000">25km</option>
						<option value="50000">50km</option>
						<option value="100000">100km</option>
					</select> <br />
					<input type="submit" value="Bestätigen" id="symptomSubmit"/>
				</form>
			</div>
		</main>
		<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
		<script src="../../JS/select.js"" defer></script>
		<script src="../../JS/script.js"" defer></script>
		<footer id="footer">
			2026 IDAS Gesundheitsportal . Hannover<br>
			Alle Rechte vorbehalten
		</footer>


		<script src="../../JS/script.js" defer></script> <!-- JS teil um profil menü zu passen -->
	</body>
</html>