<?php
session_start();
require_once "../includes/db_config.php";

// Nur für eingeloggte Patienten
if (!isset($_SESSION['patient_id'])) {
    header("Location: anmeldung.php");
    exit;
}

$patient_id = $_SESSION['patient_id'];

// Patientendaten laden
$stmt = $conn->prepare("SELECT * FROM patient WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$patient = $stmt->get_result()->fetch_assoc();

// Nächste 3 Termine laden
$stmt2 = $conn->prepare("
    SELECT t.datum, t.beschreibung, a.name AS arzt_name, f.name AS fachbereich
    FROM termin t
    JOIN arzt a ON t.arzt_id = a.arzt_id
    JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
    WHERE t.patient_id = ? AND t.datum >= NOW()
    ORDER BY t.datum ASC
    LIMIT 3
");
$stmt2->bind_param("i", $patient_id);
$stmt2->execute();
$termine = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

// Passwort ändern
$pw_fehler  = "";
$pw_erfolg  = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['aktion']) && $_POST['aktion'] === 'passwort') {
    $alt  = $_POST['passwort_alt']   ?? '';
    $neu  = $_POST['passwort_neu']   ?? '';
    $neu2 = $_POST['passwort_neu2']  ?? '';

    if (empty($alt) || empty($neu) || empty($neu2)) {
        $pw_fehler = "Bitte alle Passwortfelder ausfüllen.";
    } elseif ($neu !== $neu2) {
        $pw_fehler = "Die neuen Passwörter stimmen nicht überein.";
    } elseif (strlen($neu) < 8) {
        $pw_fehler = "Das neue Passwort muss mindestens 8 Zeichen lang sein.";
    } elseif (!password_verify($alt, $patient['passwort'])) {
        $pw_fehler = "Das aktuelle Passwort ist falsch.";
    } else {
        $hash = password_hash($neu, PASSWORD_DEFAULT);
        $upd  = $conn->prepare("UPDATE patient SET passwort = ? WHERE patient_id = ?");
        $upd->bind_param("si", $hash, $patient_id);
        $upd->execute();
        $pw_erfolg = "Passwort wurde erfolgreich geändert.";
    }
}

// Geschlecht anzeigen
$geschlecht_text = match($patient['geschlecht'] ?? '') {
    'm' => 'Männlich',
    'w' => 'Weiblich',
    'd' => 'Divers',
    default => '—'
};
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mein Konto – IDAS</title>
    <link rel="stylesheet" href="../../../forend/css/style.css" />
    <style>
        .konto-page {
            max-width: 1100px;
            margin: 0 auto;
        }

        .konto-page h1 {
            color: #2F9E44;
            font-size: 2rem;
            margin-bottom: 30px;
        }

        .konto-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        .konto-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #E9ECEF;
        }

        .konto-card h2 {
            color: #2F9E44;
            font-size: 1.2rem;
            margin-bottom: 22px;
            padding-bottom: 12px;
            border-bottom: 2px solid #F1F8F4;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #F1F8F4;
            font-size: 0.95rem;
        }

        .info-row:last-child { border-bottom: none; }

        .info-label {
            color: #6c757d;
            font-weight: 500;
            min-width: 130px;
        }

        .info-value {
            color: #212529;
            font-weight: 600;
            text-align: right;
        }

        .avatar-box {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #F1F8F4;
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
        }

        .avatar-kreis {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #2F9E44;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        .avatar-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #212529;
        }

        .avatar-email {
            font-size: 0.9rem;
            color: #6c757d;
            margin-top: 3px;
        }

        /* Termine Karte */
        .termin-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            padding: 12px 0;
            border-bottom: 1px solid #F1F8F4;
        }

        .termin-item:last-child { border-bottom: none; }

        .termin-datum-box {
            background: #2F9E44;
            color: white;
            border-radius: 10px;
            padding: 8px 12px;
            text-align: center;
            min-width: 58px;
            flex-shrink: 0;
        }

        .termin-tag {
            font-size: 1.4rem;
            font-weight: 700;
            line-height: 1;
        }

        .termin-monat {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
        }

        .termin-info-arzt {
            font-weight: 600;
            color: #212529;
            font-size: 0.95rem;
        }

        .termin-info-fach {
            color: #6c757d;
            font-size: 0.85rem;
            margin-top: 3px;
        }

        .termin-info-zeit {
            color: #2F9E44;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 2px;
        }

        .keine-termine {
            text-align: center;
            color: #6c757d;
            padding: 25px 0;
            font-size: 0.95rem;
        }

        .alle-termine-btn {
            display: block;
            text-align: center;
            margin-top: 18px;
            background: #F1F8F4;
            color: #2F9E44;
            padding: 10px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .alle-termine-btn:hover {
            background: #2F9E44;
            color: white;
        }

        /* Passwort Formular */
        .pw-form .form-group {
            margin-bottom: 16px;
        }

        .pw-form label {
            display: block;
            font-weight: 600;
            color: #2F9E44;
            margin-bottom: 6px;
            font-size: 0.9rem;
        }

        .pw-form input {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #E9ECEF;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .pw-form input:focus {
            outline: none;
            border-color: #2F9E44;
            box-shadow: 0 0 0 3px rgba(47,158,68,0.1);
        }

        .pw-btn {
            width: 100%;
            background: #2F9E44;
            color: white;
            border: none;
            padding: 12px;
            border-radius: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 5px;
            transition: all 0.2s;
        }

        .pw-btn:hover {
            background: #237A35;
            transform: translateY(-2px);
        }

        .meldung-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }

        .meldung-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 10px;
            padding: 12px 15px;
            margin-bottom: 16px;
            font-size: 0.9rem;
        }

        .abmelden-btn {
            display: block;
            width: 100%;
            margin-top: 18px;
            text-align: center;
            background: #dc3545;
            color: white;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.95rem;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .abmelden-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            color: white;
        }

        @media (max-width: 768px) {
            .konto-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="konto-page">

        <h1>Mein Konto</h1>

        <!-- Avatar + Name oben -->
        <div class="avatar-box">
            <div class="avatar-kreis">
                <?= strtoupper(substr($patient['vorname'], 0, 1) . substr($patient['nachname'], 0, 1)) ?>
            </div>
            <div>
                <div class="avatar-name">
                    <?= htmlspecialchars($patient['vorname'] . ' ' . $patient['nachname']) ?>
                </div>
                <div class="avatar-email">
                    <?= htmlspecialchars($patient['email']) ?>
                </div>
            </div>
        </div>

        <div class="konto-grid">

            <!-- Karte 1: Persönliche Daten -->
            <div class="konto-card">
                <h2>👤 Persönliche Daten</h2>

                <div class="info-row">
                    <span class="info-label">Vorname</span>
                    <span class="info-value"><?= htmlspecialchars($patient['vorname']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Nachname</span>
                    <span class="info-value"><?= htmlspecialchars($patient['nachname']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Geschlecht</span>
                    <span class="info-value"><?= $geschlecht_text ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Geburtsdatum</span>
                    <span class="info-value">
                        <?= $patient['geburtsdatum'] ? date('d.m.Y', strtotime($patient['geburtsdatum'])) : '—' ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">E-Mail</span>
                    <span class="info-value"><?= htmlspecialchars($patient['email']) ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Telefon</span>
                    <span class="info-value"><?= htmlspecialchars($patient['telefon'] ?: '—') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">PLZ</span>
                    <span class="info-value"><?= htmlspecialchars($patient['plz'] ?: '—') ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Adresse</span>
                    <span class="info-value"><?= htmlspecialchars($patient['adresse'] ?: '—') ?></span>
                </div>
            </div>

            <!-- Karte 2: Kommende Termine -->
            <div class="konto-card">
                <h2>📅 Nächste Termine</h2>

                <?php if (empty($termine)): ?>
                    <div class="keine-termine">
                        <p>📭 Keine bevorstehenden Termine.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($termine as $t): ?>
                        <div class="termin-item">
                            <div class="termin-datum-box">
                                <div class="termin-tag"><?= date('d', strtotime($t['datum'])) ?></div>
                                <div class="termin-monat"><?= date('M', strtotime($t['datum'])) ?></div>
                            </div>
                            <div>
                                <div class="termin-info-arzt">
                                    <?= htmlspecialchars($t['arzt_name']) ?>
                                </div>
                                <div class="termin-info-fach">
                                    <?= htmlspecialchars($t['fachbereich']) ?>
                                </div>
                                <div class="termin-info-zeit">
                                    <?= date('H:i', strtotime($t['datum'])) ?> Uhr
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <a href="termine.php" class="alle-termine-btn">Alle Termine ansehen →</a>
            </div>

            <!-- Karte 3: Passwort ändern -->
            <div class="konto-card">
                <h2>🔒 Passwort ändern</h2>

                <?php if ($pw_erfolg): ?>
                    <div class="meldung-success">✅ <?= htmlspecialchars($pw_erfolg) ?></div>
                <?php endif; ?>
                <?php if ($pw_fehler): ?>
                    <div class="meldung-error">⚠️ <?= htmlspecialchars($pw_fehler) ?></div>
                <?php endif; ?>

                <form method="post" class="pw-form">
                    <input type="hidden" name="aktion" value="passwort">

                    <div class="form-group">
                        <label>Aktuelles Passwort</label>
                        <input type="password" name="passwort_alt" required>
                    </div>
                    <div class="form-group">
                        <label>Neues Passwort (min. 8 Zeichen)</label>
                        <input type="password" name="passwort_neu" required>
                    </div>
                    <div class="form-group">
                        <label>Neues Passwort bestätigen</label>
                        <input type="password" name="passwort_neu2" required>
                    </div>
                    <button type="submit" class="pw-btn">Passwort ändern</button>
                </form>
            </div>

            <!-- Karte 4: Schnellzugriff -->
            <div class="konto-card">
                <h2>⚡ Schnellzugriff</h2>

                <div class="info-row">
                    <span class="info-label">Arzt finden</span>
                    <a href="symptome.php" class="info-value" style="color:#2F9E44">Symptome eingeben →</a>
                </div>
                <div class="info-row">
                    <span class="info-label">Meine Termine</span>
                    <a href="termine.php" class="info-value" style="color:#2F9E44">Übersicht →</a>
                </div>
                <div class="info-row">
                    <span class="info-label">Kontakt</span>
                    <a href="Kontakt.php" class="info-value" style="color:#2F9E44">Nachricht senden →</a>
                </div>

                <a href="logout.php" class="abmelden-btn" onclick="return confirm('Möchten Sie sich wirklich abmelden?')">
                    Abmelden
                </a>
            </div>

        </div><!-- /konto-grid -->
    </div>
</main>

<footer id="footer">
    2026 IDAS Gesundheitsportal · Hannover<br>
    Alle Rechte vorbehalten
</footer>

<script src="../../JS/script.js" defer></script>
</body>
</html>
