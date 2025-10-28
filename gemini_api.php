<?php

header('Content-Type: application/json; charset=utf-8');

$apiKey = 'AIzaSyA4cYtbqb3qnWnIouMnkpymvyq-7zYURUQ'; 
$model = 'gemini-2.5-pro'; 
$input = json_decode(file_get_contents('php://input'), true);
$userPrompt = $input['prompt'] ?? '';

if (empty($userPrompt)) {
    http_response_code(400); // Bad Request
    echo json_encode(['status' => 'error', 'message' => 'Prompt is empty.']);
    exit();
}

$apiUrl = "https://generativelanguage.googleapis.com/v1/models/{$model}:generateContent?key={$apiKey}";
$postData = json_encode(
    ['contents' => [['parts' => [['text' => $userPrompt]]]]],
    JSON_UNESCAPED_UNICODE 
);

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
$responseJson = curl_exec($ch);

if (curl_errno($ch)) {
    http_response_code(500); 
    echo json_encode(['status' => 'error', 'message' => 'cURL Error: ' . curl_error($ch)]);
    exit;
}
curl_close($ch);

$responseObject = json_decode($responseJson, true); 
if (isset($responseObject['candidates'][0]['content']['parts'][0]['text'])) {
    $answer = $responseObject['candidates'][0]['content']['parts'][0]['text'];
    echo json_encode(['status' => 'success', 'answer' => trim($answer)]);
} else {
    http_response_code(502); 
    echo json_encode([
        'status' => 'error',
        'message' => 'Could not extract a valid answer from the API response.',
        'raw_response_from_google' => $responseObject
    ]);
}
?>
