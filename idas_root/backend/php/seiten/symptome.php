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
	
	<head> <!--DO NOT TOUCH-->
		<link rel="stylesheet" href="../../../forend/css/style.css" /> 
		<link href="../../../forend/css/choices.css" " rel="stylesheet">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta charset="UTF-8">
		
	</head>
	
	
	
	<body>
<!--Titel und Logo--> 
		<div id="header">
			<a href="index.html"><img id="siteLogo" src="images/siteLogo.png" width="72px" height="72px"></a> 
			<h1><b>IDAS</b></h1> 
			<h4 id="logoBanner">Intelligent Doctor Appointment System</h4>
			
			
<!--Profil-->
			<div id="navProfil"> 
				<img id="profilImg" src="profilImg.png" onclick="dropDownButton()"> 
				<div id="profilMenuSignedOut" class="dropdownDiv"> 	<!--Dropdown wenn nicht angemeldet-->
					<button onclick="signInButton()" id="buttonLogIn" class="dropdownLoggedOut">Anmelden</button>
				</div>
				<div id="profilMenuSignedIn" class="dropdownDiv"> 	<!--dropdown wenn angemeldet-->
					<a href="konto.html" id="dropdownKonto" class="dropdownSignedIn">Konto anzeigen</a>
					<a href="kontoVerwaltung.html" id="dropdownVerwaltung" class="dropdownSignedIn">>Konto verwalten</a>
					<button onclick="signOutButton()" id="buttonLogOut" class="dropdownSignedIn">Abmelden</button>
				</div>
			</div>
			
			
<!--Navigationsbereich--> 
			<nav id="naviBereich"> 
				<div id="navLinks">
					<a href="index.html" id="linkIndex" class="navLink">Start</a> 
					<a href="symptome.html" id="linkSymptome" class="navLink">Arzt finden</a> 
					<a href="patientenakte.html" id="linkAkte" class="navLink">Patienakte</a> 
					<a href="termine.html" id="linkTermine" class="navLink">Termine</a> 
					<a href="konto.html" id="linkKonto" class="navLink">Konto</a> 
				</div>
			</nav>
		</div>
		

<!--Seiteninhalt-->		
		<main>
            <div id="symptomWrapper">
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
					<label id="symptomRadioLabel">Tage</label> <br />
					<label for="symptomInputDays_mo">Montag</label>
					<input type="checkbox" name="symptomDays" value="1" id="symptomInputDays_mo"/> <br />
					<label for="symptomInputDays_di">Dienstag</label>
					<input type="checkbox" name="symptomDays" value="2" id="symptomInputDays_di"/> <br />
					<label for="symptomInputDays_mi">Mittwoch</label>
					<input type="checkbox" name="symptomDays" value="3" id="symptomInputDays_mi"/> <br />
					<label for="symptomInputDays_do">Donnerstag</label>
					<input type="checkbox" name="symptomDays" value="4" id="symptomInputDays_do"/> <br />
					<label for="symptomInputDays_fr">Freitag</label>
					<input type="checkbox" name="symptomDays" value="5" id="symptomInputDays_fr"/> <br />
					<label for="symptomInputDays_sa">Samstag</label>
					<input type="checkbox" name="symptomDays" value="6" id="symptomInputDays_sa"/> <br />

					<label for="symptomAddressInput" id="symptomAddressLabel" class="symptomFormLabel">Adresse</label> <br />
					<input type="text" name="symptomAddress" id="symptomAddressInput" class="symptomInput"/> <br />

					<label for="symptomCityInput" id="symptomCityLabel" class="symptomFormLabel">Stadt</label> <br />
					<input type="text" name="symptomCity" id="symptomCityInput" class="symptomInput"/> <br />

					<label for="symptomPlzInput" id="symptomPlzLabel" class="symptomFormLabel">PLZ</label> <br />
					<input type="text" name="symptomPlz" id="symptomPlzInput" class="symptomInput"/> <br />
					<input type="submit"/>
				</form>
			</div>
		</main>
		<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
		<script src="../../JS/select.js"" defer></script>
		<script src="../../JS/script.js"" defer></script>
	</body>
</html>