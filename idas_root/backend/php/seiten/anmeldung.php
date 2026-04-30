<?php
session_start();
require_once "../includes/db_config.php";

// Bereits eingeloggt → weiterleiten
if (isset($_SESSION['patient_id'])) {
    header("Location: index.php");
    exit;
}

$fehler = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email']    ?? '');
    $passwort = $_POST['passwort']      ?? '';

    if (empty($email) || empty($passwort)) {
        $fehler = "Bitte E-Mail und Passwort eingeben.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fehler = "Bitte eine gültige E-Mail-Adresse eingeben.";

    } else {
        $stmt = $conn->prepare(
            "SELECT patient_id, vorname, passwort FROM patient WHERE email = ? LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && password_verify($passwort, $user['passwort'])) {
            $_SESSION['patient_id'] = $user['patient_id'];
            $_SESSION['vorname']    = $user['vorname'];
            header("Location: index.php");
            exit;
        } else {
            $fehler = "E-Mail-Adresse oder Passwort ist falsch.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anmelden – IDAS</title>
    <link rel="stylesheet" href="../../../forend/css/style.css" />
    <style>
        .login-wrapper {
            max-width: 480px;
            margin: 60px auto;
            background: #ffffff;
            border-radius: 24px;
            padding: 50px 50px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .login-wrapper h1 {
            color: #2F9E44;
            font-size: 2rem;
            margin-bottom: 8px;
            text-align: center;
        }

        .login-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 35px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 22px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #2F9E44;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .form-group input {
            width: 100%;
            padding: 13px 15px;
            border: 2px solid #E9ECEF;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.2s ease;
            color: #212529;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2F9E44;
            box-shadow: 0 0 0 3px rgba(47, 158, 68, 0.1);
        }

        .login-btn {
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
            margin-top: 5px;
        }

        .login-btn:hover {
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

        .reg-link {
            text-align: center;
            margin-top: 25px;
            color: #6c757d;
            font-size: 0.95rem;
        }

        .reg-link a {
            color: #2F9E44;
            font-weight: 600;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 25px 0;
            color: #adb5bd;
            font-size: 0.9rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E9ECEF;
        }

        @media (max-width: 550px) {
            .login-wrapper { margin: 20px; padding: 30px 25px; }
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <div class="login-wrapper">
        <h1>Willkommen zurück</h1>
        <p class="login-subtitle">Melden Sie sich mit Ihrem IDAS-Konto an</p>

        <?php if ($fehler): ?>
            <div class="fehler-box">⚠️ <?= htmlspecialchars($fehler) ?></div>
        <?php endif; ?>

        <form method="post" action="anmeldung.php">

            <div class="form-group">
                <label for="email">E-Mail-Adresse</label>
                <input type="email" name="email" id="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                       placeholder="ihre@email.de" required autofocus>
            </div>

            <div class="form-group">
                <label for="passwort">Passwort</label>
                <input type="password" name="passwort" id="passwort"
                       placeholder="Ihr Passwort" required>
            </div>

            <button type="submit" class="login-btn">Anmelden</button>
        </form>

        <div class="divider">oder</div>

        <p class="reg-link">
            Noch kein Konto? <a href="registrierung.php">Jetzt kostenlos registrieren</a>
        </p>
        <p class="reg-link">
            <a href="../cms/admin_dashboard.php">Admin Bereich</a>
        </p>
    </div>
</main>

<footer id="footer">
    2026 IDAS Gesundheitsportal · Hannover<br>
    Alle Rechte vorbehalten
</footer>

<script src="../JS/script.js" defer></script>
</body>
</html>
