<?php
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";
$DB_NAME = "idas";

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    die("DB Fehler: " . mysqli_connect_error());
}

#mysqli_set_charset($conn, "utf8mb4");

#mysqli_set_charset($conn, "utf8mb4");
// ─── SMTP Einstellungen ────────────────────────────────────────────────────
define('MAIL_HOST',      'smtp.gmail.com');
define('MAIL_PORT',      587);
define('MAIL_USER',      'mohammadmirzayan20@gmail.com');
define('MAIL_PASS',      'dfmr teeh rpry ncjo');  // 16-stelliges Gmail App-Passwort
define('MAIL_FROM_NAME', 'idas');

// ─── Mailer-Funktion ───────────────────────────────────────────────────────
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendMail(string $to, string $subject, string $htmlBody): bool {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USER;
        $mail->Password   = MAIL_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = MAIL_PORT;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(MAIL_USER, MAIL_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags($htmlBody);

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mail-Fehler: " . $e->getMessage());
        return false;
    }
}
