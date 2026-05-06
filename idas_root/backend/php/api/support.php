<?php
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$patientId = requireToken($pdo, trim($_REQUEST["token"] ?? ""));

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $betreff = trim($_POST["betreff"] ?? "");
    $problem = trim($_POST["problembeschreibung"] ?? "");

    if ($betreff === "" || $problem === "") {
        respond(["success" => false, "message" => "Bitte alle Felder ausfüllen."]);
    }

    $stmt = $pdo->prepare(
        "INSERT INTO support (patient_id, betreff, problembeschreibung, status, datum)
         VALUES (?, ?, ?, 'offen', NOW())"
    );
    $stmt->execute([$patientId, $betreff, $problem]);
    respond(["success" => true, "message" => "Ticket erfolgreich gesendet."]);
}

// GET - load patient's tickets
$stmt = $pdo->prepare(
    "SELECT ticket_id, betreff, problembeschreibung, status, datum, antwort
     FROM support WHERE patient_id = ? ORDER BY datum DESC"
);
$stmt->execute([$patientId]);
$tickets = $stmt->fetchAll();
respond(["success" => true, "data" => $tickets]);
