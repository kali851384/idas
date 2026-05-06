<?php
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(["success" => false, "message" => "Nur POST erlaubt."]);
}

$token        = trim($_POST["token"]        ?? "");
$arztId       = (int)($_POST["arzt_id"]     ?? 0);
$datum        = trim($_POST["datum"]        ?? "");
$beschreibung = trim($_POST["beschreibung"] ?? "");
$symptomIds   = trim($_POST["symptom_ids"]  ?? ""); // NEW: comma separated

if ($token === "") respond(["success" => false, "message" => "Token fehlt."]);
if ($arztId <= 0)  respond(["success" => false, "message" => "Ungültige Arzt-ID."]);
if ($datum === "")  respond(["success" => false, "message" => "Datum fehlt."]);

if (!preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datum)) {
    respond(["success" => false, "message" => "Datum-Format ungültig."]);
}

$patientId = requireToken($pdo, $token);

$stmt = $pdo->prepare("SELECT arzt_id FROM arzt WHERE arzt_id = ?");
$stmt->execute([$arztId]);
if (!$stmt->fetch()) respond(["success" => false, "message" => "Arzt nicht gefunden."]);

$stmt = $pdo->prepare("SELECT termin_id FROM termin WHERE arzt_id = ? AND datum = ?");
$stmt->execute([$arztId, $datum]);
if ($stmt->fetch()) respond(["success" => false, "message" => "Dieser Termin ist bereits vergeben."]);

// Insert appointment
$stmt = $pdo->prepare("INSERT INTO termin (arzt_id, patient_id, datum, beschreibung) VALUES (?, ?, ?, ?)");
$stmt->execute([$arztId, $patientId, $datum, $beschreibung]);
$terminId = (int) $pdo->lastInsertId();

// Save symptom IDs if provided
if ($symptomIds !== "") {
    $ids = array_filter(array_map('intval', explode(',', $symptomIds)));
    foreach ($ids as $sid) {
        try {
            $s = $pdo->prepare("INSERT IGNORE INTO termin_symptome (termin_id, symptom_id) VALUES (?, ?)");
            $s->execute([$terminId, $sid]);
        } catch (Exception $e) { /* ignore if table doesn't exist yet */ }
    }
}

respond(["success" => true, "termin_id" => $terminId, "message" => "Termin erfolgreich gebucht."]);
