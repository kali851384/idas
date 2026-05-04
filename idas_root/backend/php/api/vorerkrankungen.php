<?php
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$patientId = requireToken($pdo, trim($_REQUEST["token"] ?? ""));
$method    = $_POST["_method"] ?? $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $name = trim($_POST["name"] ?? "");
    $seit = trim($_POST["seit"] ?? "");
    if ($name === "") respond(["success" => false, "message" => "Name fehlt."]);
    $stmt = $pdo->prepare(
        "INSERT INTO vorerkrankungen (patient_id, erkrankungsname, seit) VALUES (?, ?, ?)"
    );
    $stmt->execute([$patientId, $name, $seit ?: null]);
    respond(["success" => true, "message" => "Gespeichert."]);
}

if ($method === "PUT") {
    $id   = (int)($_POST["id"] ?? 0);
    $seit = trim($_POST["seit"] ?? "");
    $stmt = $pdo->prepare(
        "UPDATE vorerkrankungen SET seit = ? WHERE vorerkrankung_id = ? AND patient_id = ?"
    );
    $stmt->execute([$seit, $id, $patientId]);
    respond(["success" => true]);
}

if ($method === "DELETE") {
    $id = (int)($_POST["id"] ?? 0);
    $stmt = $pdo->prepare(
        "DELETE FROM vorerkrankungen WHERE vorerkrankung_id = ? AND patient_id = ?"
    );
    $stmt->execute([$id, $patientId]);
    respond(["success" => true]);
}

// GET
$stmt = $pdo->prepare(
    "SELECT vorerkrankung_id AS id,
            erkrankungsname  AS name,
            COALESCE(seit, '') AS seit
     FROM vorerkrankungen WHERE patient_id = ? ORDER BY vorerkrankung_id DESC"
);
$stmt->execute([$patientId]);
respond(["success" => true, "data" => $stmt->fetchAll()]);