<?php
/**
 * Server-side proxy for Gemini API calls.
 * Keeps the API key on the server — never exposed to the browser.
 */
require_once __DIR__ . '/../env_loader.php';
loadEnv(__DIR__ . '/../.env');

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Read JSON body
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['contents'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request body']);
    exit;
}

$apiKey = $_ENV['GEMINI_API_KEY'] ?? '';
if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit;
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . urlencode($apiKey);

// Build the request payload (pass through what the frontend sends)
$payload = json_encode([
    'systemInstruction' => $input['systemInstruction'] ?? null,
    'contents' => $input['contents']
]);

// cURL call to Gemini API
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_SSL_VERIFYPEER => true,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($curlError) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach API: ' . $curlError]);
    exit;
}

http_response_code($httpCode);
echo $response;
?>
