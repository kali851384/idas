<?php
// C:\xampp\htdocs\IDAS\backend\api\register.php
// POST: vorname, nachname, email, passwort, geburtsdatum, geschlecht
// Returns: { success, message }

require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(["success" => false, "message" => "Nur POST erlaubt."]);
}

$vorname     = trim($_POST["vorname"]     ?? "");
$nachname    = trim($_POST["nachname"]    ?? "");
$email       = trim($_POST["email"]       ?? "");
$passwort    = $_POST["passwort"]         ?? "";
$geburtsdatum = trim($_POST["geburtsdatum"] ?? "");
$geschlecht  = trim($_POST["geschlecht"]  ?? "m");

// Validation
if ($vorname === "" || $nachname === "" || $email === "" || $passwort === "" || $geburtsdatum === "") {
    respond(["success" => false, "message" => "Alle Pflichtfelder ausfüllen."]);
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    respond(["success" => false, "message" => "Ungültige E-Mail-Adresse."]);
}
if (strlen($passwort) < 6) {
    respond(["success" => false, "message" => "Passwort min. 6 Zeichen."]);
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $geburtsdatum)) {
    respond(["success" => false, "message" => "Geburtsdatum Format: YYYY-MM-DD"]);
}

// Check if email already exists
$stmt = $pdo->prepare("SELECT patient_id FROM patient WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    respond(["success" => false, "message" => "Diese E-Mail ist bereits registriert."]);
}

// Hash password and insert
$hash = password_hash($passwort, PASSWORD_BCRYPT);
$stmt = $pdo->prepare(
    "INSERT INTO patient (vorname, nachname, email, passwort, geburtsdatum, geschlecht)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->execute([$vorname, $nachname, $email, $hash, $geburtsdatum, $geschlecht]);

respond(["success" => true, "message" => "Registrierung erfolgreich."]);
