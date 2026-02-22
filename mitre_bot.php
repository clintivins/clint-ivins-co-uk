<?php
/**
 * MITRE ATT&CK Intel Bot Backend
 * Proxies requests to Google Gemini API (AI Studio) with Web Search Grounding
 */

header('Content-Type: application/json');

// --- CONFIGURATION ---
define('GEMINI_API_KEY', 'AIzaSyBkRualRDed7YcKnmg1furs9LpFdGl-pJA');
define('GEMINI_MODEL', 'gemini-2.5-flash');
define('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY);

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);
$userMessage = isset($input['message']) ? trim($input['message']) : '';

if (empty($userMessage)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Empty message protocol.',
        'error' => 'EMPTY_INPUT'
    ]);
    exit;
}

// System Prompt for Grounding & Personality
$systemPrompt = "You are 'Gordie the ID guy', a super friendly, helpful, and highly knowledgeable AI Identity Specialist and MITRE ATT&CK expert.
Personality: Warm, approachable, and enthusiastic. You're like a helpful colleague who loves talking about cybersecurity. Use phrases like 'Hey there!', 'Glad to help!', and 'Let's see what I can find for you.' 
Maintain your technical edge but deliver it with a smile. You're part of the T3chN0mad team.
Capabilities: You have real-time access to the web (Google Search) to scrape for latest breaches and identity threats.
Objective: 
1. Provide natural-language briefings. Keep it conversational and easy to digest.
2. If asked about breaches or newest threats, use Google Search to get real-time info.
3. Your primary focus is Identity Security and MITRE ATT&CK techniques.
Always be accurate, but keep the tone light and friendly!";

// Gemini Request Payload with Google Search Grounding
$data = [
    'system_instruction' => [
        'parts' => [
            ['text' => $systemPrompt]
        ]
    ],
    'contents' => [
        [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ]
    ],
    'tools' => [
        ['google_search' => (object)[]]
    ],
    'generationConfig' => [
        'temperature' => 0.8,
        'maxOutputTokens' => 2048
    ]
];

$ch = curl_init(GEMINI_ENDPOINT);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

$responseData = json_decode($response, true);

if ($httpCode !== 200) {
    $msg = "Uplink Error ($httpCode)";
    if (isset($responseData['error']['message'])) {
        $msg = $responseData['error']['message'];
    }
    echo json_encode([
        'status' => 'error',
        'message' => $msg,
        'error' => $responseData['error']['status'] ?? 'API_FAILURE'
    ]);
    exit;
}

// Extract the response text
$botReply = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? 'Data retrieval interrupted.';

echo json_encode([
    'status' => 'success',
    'message' => $botReply
]);