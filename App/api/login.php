<?php
// C:\xampp\htdocs\IDAS\backend\api\login.php
// POST: email, passwort
// Returns: { success, token, patient_id, vorname, message }

require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(["success" => false, "message" => "Nur POST erlaubt."]);
}

$email   = trim($_POST["email"]   ?? "");
$passwort = $_POST["passwort"] ?? "";

if ($email === "" || $passwort === "") {
    respond(["success" => false, "message" => "E-Mail und Passwort erforderlich."]);
}

// Fetch patient by email
$stmt = $pdo->prepare(
    "SELECT patient_id, vorname, nachname, passwort FROM patient WHERE email = ?"
);
$stmt->execute([$email]);
$patient = $stmt->fetch();

if (!$patient) {
    respond(["success" => false, "message" => "E-Mail oder Passwort falsch."]);
}

// Support both plain text (dev/test) and bcrypt passwords
$passwordOk = ($patient["passwort"] === $passwort)          // plain (patient #1 dev account)
           || password_verify($passwort, $patient["passwort"]); // bcrypt

if (!$passwordOk) {
    respond(["success" => false, "message" => "E-Mail oder Passwort falsch."]);
}


// Delete old sessions for this patient
$del = $pdo->prepare("DELETE FROM app_sessions WHERE patient_id = ?");
$del->execute([$patient["patient_id"]]);

// Generate token and store session
$token = bin2hex(random_bytes(32));
$stmt  = $pdo->prepare(
    "INSERT INTO app_sessions (token, patient_id) VALUES (?, ?)"
);
$stmt->execute([$token, $patient["patient_id"]]);

respond([
    "success"    => true,
    "token"      => $token,
    "patient_id" => (int) $patient["patient_id"],
    "vorname"    => $patient["vorname"],
]);
