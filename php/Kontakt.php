<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}                                            

$msg="";
$vorname=$nachname=$email=$telefon=$betreff=$nachricht="";

// datensätze aus formular lesen
if ($_SERVER["REQUEST_METHOD"]=="POST") {
    $vorname=trim($_POST['vorname']?? '');          //trim für leerzeilen am anfang/ende löschen
    $nachname=trim($_POST['nachname']?? '');
    $email=trim($_POST['email']?? '');
    $telefon=trim($_POST['telefon']?? '');
    $betreff=trim($_POST['betreff']?? '');
    $nachricht=trim($_POST['nachricht']?? '');

// prüfen ob alle felder gefullt
    if (empty($vorname) || empty($nachname) || empty($email) || empty($betreff) || empty($nachricht)) {
        $msg= "Bitte alle Pflichtfelder ausfüllen!";    }

    
    // E-Mail prüfen 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg = "Ungültige E-Mail-Adresse.";
    }
    
    else {
        $stmt = $conn->prepare("INSERT INTO kontakt_nachrichten (vorname,nachname,email,telefon,betreff,nachricht)VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $vorname,$nachname,$email,$telefon,$betreff,$nachricht);

        if ($stmt->execute()) {
            $msg = "Nachricht wurde erfolgreich gesendet!";     
            $vorname=$nachname=$email=$telefon=$betreff=$nachricht="";
        } else {
            $msg = "Fehler beim Speichern: " . $stmt->error;
        }
      
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontakt - IDAS</title>
  <link rel="stylesheet" href="style.css" />
  <script src="script.js" defer></script>
</head>
<body>

<div id="header">
    <a href="index.php"><img id="siteLogo" src="images/siteLogo.png" width="72px" height="72px"></a>
    <h1><b>IDAS</b></h1>
    <h4 id="logoBanner">Intelligent Doctor Appointment System</h4>

    <div id="navProfil">
        <img id="profilImg" src="profilImg.png" onclick="dropDownButton()">
        <div id="profilMenuSignedIn" class="dropdownDiv">
            <a href="konto.php" class="dropdownSignedIn">Konto anzeigen</a>
            <a href="logout.php" class="dropdownSignedIn">Abmelden</a>
        </div>
    </div>

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

<main class="kontakt-container">

     
    <h1>Kontakt</h1>
    <p class="kontakt-info">  Für Fragen oder Anliegen stehen wir Ihnen jederzeit gerne zur Verfügung.</p>


    <?php if ($msg): ?>
        <p class="form-nachricht"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <form id="kontaktForm" method="post" action="kontakt.php">
        <label>Vorname</label><br>
        <input type="text" name="vorname" value="<?=htmlspecialchars($vorname)?>" required><br>

        <label>Nachname</label><br>
        <input type="text" name="nachname" value="<?=htmlspecialchars($nachname)?>" required><br>

        <label>E-Mail</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email)?>" required><br>

        <label>Telefon</label><br>
        <input type="tel" name="telefon" value="<?= htmlspecialchars($telefon)?>"><br>

        <label>Betreff</label><br>
        <input type="text" name="betreff" value="<?=htmlspecialchars($betreff)?>" required><br>

        <label>Nachricht / Problem</label><br>
        <textarea name="nachricht" rows="5" required><?= htmlspecialchars($nachricht)?></textarea><br><br>

        <button type="submit">Absenden</button><br>
    </form>
</main>

</body>
</html>