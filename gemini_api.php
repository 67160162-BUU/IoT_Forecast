<?php
// gemini_api.php (Final Version)

header('Content-Type: application/json; charset=utf-8');

// --- 1. CONFIGURATION ---
$apiKey = 'AIzaSyA4cYtbqb3qnWnIouMnkpymvyq-7zYURUQ'; // <--- PASTE YOUR API KEY HERE
$model = 'gemini-2.5-pro'; // Use a valid model name

// --- 2. GET PROMPT FROM JAVASCRIPT ---
$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = $input['prompt'] ?? '';

if (empty($userPrompt)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Prompt is empty.']);
    exit();
}

// --- 3. PREPARE DATA FOR GOOGLE ---
$apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";
$postData = json_encode(
    ['contents' => [['parts' => [['text' => $userPrompt]]]]],
    JSON_UNESCAPED_UNICODE // Important for Thai characters
);

// --- 4. SEND REQUEST VIA cURL ---
$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$responseJson = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

// --- 5. PROCESS THE RESPONSE (Logic adapted from your working code) ---
$responseObject = json_decode($responseJson, true); // Use true for associative array

// Check if the expected answer text exists
if (isset($responseObject['candidates'][0]['content']['parts'][0]['text'])) {
    $answer = $responseObject['candidates'][0]['content']['parts'][0]['text'];
    // Send success response
    echo json_encode(['status' => 'success', 'answer' => trim($answer)]);
} else {
    // If the answer text is not found, it's an error from the API
    http_response_code(502); // Bad Gateway (error from upstream server)
    // Send the raw response from Google for debugging
    echo json_encode([
        'status' => 'error',
        'message' => 'Could not extract a valid answer from the API response.',
        'raw_response_from_google' => $responseObject
    ]);
}
?>