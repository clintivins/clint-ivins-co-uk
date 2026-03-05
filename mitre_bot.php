<?php
/**
 * MITRE ATT&CK Intel Bot Backend
 * Proxies requests to Google Gemini API (AI Studio) with Web Search Grounding
 */

// Suppress PHP warnings/notices from corrupting JSON output
error_reporting(E_ERROR);
ini_set('display_errors', '0');

header('Content-Type: application/json');

// --- CONFIGURATION ---
require_once('secrets.php');
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
$systemPrompt = "You are 'Gordie the ID guy', a chill surfer-dude cybersecurity expert on the T3chN0mad crew. You sprinkle in surfer lingo ('Dude!', 'Gnarly!', 'Stoked!', 'Shaka brah!') but YOUR #1 PRIORITY IS GIVING REAL, SUBSTANTIVE ANSWERS.

CRITICAL RULES:
1. LEAD WITH THE ACTUAL ANSWER. Do NOT waste the first paragraph on greetings or personality filler. Get straight to the facts, data, and intel.
2. ALWAYS use Google Search to pull the LATEST real-time data before answering. Every response must contain current, factual information.
3. ALWAYS include specific details: names, dates, CVE numbers, technique IDs, statistics, affected organizations, and timelines.
4. ALWAYS include clickable source URLs in your response so the user can verify and read more.
5. You can answer ANY question — cybersecurity, general knowledge, coding, anything. If it's outside your specialty, still answer helpfully.

FRAMEWORKS & SOURCES TO REFERENCE:
- MITRE ATT&CK (always cite technique IDs like T1078, T1566, etc.)
- NIST Cybersecurity Framework (CSF 2.0) and NIST SP 800-63 for identity
- CIS Controls
- OWASP Top 10
- CISA Known Exploited Vulnerabilities (KEV) catalog
- Link to relevant pages: https://attack.mitre.org, https://nvd.nist.gov, https://www.cisa.gov/known-exploited-vulnerabilities-catalog, https://owasp.org

NEWS SOURCES TO SEARCH & CITE:
- BleepingComputer, The Hacker News, Krebs on Security, Dark Reading, SecurityWeek, The Record, Ars Technica, TechCrunch Security

RESPONSE FORMAT:
- Start with the actual answer immediately
- Use **bold** for key terms, bullet points for lists
- Include MITRE ATT&CK technique IDs where relevant
- End with a 'Sources' section listing URLs you referenced
- Sprinkle in surfer personality throughout, but NEVER at the expense of content
- Give comprehensive, detailed answers — minimum 300 words for any technical question";

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
        'temperature' => 0.9,
        'maxOutputTokens' => 8192
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

// Extract thinking and response from parts
$botReply = '';
$thinkingText = '';

if (isset($responseData['candidates'][0]['content']['parts'])) {
    foreach ($responseData['candidates'][0]['content']['parts'] as $part) {
        if (isset($part['thought']) && $part['thought'] === true) {
            $thinkingText .= $part['text'];
        } else if (isset($part['text'])) {
            $botReply .= $part['text'];
        }
    }
}

if (empty($botReply)) {
    $botReply = 'Data retrieval interrupted.';
}

// Extract grounding source URLs from the API response and append them
$sources = [];
if (isset($responseData['candidates'][0]['groundingMetadata']['groundingChunks'])) {
    foreach ($responseData['candidates'][0]['groundingMetadata']['groundingChunks'] as $chunk) {
        if (isset($chunk['web']['uri']) && isset($chunk['web']['title'])) {
            $title = trim($chunk['web']['title']);
            $uri = $chunk['web']['uri'];
            
            // Extract domain from title or URI for dedup and clean link
            // Try to find a domain pattern in the title (e.g. "Article - SiteName")
            // If title looks like a domain (contains a dot, no spaces), use it directly
            if (preg_match('/^[a-zA-Z0-9.-]+\.[a-z]{2,}$/', $title)) {
                $domain = $title;
            } else {
                // Extract domain from the URI if possible
                $parsedUrl = parse_url($uri);
                $domain = $parsedUrl['host'] ?? $title;
            }
            
            // Skip if we already have this domain
            if (isset($sources[$domain])) continue;
            
            $cleanUrl = 'https://' . $domain;
            $sources[$domain] = ['title' => $title, 'url' => $cleanUrl];
            
            // Limit to 5 sources
            if (count($sources) >= 5) break;
        }
    }
}

// Append sources to the reply if any were found
if (!empty($sources)) {
    $botReply .= "\n\n---\n🔗 **Sources:**\n";
    foreach ($sources as $domain => $info) {
        $botReply .= "• [{$info['title']}]({$info['url']})\n";
    }
}

echo json_encode([
    'status' => 'success',
    'message' => $botReply,
    'thinking' => $thinkingText,
    'sources' => array_values($sources)
]);