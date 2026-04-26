<?php
// C:\xampp\htdocs\IDAS\backend\api\profil.php
//
// GET  ?token=...
//   Returns: { success, data: { patient_id, vorname, nachname, email,
//                               telefon, wohnort, plz, adresse,
//                               geburtsdatum, geschlecht } }
//
// POST token, vorname, nachname, email, telefon, wohnort, plz,
//      adresse, geburtsdatum, geschlecht
//   Returns: { success, message }

require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$method = $_SERVER["REQUEST_METHOD"];

// ── GET: fetch profile ─────────────────────────────────────────────────────
if ($method === "GET") {
    $token      = trim($_GET["token"] ?? "");
    if ($token === "") respond(["success" => false, "message" => "Token fehlt."]);

    $patientId  = requireToken($pdo, $token);

    $stmt = $pdo->prepare(
        "SELECT patient_id, vorname, nachname, email, telefon,
                wohnort, plz, adresse, geburtsdatum, geschlecht
         FROM patient WHERE patient_id = ?"
    );
    $stmt->execute([$patientId]);
    $row = $stmt->fetch();

    if (!$row) respond(["success" => false, "message" => "Patient nicht gefunden."]);

    respond(["success" => true, "data" => $row]);
}

// ── POST: update profile ───────────────────────────────────────────────────
if ($method === "POST") {
    $token      = trim($_POST["token"] ?? "");
    if ($token === "") respond(["success" => false, "message" => "Token fehlt."]);

    $patientId  = requireToken($pdo, $token);

    $vorname     = trim($_POST["vorname"]      ?? "");
    $nachname    = trim($_POST["nachname"]     ?? "");
    $email       = trim($_POST["email"]        ?? "");
    $telefon     = trim($_POST["telefon"]      ?? "");
    $wohnort     = trim($_POST["wohnort"]      ?? "");
    $plz         = trim($_POST["plz"]          ?? "");
    $adresse     = trim($_POST["adresse"]      ?? "");
    $geburtsdatum = trim($_POST["geburtsdatum"] ?? "");
    $geschlecht  = trim($_POST["geschlecht"]   ?? "");

    if ($vorname === "" || $nachname === "" || $email === "") {
        respond(["success" => false, "message" => "Pflichtfelder fehlen."]);
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        respond(["success" => false, "message" => "Ungültige E-Mail-Adresse."]);
    }

    // Check email uniqueness (allow own email)
    $stmt = $pdo->prepare(
        "SELECT patient_id FROM patient WHERE email = ? AND patient_id != ?"
    );
    $stmt->execute([$email, $patientId]);
    if ($stmt->fetch()) {
        respond(["success" => false, "message" => "Diese E-Mail wird bereits verwendet."]);
    }

    $stmt = $pdo->prepare(
        "UPDATE patient SET vorname=?, nachname=?, email=?, telefon=?,
         wohnort=?, plz=?, adresse=?, geburtsdatum=?, geschlecht=?
         WHERE patient_id=?"
    );
    $stmt->execute([
        $vorname, $nachname, $email, $telefon,
        $wohnort, $plz, $adresse, $geburtsdatum, $geschlecht,
        $patientId
    ]);

    respond(["success" => true, "message" => "Profil erfolgreich aktualisiert."]);
}

respond(["success" => false, "message" => "Methode nicht erlaubt."]);
