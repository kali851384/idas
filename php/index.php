 <?php
session_start();
require_once "../includes/db_config.php"; 

// wenn eingelogt
$vorname = "";
if (isset($_SESSION['patient_id'])) {
    $stmt = $conn->prepare("SELECT vorname FROM patient WHERE patient_id = ?");
    $stmt->bind_param("i", $_SESSION['patient_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $vorname = $row['vorname'];
    }
}

// Statistik aus DB holen
$anzahl_aerzte = 0;
if ($conn) {
    $erg_aerzte = mysqli_query($conn, "SELECT COUNT(*) AS anzahl FROM arzt");
    if ($erg_aerzte) {
        $row = mysqli_fetch_assoc($erg_aerzte);
        $anzahl_aerzte = $row['anzahl'];
    }

}
?>

<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>IDAS</title>
  <link rel="stylesheet" href="style.css" />


</head>
<body>

<?php include 'header.php'; ?>   <!-- kopf bereich rufen -->

<main>

  <!-- begrüßung-Bereich (diese teil wird gezeigt für die eingelogte patiente)-->
  <section id="begrüßung">
    <?php if (isset($_SESSION['patient_id'])): ?>
      <h1>Hallo <?= htmlspecialchars($vorname) ?>!</h1>
      <p>Deine Gesundheit liegt uns am Herzen - lass uns loslegen!</p>
    <?php else: ?>  <!-- und diese teil wird gezeigt für die nicht eingelogte patiente-->
      <h1>Willkommen bei IDAS</h1>
      <p>Schnell & einfach zum richtigen Arzt.</p>
    <?php endif; ?>
  </section>

  
  <!-- über uns-Bereich -->
  <section id="über-uns">
    <h2>über IDAS</h2>
    <h3>Wir machen <em>Medizin menschlich.</em></h3>
    <p>
      IDAS ist ein modernes Gesundheitsportal aus Hannover, das Patienten und Fachärzte auf einer Plattform zusammenbringt. Unser Ziel ist es, den Zugang zur medizinischen Versorgung so einfach und angenehm wie möglich zu gestalten.
    </p>
    <p>
      Von der Terminbuchung bis zur Diagnoseunterstützung - wir begleiten Sie bei jedem Schritt Ihrer Gesundheitsreise.
    </p>


   <!-- Vorteile-Bereich -->
<div class="vorteile-container">
  <div class="vorteil-box">
    <span class="vorteil-icon">📅</span>
    <strong>Online-Termine</strong><br>
    Termine buchen – wann und wo Sie möchten.
  </div>

  <div class="vorteil-box">
    <span class="vorteil-icon">🔒</span>
    <strong>Datenschutz</strong><br>
    Ihre Daten sind bei uns sicher.
  </div>

  <div class="vorteil-box">
    <span class="vorteil-icon">✉️</span>
    <strong>Direkter Kontakt</strong><br>
    Schreiben Sie uns jederzeit über unser Kontaktformular.
  </div>
</div>
  </section>

  <!-- hauptberech (Zwei große Buttons zu Termine und Symptome) -->
<section id="hauptbereiche">
  <div class="hauptbereiche-container">
    <a href="symptome.php" class="hauptbereich-karte">
      <span class="hauptbereich-icon">🩺</span>
      <h3><?php echo isset($_SESSION['patient_id']) ? 'Neue Symptome prüfen' : 'Symptome eingeben'; ?></h3>
      <p>Passenden Arzt finden</p>
    </a>

    <a href="termine.php" class="hauptbereich-karte">
      <span class="hauptbereich-icon">📆</span>
      <h3><?php echo isset($_SESSION['patient_id']) ? 'Meine Termine' : 'Termin buchen'; ?></h3>
      <p>Termine verwalten</p>
    </a>
  </div>
</section>



  <!-- Vorteile und statistick zum Ärzte zahl in DB -->
  <section id="stats">
    <div class="stats-container">

      <div class="stat-box">
        <div class="stat-zahl"><?php echo $anzahl_aerzte; ?>+</div>
        <div class="stat-text">Ärzte</div>
      </div>

    <div class="stat-box">
        <div class="stat-zahl">24/7</div>
        <div class="stat-text">Verfügbar</div>
      </div>

      <div class="stat-box">
        <div class="stat-zahl">100%</div>
        <div class="stat-text">Kostenlos</div>
      </div>

    </div>
  </section>



  <!-- für besucher -->
  <?php if (!isset($_SESSION['patient_id'])): ?>
    <section id="start-aufruf">
      <h2>Bereit loszulegen?</h2>
      <div class="start-buttons">
        <a href="anmeldung.php" class="start-login">Anmelden</a>
        <a href="registrierung.php" class="start-register">Registrieren</a>
      </div>
    </section>
  <?php endif; ?>

</main>



<!-- fuß berech -->
<footer id="footer">
   2026 IDAS Gesundheitsportal . Hannover<br>
  Alle Rechte vorbehalten
</footer>



<!-- JS teil um profil menü zu passen -->
 <script>
const profilImg = document.getElementById("profilImg");
const menu = document.getElementById("profilMenu");

// wenn man den prfilbild klickt
profilImg.addEventListener("click", function(e) {
  e.stopPropagation(); 
  menu.classList.toggle("show");
});

// wenn man außerhalb der Menü klickt
document.addEventListener("click", function(e) {
  if (!menu.contains(e.target)) {
    menu.classList.remove("show");  // menü schließen
  }
});
</script>

</body>
</html>