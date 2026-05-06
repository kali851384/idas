<?php
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$patientId = requireToken($pdo, trim($_REQUEST["token"] ?? ""));

// Cancel appointment
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tid = (int)($_POST["termin_id"] ?? 0);
    if (!$tid) respond(["success" => false, "message" => "Keine Termin-ID"]);
    $stmt = $pdo->prepare("DELETE FROM termin WHERE termin_id = ? AND patient_id = ?");
    $stmt->execute([$tid, $patientId]);
    respond(["success" => true, "message" => "Termin abgesagt"]);
}

// Get all appointments with symptoms
$stmt = $pdo->prepare(
    "SELECT t.termin_id, t.datum, t.beschreibung,
            a.name AS arzt_name,
            a.telefonnummer AS arzt_telefon,
            a.email AS arzt_email,
            f.name AS fachbereich,
            COALESCE(t.status, CASE WHEN t.datum > NOW() THEN 'Bevorstehend' ELSE 'Abgeschlossen' END) AS status     FROM termin t
     JOIN arzt a ON t.arzt_id = a.arzt_id
     JOIN fachbereich f ON a.fachbereich_id = f.fachbereich_id
     WHERE t.patient_id = ?
     ORDER BY t.datum DESC"
);
$stmt->execute([$patientId]);
$termine = $stmt->fetchAll();

// Get symptoms for each termin
foreach ($termine as &$termin) {
    $tid = $termin["termin_id"];
    try {
        $s = $pdo->prepare(
            "SELECT s.name FROM termin_symptome ts
             JOIN symptome s ON ts.symptom_id = s.symptom_id
             WHERE ts.termin_id = ?"
        );
        $s->execute([$tid]);
        $termin["symptome"] = array_column($s->fetchAll(), "name");
    } catch (Exception $e) {
        $termin["symptome"] = [];
    }
}

respond(["success" => true, "data" => $termine]);
