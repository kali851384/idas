<?php
// C:\xampp\htdocs\IDAS\backend\api\symptome.php
// GET ?token=...
// Returns: { success, data: [ { id, name }, ... ] }

require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$token = trim($_GET["token"] ?? "");
if ($token === "") respond(["success" => false, "message" => "Token fehlt."]);

requireToken($pdo, $token); // just validate, don't need patient_id here

$stmt = $pdo->query(
    "SELECT symptom_id AS id, name FROM symptome ORDER BY name ASC"
);
$rows = $stmt->fetchAll();

respond(["success" => true, "data" => $rows]);
