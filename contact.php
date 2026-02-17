<?php
header('Access-Control-Allow-Origin: *');

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Helper to sanitize input
    function sanitize($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $name = sanitize($_POST["name"]);
    $email = sanitize($_POST["email"]);
    $message = sanitize($_POST["message"]);
    $to = "clinton@clint-ivins.co.uk";
    $subject = "New Contact from T3chN0mad: " . $name;

    // Validate inputs
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: about.html?status=empty");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
         header("Location: about.html?status=invalid_email");
         exit;
    }

    // Email Headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

// Webhook URL
    $discordWebhookUrl = "https://discordapp.com/api/webhooks/1471804922977583211/RtmtnaHCIhc9zPiEACxeNaAeLB0IvfvD2fneJMpceRno29qGo3W2V2ezdpCMRbdrijaJ";

    // Prepare Discord Payload (decode HTML entities for better readability in Discord)
    $discord_name = htmlspecialchars_decode($name);
    $discord_email = htmlspecialchars_decode($email);
    $discord_message = htmlspecialchars_decode($message);

    $discordMessage = "**New Contact Form Submission**\n";
    $discordMessage .= "**Name:** $discord_name\n";
    $discordMessage .= "**Email:** $discord_email\n";
    $discordMessage .= "**Message:**\n$discord_message";

    $json_data = json_encode([
        "content" => $discordMessage,
        "username" => "T3chN0mad Bot"
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );


    // Send to Discord using Curl
    $ch = curl_init( $discordWebhookUrl );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec( $ch );
    curl_close( $ch );

    // Send the email
    // We send to Discord regardless of email success, or we could check response.
    // For now, we proceed to email.

    // Determine redirect page
    $redirect_page = "about.html";
    $redirect_anchor = "#contact";
    
    if (isset($_POST["source_page"]) && $_POST["source_page"] === "index") {
        $redirect_page = "index.html";
        $redirect_anchor = "#connect";
    }

    if (mail($to, $subject, $email_content, $headers)) {
        // Redirect back to page with success 
         header("Location: $redirect_page?status=success$redirect_anchor");
    } else {
         header("Location: $redirect_page?status=error$redirect_anchor");
    }

} else {
    // Not a POST request
    header("Location: about.html");
}
?>
