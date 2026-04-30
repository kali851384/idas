<?php
function getCoordinates ($address) {
    $opts = [
        "http" => [
            "header" => "User-Agent: IDAS\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $url = "https://nominatim.openstreetmap.org/search?format=json&q=" . urlencode($address); // Openstreetmap API 
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);
    if (!empty($data)) {
        return [
            'lat' => $data[0]['lat'] * (pi() / 180), // Umrechnung von Grad in Radiant 
            'lon' => $data[0]['lon'] * (pi() / 180)
        ];
    } else {
        return null; 
    }
}

function getDistance ($address1, $address2, $radius = 6371000 /* Erdradius in Metern */) {
    $coord1 = getCoordinates($address1); // Adressen in Koordinaten umwaldeln
    $coord2 = getCoordinates($address2);
    if ($coord1 !== null && $coord2 !== null) {
        $latDelta = $coord2['lat'] - $coord1['lat']; // Veränderung der Breitengrade
        $lonDelta = $coord2['lon'] - $coord1['lon']; // Veränderung der Längengrade
        $angDist = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($coord1['lat']) * cos($coord2['lat']) * pow(sin($lonDelta / 2), 2)));  // Haversine Formel zur berechnung der Entfernung
        $dist = $angDist * $radius;                                                                                   
        return $dist; 
    } else {
        return null; 
    }
}   
?>