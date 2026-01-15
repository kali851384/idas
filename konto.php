<!DOCTYPE html>
<html lang="de">
  <head>
  
    <link href="style.css" type="text/css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta charset="UTF-8" />
    <script src="script.js"></script>
  </head>

  <body>
    <div id="header">
      <a href="index.html"
        ><img
          id="siteLogo"
          src="images/siteLogo.png"
          width="72px"
          height="72px"
      /></a>
      <h1><b>IDAS</b></h1>
      <h4 id="logoBanner">Intelligent Doctor Appointment System</h4>
    </div>

    <main>
      <div id="konto" class="konto">
        <h1>Willkommen, Frau/Herr ...</h1>

        <nav id="naviBereich">
          <div id="navLinks">
            <a href="index.html" id="index" class="navLink">Start</a>
            <a href="symptome.html" id="symptome" class="navLink"
              >Arzt finden</a
            >
            <a href="patientenakte.html" id="akte" class="navLink"
              >Patienakte</a
            >
            <a href="termine.html" id="termine" class="navLink">Termine</a>
            <a href="befunde">Befunde</a>
            <a href="rezepte">Rezepte</a>
            <a href="nachrichten">Nachrichten</a>
            <a href="konto.php" id="konto" class="navLink"
              >Konto-Einstellungen</a
            >
          </div>
        </nav>

        <section id="termine">
          <h2>Kommende Termine</h2>
          <ul>
            <li>12.11.2025 - 14:30 Uhr</li>
            <li>03.12.2025 - 10:00 Uhr Blutabnahme</li>
          </ul>
          <button>Neuen Termin buchen</button>
        </section>

        <section id="befunde">
          <h2>Befunde & Dokumente</h2>
          <ul>
            <li>Blutbild - 10.10.2025 <a href="#">Download</a></li>
            <li>Röntgen - 02.09.2025 <a href="#">Download</a></li>
          </ul>
        </section>

        <section id="rezepte">
          <h2>Rezepte</h2>
          <ul>
            <li>Ibuprofen - ausgestellt am 15.10.2025</li>
          </ul>
          <button>Folgerezept anfordern</button>
        </section>

        <section id="nachrichten">
          <h2>Nachrichten</h2>
          <p>Sie haben keine neuen Nachrichten.</p>
          <button>Neue Nachricht schreiben</button>
        </section>

        <section id="einstellungen">
          <h2>Konto-Einstellungen</h2>
          <p><b>E-Mail:</b> ....@example.com</p>
          <button>Passwort ändern</button>
          <button>Abmelden</button>
        </section>
      </div>
    </main>

    <footer></footer>
  </body>
</html>
