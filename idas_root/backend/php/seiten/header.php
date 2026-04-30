<?php
if (session_status() === PHP_SESSION_NONE) {    // sichern das session funktioniert
    session_start();
}


?>


<div id="header">
    <!-- Logo  -->
    <div class="header-left">
        <a href="index.php">
            <img id="siteLogo" src="../LOGO/logo2.png" alt="IDAS Logo" width="72px" height="72px">
        </a>
        <h1><b>IDAS</b></h1>
        <h4>Intelligent Doctor Appointment System</h4>
    </div>
    
    <!--Profil -->
    <div class="header-right">
        <div id="navProfil">
            <div id="profilImg">👤</div>
            <div id="profilMenu" class="dropdown">
                <?php if (isset($_SESSION['patient_id'])): ?>
                    <a href="konto.php">Konto verwalten</a>
                    <a href="logout.php">Abmelden</a>
                <?php else: ?>
                    <a href="anmeldung.php">Anmelden</a>
                    <a href="registrierung.php">Registrieren</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Navigation r -->
<nav id="naviBereich">
    <div id="navLinks">
        <a href="index.php" class="navLink"> Start</a>
        <a href="symptome.php" class="navLink"> Arzt finden</a>
        <a href="termine.php" class="navLink"> Termine</a>
        <a href="konto.php" class="navLink"> Konto</a>
        <a href="kontakt.php" class="navLink"> Kontakt</a>
    </div>
</nav>



