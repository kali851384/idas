<?php
require_once "db.php";
header("Content-Type: application/json; charset=utf-8");

$arzt_id = intval($_GET['arzt_id'] ?? 0);
$datum   = trim($_GET['datum'] ?? '');

if (!$arzt_id || !$datum) {
    respond(["success" => false, "message" => "Fehlende Parameter"]);
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $datum)) {
    respond(["success" => false, "message" => "Ungültiges Datum"]);
}

$date      = new DateTime($datum);
$wochentag = (int)$date->format('N'); // 1=Mon ... 7=Sun

// 1. Check if doctor has ANY working hours set
$stmt = $pdo->prepare("SELECT COUNT(*) AS c FROM arzt_arbeitszeiten WHERE arzt_id=?");
$stmt->execute([$arzt_id]);
$has_schedule = (int)$stmt->fetch()['c'] > 0;

// 2. Get working hours for this weekday
$stmt = $pdo->prepare("SELECT von, bis FROM arzt_arbeitszeiten WHERE arzt_id=? AND wochentag=?");
$stmt->execute([$arzt_id, $wochentag]);
$hours = $stmt->fetch();

// If schedule set but this day has no entry = day off
if ($has_schedule && !$hours) {
    respond([
        "success"   => true,
        "slots"     => [],
        "has_hours" => true,
        "day_off"   => true,
        "working"   => null,
        "message"   => "Der Arzt arbeitet an diesem Tag nicht."
    ]);
}

// 3. Check blocked slots for this day
$stmt = $pdo->prepare("SELECT von, bis FROM arzt_blocked_slots WHERE arzt_id=? AND datum=?");
$stmt->execute([$arzt_id, $datum]);
$blocked_slots = $stmt->fetchAll();

// Check if entire day is blocked
foreach ($blocked_slots as $block) {
    if (empty($block['von']) && empty($block['bis'])) {
        respond([
            "success"   => true,
            "slots"     => [],
            "has_hours" => true,
            "day_off"   => false,
            "working"   => null,
            "message"   => "Der Arzt ist an diesem Tag nicht verfügbar."
        ]);
    }
}

// 4. Already booked appointments
$stmt = $pdo->prepare(
    "SELECT DATE_FORMAT(datum, '%H:%i') AS uhrzeit FROM termin
     WHERE arzt_id=? AND DATE(datum)=? AND status != 'Abgesagt'"
);
$stmt->execute([$arzt_id, $datum]);
$booked = array_column($stmt->fetchAll(), 'uhrzeit');

// 5. All possible slots
$all_slots = [
    "08:00","08:30","09:00","09:30","10:00","10:30",
    "11:00","11:30","13:00","13:30","14:00","14:30",
    "15:00","15:30","16:00","16:30","17:00"
];

$available = [];
foreach ($all_slots as $slot) {
    $slot_time = strtotime("$datum $slot");

    // Skip past times
    if ($slot_time <= time()) continue;

    // Check working hours
    if ($hours) {
        $von = strtotime("$datum " . $hours['von']);
        $bis = strtotime("$datum " . $hours['bis']);
        if ($slot_time < $von || $slot_time >= $bis) continue;
    }

    // Check blocked time ranges
    $is_blocked = false;
    foreach ($blocked_slots as $block) {
        if (!empty($block['von']) && !empty($block['bis'])) {
            $block_von = strtotime("$datum " . $block['von']);
            $block_bis = strtotime("$datum " . $block['bis']);
            if ($slot_time >= $block_von && $slot_time < $block_bis) {
                $is_blocked = true; break;
            }
        }
    }
    if ($is_blocked) continue;

    // Check already booked
    if (in_array($slot, $booked)) continue;

    $available[] = $slot;
}

respond([
    "success"   => true,
    "slots"     => $available,
    "has_hours" => $has_schedule,
    "day_off"   => false,
    "working"   => $hours ? [
        "von" => substr($hours['von'], 0, 5),
        "bis" => substr($hours['bis'], 0, 5)
    ] : null
]);