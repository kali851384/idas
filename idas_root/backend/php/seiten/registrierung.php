<?php
session_start();
require_once "../includes/db_config.php";
$fehler = "";
$erfolg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nname      = trim($_POST["createAccountNName"]       ?? "");
    $vname      = trim($_POST["createAccountVName"]       ?? "");
    $email      = trim($_POST["createAccountEmail"]       ?? "");
    $gender     = trim($_POST["createAccountGender"]      ?? "");
    $geburt     = trim($_POST["createAccountAge"]         ?? "");
    $plz        = trim($_POST["createAccountPlz"]         ?? "");
    $adresse    = trim($_POST["createAccountAdress"]      ?? "");
    $telefon    = trim($_POST["createAccountTelefon"]     ?? "");
    $passwort   = $_POST["createAccountPassword"]         ?? "";
    $passwort2  = $_POST["createAccountPasswordConfirm"]  ?? "";

    // --- Pflichtfelder prüfen ---
    if (empty($nname) || empty($vname) || empty($email) || empty($gender) ||
        empty($geburt) || empty($plz) || empty($adresse) || empty($passwort) || empty($passwort2)) {
        $fehler = "Bitte füllen Sie alle Felder aus!";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fehler = "Die eingegebene E-Mail-Adresse ist ungültig.";

    } elseif ($passwort !== $passwort2) {
        $fehler = "Die Passwörter stimmen nicht überein!";

    } elseif (strlen($passwort) < 8) {
        $fehler = "Das Passwort muss mindestens 8 Zeichen lang sein.";

    } else {
        // --- E-Mail bereits vergeben? ---
        $check = $conn->prepare("SELECT patient_id FROM patient WHERE email = ? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $fehler = "Diese E-Mail-Adresse ist bereits registriert.";
        } else {
            // --- Passwort hashen ---
            $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);

            // --- In Datenbank speichern ---
            $stmt = $conn->prepare(
                "INSERT INTO patient (vorname, nachname, email, geschlecht, geburtsdatum, plz, adresse, telefon, passwort)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param(
                "sssssssss",
                $vname, $nname, $email, $gender,
                $geburt, $plz, $adresse, $telefon, $passwort_hash
            );

            if ($stmt->execute()) {
                // Direkt einloggen nach Registrierung
                $_SESSION['patient_id'] = $conn->insert_id;
                header("Location: index.php");
                exit;
            } else {
                $fehler = "Fehler beim Speichern. Bitte versuchen Sie es erneut.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrierung – IDAS</title>
    <link rel="stylesheet" href="../../../forend/css/style.css" />
    <style>
        .reg-wrapper {
            max-width: 650px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 45px 50px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .reg-wrapper h1 {
            color: #2F9E44;
            font-size: 2rem;
            margin-bottom: 8px;
            text-align: center;
        }

        .reg-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 35px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2F9E44;
            margin-bottom: 7px;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #E9ECEF;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            background: #fff;
            color: #212529;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2F9E44;
            box-shadow: 0 0 0 3px rgba(47, 158, 68, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            padding: 12px 15px;
            border: 2px solid #E9ECEF;
            border-radius: 12px;
        }

        .radio-group label {
            display: flex;
            align-items: center;
            gap: 7px;
            font-weight: 500;
            color: #212529;
            cursor: pointer;
            margin: 0;
        }

        .radio-group input[type="radio"] {
            width: auto;
            padding: 0;
            border: none;
            accent-color: #2F9E44;
        }

        .reg-btn {
            width: 100%;
            background: #2F9E44;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 10px;
        }

        .reg-btn:hover {
            background: #237A35;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(47,158,68,0.3);
        }

        .fehler-box {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 25px;
            font-size: 0.95rem;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .login-link a {
            color: #2F9E44;
            font-weight: 600;
        }

        .section-title {
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            margin: 28px 0 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E9ECEF;
        }

        @media (max-width: 600px) {
            .reg-wrapper { padding: 30px 20px; }
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="reg-wrapper">
        <h1>Konto erstellen</h1>
        <p class="reg-subtitle">Kostenlos registrieren und Arzttermine buchen</p>

        <?php if ($fehler): ?>
            <div class="fehler-box">⚠️ <?= htmlspecialchars($fehler) ?></div>
        <?php endif; ?>

        <form method="post" action="registrierung.php">

            <div class="section-title">Persönliche Daten</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="createAccountVName">Vorname *</label>
                    <input type="text" name="createAccountVName" id="createAccountVName"
                           value="<?= htmlspecialchars($_POST['createAccountVName'] ?? '') ?>" required maxlength="30">
                </div>
                <div class="form-group">
                    <label for="createAccountNName">Nachname *</label>
                    <input type="text" name="createAccountNName" id="createAccountNName"
                           value="<?= htmlspecialchars($_POST['createAccountNName'] ?? '') ?>" required maxlength="30">
                </div>
            </div>

            <div class="form-group">
                <label>Geschlecht *</label>
                <div class="radio-group">
                    <label>
                        <input type="radio" name="createAccountGender" value="m"
                            <?= (($_POST['createAccountGender'] ?? '') === 'm') ? 'checked' : '' ?> required>
                        Männlich
                    </label>
                    <label>
                        <input type="radio" name="createAccountGender" value="w"
                            <?= (($_POST['createAccountGender'] ?? '') === 'w') ? 'checked' : '' ?>>
                        Weiblich
                    </label>
                    <label>
                        <input type="radio" name="createAccountGender" value="d"
                            <?= (($_POST['createAccountGender'] ?? '') === 'd') ? 'checked' : '' ?>>
                        Divers
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="createAccountAge">Geburtsdatum *</label>
                    <input type="date" name="createAccountAge" id="createAccountAge"
                           value="<?= htmlspecialchars($_POST['createAccountAge'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="createAccountTelefon">Telefon</label>
                    <input type="tel" name="createAccountTelefon" id="createAccountTelefon"
                           value="<?= htmlspecialchars($_POST['createAccountTelefon'] ?? '') ?>" placeholder="+49 ...">
                </div>
            </div>

            <div class="section-title">Adresse</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="createAccountPlz">PLZ *</label>
                    <input type="text" name="createAccountPlz" id="createAccountPlz"
                           value="<?= htmlspecialchars($_POST['createAccountPlz'] ?? '') ?>" required maxlength="10">
                </div>
                <div class="form-group">
                    <label for="createAccountAdress">Straße &amp; Hausnummer *</label>
                    <input type="text" name="createAccountAdress" id="createAccountAdress"
                           value="<?= htmlspecialchars($_POST['createAccountAdress'] ?? '') ?>" required maxlength="70">
                </div>
            </div>

            <div class="section-title">Zugangsdaten</div>
            <div class="form-group">
                <label for="createAccountEmail">E-Mail-Adresse *</label>
                <input type="email" name="createAccountEmail" id="createAccountEmail"
                       value="<?= htmlspecialchars($_POST['createAccountEmail'] ?? '') ?>" required maxlength="70">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="createAccountPassword">Passwort * (min. 8 Zeichen)</label>
                    <input type="password" name="createAccountPassword" id="createAccountPassword" required>
                </div>
                <div class="form-group">
                    <label for="createAccountPasswordConfirm">Passwort bestätigen *</label>
                    <input type="password" name="createAccountPasswordConfirm" id="createAccountPasswordConfirm" required>
                </div>
            </div>

            <button type="submit" class="reg-btn">Jetzt registrieren</button>
        </form>

        <p class="login-link">
            Bereits ein Konto? <a href="anmeldung.php">Hier anmelden</a>
        </p>
    </div>
</main>

<footer id="footer">
    2026 IDAS Gesundheitsportal · Hannover<br>
    Alle Rechte vorbehalten
</footer>

<script src="../../JS/script.js" defer></script> <!-- JS teil um profil menü zu passen -->
</body>
</html>
