<?php
// ─── Database connection ───────────────────────────────────────────────────
// Put this file in: C:\xampp\htdocs\IDAS\backend\api\db.php

$host   = "localhost";
$dbname = "idas";
$user   = "root";
$pass   = "";          // default XAMPP has no password

$pdo = new PDO(
    "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
    $user,
    $pass,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]
);

// ─── Helper: validate session token, return patient_id or die ─────────────
function requireToken(PDO $pdo, string $token): int {
    $stmt = $pdo->prepare(
        "SELECT patient_id FROM app_sessions WHERE token = ? 
         AND erstellt > DATE_SUB(NOW(), INTERVAL 30 DAY)"
    );
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) {
        echo json_encode(["success" => false, "message" => "Ungültiger oder abgelaufener Token."]);
        exit;
    }
    return (int) $row["patient_id"];
}

// ─── Helper: send JSON response ────────────────────────────────────────────
function respond(array $data): void {
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data);
    exit;
}
