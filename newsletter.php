<?php
// newsletter.php
// Generates a cybersecurity, food, and travel newsletter and optionally emails it to subscribers.

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once('secrets.php');
require_once('vendor/PHPMailer/Exception.php');
require_once('vendor/PHPMailer/PHPMailer.php');
require_once('vendor/PHPMailer/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

define('GEMINI_MODEL', 'gemini-2.5-flash');
define('GEMINI_ENDPOINT', 'https://generativelanguage.googleapis.com/v1beta/models/' . GEMINI_MODEL . ':generateContent?key=' . GEMINI_API_KEY);

$subscribersFile = 'subscribers.txt';

function getSubscribers() {
    global $subscribersFile;
    if (!file_exists($subscribersFile)) return [];
    $emails = file($subscribersFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return array_unique($emails);
}

function generateNewsletterContent() {
    $systemPrompt = "You are the T3chN0mad AI Editor. Generate a daily newsletter in pure HTML with INLINE cyberpunk/neon styling.
The newsletter must include the LATEST ground-truth intelligence using Google Search on:
1. Tech & Cybersecurity (latest breaches, zero-days, or trends)
2. Travel (top digital nomad cities, visa news, or hidden spots)
3. Food (street food discoveries or cafes for nomads)

RULES:
- ONLY output valid HTML. No markdown code blocks like ```html.
- Use inline CSS entirely. Do not use external stylesheets.
- Background should be dark (#0a0a0f), text light grey (#e0e0e0).
- Accent colors: Neon Cyan (#00f3ff) for headings/links, Neon Gold (#ffd700) for highlights, Purple (#bd00ff) for borders.
- Include a sleek header: 'T3chN0mad Daily Dispatch'.
- Make it look premium, modern, and readable in an email client.
- Always include specific real-world data and cite sources briefly.
- Conclude with 'Stay connected. Follow the tribe.'";

    $userPrompt = "Generate today's T3chN0mad newsletter with the most recent news on cybersecurity, digital nomad travel, and global food culture.";

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
                    ['text' => $userPrompt]
                ]
            ]
        ],
        'tools' => [
            ['google_search' => (object)[]]
        ],
        'generationConfig' => [
            'temperature' => 0.8,
            'maxOutputTokens' => 8192
        ]
    ];

    $ch = curl_init(GEMINI_ENDPOINT);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    // Extend timeout for generation
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200) {
        return false;
    }

    $responseData = json_decode($response, true);
    $htmlContent = '';
    
    if (isset($responseData['candidates'][0]['content']['parts'])) {
        foreach ($responseData['candidates'][0]['content']['parts'] as $part) {
            // we only want actual text, not the thinking part
            if (!isset($part['thought']) && isset($part['text'])) {
                $htmlContent .= $part['text'];
            }
        }
    }
    
    // Remove markdown formatting if Gemini wrapped it
    $htmlContent = preg_replace('/^```html\s*/i', '', $htmlContent);
    $htmlContent = preg_replace('/```$/', '', $htmlContent);
    
    return trim($htmlContent);
}

function sendEmails($htmlBody, $specificRecipient = null) {
    $subject = "T3chN0mad Daily Dispatch | " . date('M j, Y');
    $successCount = 0;

    $subscribers = [];
    if ($specificRecipient) {
        $subscribers[] = $specificRecipient;
    } else {
        $subscribers = getSubscribers();
    }

    if (empty($subscribers)) {
        return 0;
    }

    $mail = new PHPMailer(true);
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'mail.clint-ivins.co.uk';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'technomad@clint-ivins.co.uk';
        $mail->Password   = 'Mandrake@1976';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // SSL workaround if hosting has invalid cert
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        $mail->setFrom('technomad@clint-ivins.co.uk', 'T3chN0mad');
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        foreach ($subscribers as $email) {
            $mail->clearAddresses();
            $mail->addAddress($email);
            try {
                if ($mail->send()) {
                    $successCount++;
                }
            } catch (Exception $e) {
                // Silently fail per email and try next
                error_log("Failed to send to $email: " . $mail->ErrorInfo);
            }
        }
    } catch (Exception $e) {
        error_log("Mailer configuration failed: " . $mail->ErrorInfo);
    }
    
    return $successCount;
}

// Logic flow
if (isset($_GET['action']) && $_GET['action'] === 'generate_and_send') {
    $target = isset($_GET['preview_email']) ? $_GET['preview_email'] : null;
    $html = generateNewsletterContent();
    if ($html) {
        $sent = sendEmails($html, $target);
        echo "Newsletter generated and sent to $sent subscriber(s).";
    } else {
        echo "Failed to generate newsletter content via API.";
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'preview') {
    $html = generateNewsletterContent();
    if ($html) {
        echo $html;
    } else {
        echo "Failed to generate newsletter content.";
    }
} else {
    echo "Direct access to newsletter generation script. Use ?action=preview or ?action=generate_and_send";
}
