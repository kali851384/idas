<?php
session_start();
require_once "../includes/db_config.php";

if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}                                            

$msg="";
$type="";
$vorname=$nachname=$email=$telefon=$betreff=$nachricht="";

// datens?tze aus formular lesen
if($_SERVER["REQUEST_METHOD"]=="POST") {
    $vorname=trim($_POST['vorname']?? '');          
    $nachname=trim($_POST['nachname']?? '');       //trim f?r leerzeilen am anfang/ende l?schen
    $email=trim($_POST['email']?? '');
    $telefon=trim($_POST['telefon']?? '');
    $betreff=trim($_POST['betreff']?? '');
    $nachricht=trim($_POST['nachricht']?? '');

// pr?fen ob alle felder gefullt
    if(empty($vorname) || empty($nachname) || empty($email) || empty($betreff) || empty($nachricht)) {              
        $msg= "Bitte alle Pflichtfelder ausf?llen!";   
        $type="error"; }

    
    // E-Mail pr?fen 
    elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {   //filter_var ist fertige funktion um email zu ?berpr?fen und FILTER_VALIDATE_EMAIL sichert das es richtig aussieht 
        $msg = "Ung?ltige E-Mail-Adresse.";
        $type="error";
    }
   
   // wenn alles in formular stimmt dann 
    else{
        $stmt = $conn->prepare("INSERT INTO kontakt_nachrichten (vorname,nachname,email,telefon,betreff,nachricht)VALUES (?,?,?,?,?,?)");       
        $stmt->bind_param("ssssss", $vorname,$nachname,$email,$telefon,$betreff,$nachricht);     
                                                                    
        if ($stmt->execute()) {
            $msg = "Nachricht wurde erfolgreich gesendet!"; 
            $type="success";    
            $vorname=$nachname=$email=$telefon=$betreff=$nachricht="";    //wenn alles klappt und $msg gezeigt ist dann wird alles wieder leer 
        } else {                                     
            $msg = "Fehler beim Speichern: " . $stmt->error;   // wenn fehler dann wird das fehler von mySQl kkopiert und gezeigt
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
 <link rel="stylesheet" href="../css/style.css" />
  <script src="script.js" defer></script>
</head>
<body>

 <?php include 'header.php'; ?> <!-- kopf bereich rufen -->

<main class="kontakt-container">

     
    <h1>Kontakt</h1>
    <p class="kontakt-info">  Für Fragen oder Anliegen stehen wir Ihnen jederzeit gerne zur Verfügung.</p>


            <!-- wenn msg nicht leer ist dann nachrichtzeigen ansonsten nix -->
            <?php if ($msg): ?>
                    <div class="form-message <?= $type ?>">
                        <?= htmlspecialchars($msg) ?>
                    </div>
                <?php endif; ?>
                        

    <form id="kontaktForm" method="post" action="kontakt.php">
        <label>Vorname</label><br>
        <input type="text" name="vorname" value="<?=htmlspecialchars($vorname)?>" required><br>

        <label>Nachname</label><br>
        <input type="text" name="nachname" value="<?=htmlspecialchars($nachname)?>" required><br>

        <label>E-Mail</label><br>
        <input type="email" name="email" value="<?= htmlspecialchars($email)?>" required><br>

        <label>Telefon</label><br>
        <input type="tel" name="telefon" value="<?= htmlspecialchars($telefon)?>" required><br>

        <label>Betreff</label><br>
        <input type="text" name="betreff" value="<?=htmlspecialchars($betreff)?>" required><br>

        <label>Nachricht / Problem</label><br>
        <textarea name="nachricht" rows="5" required><?= htmlspecialchars($nachricht)?></textarea><br><br>

        <button type="submit">Absenden</button><br>
    </form>
</main>

<footer id="footer">
   2026 IDAS Gesundheitsportal . Hannover<br>
  Alle Rechte vorbehalten
</footer>



<script src="../JS/script.js" defer></script>  <!-- JS teil um profil menü zu passen -->


<!-- nach den nachricht gesendet wird, wird die nach 5 sekunden verschwenden -->
 <script>
setTimeout(() => {
  const msg = document.querySelector('.form-message');
  if (msg) {
    msg.style.transition = "5.0s";
    msg.style.opacity = "0";
  }
}, 3000);
</script>

</body>
</html>
