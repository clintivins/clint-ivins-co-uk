<?php
// subscribe.php
// Adds a subscriber to the file

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $file = 'subscribers.txt';
        $subscribers = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
        if (!in_array($email, $subscribers)) {
            file_put_contents($file, $email . PHP_EOL, FILE_APPEND | LOCK_EX);
            
            // Fire off a background HTTP request to generate AND email them the initial dispatch
            // This is asynchronous so it won't block the UI
            $url = "https://" . $_SERVER['HTTP_HOST'] . "/newsletter.php?action=generate_and_send&preview_email=" . urlencode($email);
            
            // Send async request
            $parts = parse_url($url);
            if (isset($parts['host'])) {
                $fp = @fsockopen(
                    ($parts['scheme'] === 'https' ? 'ssl://' : '') . $parts['host'],
                    isset($parts['port']) ? $parts['port'] : ($parts['scheme'] === 'https' ? 443 : 80),
                    $errno, $errstr, 3
                );
                
                if ($fp) {
                    $out = "GET " . $parts['path'] . "?" . $parts['query'] . " HTTP/1.1\r\n";
                    $out .= "Host: " . $parts['host'] . "\r\n";
                    $out .= "Connection: Close\r\n\r\n";
                    fwrite($fp, $out);
                    fclose($fp);
                }
            }
            
            echo "Welcome to the tribe! Your first dispatch is generating and will arrive shortly.";
        } else {
            echo "You are already subscribed to the Nomad Tribe!";
        }
    } else {
        echo "Please provide a valid email address.";
    }
} else {
    echo "Invalid request method.";
}
