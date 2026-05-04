<?php
// C:\xampp\htdocs\IDAS\backend\api\matching.php
// GET ?token=...&symptome=1,2,3
//
// Returns top 3 Fachbereiche scored by symptomdet, with their doctors:
// {
//   success: true,
//   ergebnisse: [
//     {
//       fachbereich_id, fachbereich, punkte,
//       aerzte: [ { arzt_id, name, telefon, email, addresse }, ... ]
//     },
//     ...
//   ]
// }

require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$token = trim($_GET["token"] ?? "");
if ($token === "") respond(["success" => false, "message" => "Token fehlt."]);
requireToken($pdo, $token);

$symptomeRaw = trim($_GET["symptome"] ?? "");
if ($symptomeRaw === "") respond(["success" => false, "message" => "Keine Symptome übergeben."]);

// Parse and sanitize symptom IDs (integers only)
$symptomIds = array_filter(
    array_map('intval', explode(",", $symptomeRaw)),
    fn($id) => $id > 0
);

if (empty($symptomIds)) {
    respond(["success" => false, "message" => "Ungültige Symptom-IDs."]);
}

// Build placeholders for IN clause
$placeholders = implode(",", array_fill(0, count($symptomIds), "?"));

// Score each Fachbereich by summing punkte for selected symptoms
$stmt = $pdo->prepare("
    SELECT
        sd.fachbereich_id,
        f.name AS fachbereich,
        SUM(sd.punkte) AS punkte
    FROM symptomdet sd
    JOIN fachbereich f ON f.fachbereich_id = sd.fachbereich_id
    WHERE sd.symptom_id IN ($placeholders)
    GROUP BY sd.fachbereich_id, f.name
    ORDER BY punkte DESC
    LIMIT 3
");
$stmt->execute($symptomIds);
$fachbereiche = $stmt->fetchAll();

if (empty($fachbereiche)) {
    respond(["success" => false, "message" => "Keine passenden Fachbereiche gefunden."]);
}

// For each Fachbereich, get its doctors
$ergebnisse = [];
foreach ($fachbereiche as $fb) {
    $stmtAerzte = $pdo->prepare("
        SELECT
            arzt_id,
            name,
            telefonnummer AS telefon,
            email,
            addresse
        FROM arzt
        WHERE fachbereich_id = ?
        ORDER BY name ASC
    ");
    $stmtAerzte->execute([$fb["fachbereich_id"]]);
    $aerzte = $stmtAerzte->fetchAll();

    $ergebnisse[] = [
        "fachbereich_id" => (int) $fb["fachbereich_id"],
        "fachbereich"    => $fb["fachbereich"],
        "punkte"         => (int) $fb["punkte"],
        "aerzte"         => $aerzte,
    ];
}

respond(["success" => true, "ergebnisse" => $ergebnisse]);
