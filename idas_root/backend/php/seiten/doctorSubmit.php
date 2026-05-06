<?php
session_start();
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $patient_id = $_POST["patientId"];
    $symptomIds = $_POST["symptoms"];
	$days = $_POST["days"];
	$address = $_POST["address"];
	$doctor = $_POST["docId"];

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
		<main>
			<div id="docSubmit">
				<p>Ihre Terminanfrage wurde Versendet</p>
				<a href="index.php">
					<button>Ok.</button>
				</a>
			</div>
		</main>
	</body>
</html>