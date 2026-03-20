<?php
if (session_status() === PHP_SESSION_NONE) {    // sichern das session funktioniert
    session_start();
}


?>

<!-- logo -->
<a href="index.php">
  <img id="siteLogo" src="LOGO/logo2.png" alt="IDAS Logo" width="72px" height="72px">    <!-- logog -->
</a>
  <h1><b>IDAS</b></h1>
  <h4>Intelligent Doctor Appointment System</h4>


<!-- Profil -->
<div id="navProfil">
<div id="profilImg">👤</div>

  <div id="profilMenu" class="dropdown">
    <?php if (isset($_SESSION['patient_id'])): ?>  <!-- wenn eingelogt dann hat er diese 2 wahlen  -->
      <a href="konto.php">Konto verwalten</a>
      <a href="logout.php">Abmelden</a>
    <?php else: ?>                  <!-- wenn nicht dann das -->
      <a href="anmeldung.php">Anmelden</a>  
    <?php endif; ?>
  </div>
</div>

<!-- Navigation -->
  <nav id="naviBereich">
    <div id="navLinks">
      <a href="index.php">Start</a>
      <a href="symptome.php">Arzt finden</a>
      <a href="termine.php">Termine</a>
      <a href="konto.php">Konto</a>
      <a href="kontakt.php">Kontakt</a>
    </div>
  </nav>
</div>



