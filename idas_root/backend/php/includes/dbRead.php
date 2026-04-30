<?php
require "../includes/db_config.php";

function readSymptoms ($conn) {
    $sql = "select symptom_id, name from symptome;";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result; 
}

function readSymptomDetail ($conn) {
    
}

function countSymptomDet ($conn, $ids) {
    $sql = "SELECT fb.fachbereich_id, fb.name, COUNT(*) as anzahl FROM symptomdet sd 
JOIN fachbereich fb ON fb.fachbereich_id = sd.fachbereich_id 
WHERE sd.symptom_id IN ($ids) 
GROUP BY fb.fachbereich_id, fb.name 
ORDER BY anzahl DESC LIMIT 1";
    $result = $conn->query($sql); 
    return $result;
}

function readDoctors ($conn, $fbId) {
    $sql = "select arzt_id, name, fachbereich_id, addresse from arzt where fachbereich_id = {$fbId}";
    $result = $conn->query($sql);
    return $result;
}

function readArea ($conn) {
    
}
?>