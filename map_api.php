<?php
header('Content-Type: application/json');

$file = 'locations.json';

// Initialize file if it doesn't exist
if (!file_exists($file)) {
    file_put_contents($file, json_encode([]));
    chmod($file, 0666);
}

// Handle GET requests (load pins)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $data = file_get_contents($file);
    echo $data ? $data : '[]';
    exit;
}

// Handle POST requests (save pin)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $newPin = json_decode($json, true);

    if ($newPin && isset($newPin['lat']) && isset($newPin['lng'])) {
        $data = json_decode(file_get_contents($file), true) ?: [];
        $newPin['name'] = htmlspecialchars($newPin['name']);
        $newPin['description'] = htmlspecialchars($newPin['description']);
        $newPin['category'] = htmlspecialchars($newPin['category']);

        $data[] = $newPin;
        file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT));
        echo json_encode(["status" => "success"]);
    }
    else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid data"]);
    }
    exit;
}
?>
