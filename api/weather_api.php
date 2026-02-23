<?php
/**
 * Server-side proxy for OpenWeatherMap API.
 * Keeps the API key on the server — never exposed to the browser.
 */
require_once __DIR__ . '/../config/env_loader.php';
loadEnv(__DIR__ . '/../.env');

header('Content-Type: application/json');

// Only accept GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$lat = isset($_GET['lat']) ? floatval($_GET['lat']) : null;
$lon = isset($_GET['lon']) ? floatval($_GET['lon']) : null;

if ($lat === null || $lon === null) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing lat or lon parameter']);
    exit;
}

$apiKey = $_ENV['OPENWEATHER_API_KEY'] ?? '';
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit;
}

$url = "https://api.openweathermap.org/data/2.5/weather?lat=" . urlencode($lat)
     . "&lon=" . urlencode($lon)
     . "&units=metric&appid=" . urlencode($apiKey);

$context = stream_context_create([
    'http' => ['timeout' => 10]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach weather API']);
    exit;
}

echo $response;
?>
