<?php
// Ankabit Farm PHP Configuration
define('ADMIN_EMAIL', 'olaomansur@gmail.com');
define('FROM_EMAIL', 'Ankabit Farm <onboarding@resend.dev>');
define('MIN_WHOLESALE_CRATES', 100);
define('LEADS_FILE_PATH', __DIR__ . '/data/leads.json');

// Resend API Key (Set in environment or paste your API key here)
define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: 're_123456789_your_resend_key_here');

// Helper function to send emails via Resend API using PHP cURL
function sendResendEmail($toEmail, $subject, $htmlContent) {
    $apiKey = RESEND_API_KEY;
    
    // If no valid API key is set, log gracefully
    if (empty($apiKey) || strpos($apiKey, 'your_resend_key') !== false) {
        error_log("Resend API Key not configured. Email to $toEmail logged: $subject");
        return array('success' => true, 'mock' => true);
    }

    $payload = json_encode(array(
        'from' => FROM_EMAIL,
        'to' => array($toEmail),
        'subject' => $subject,
        'html' => $htmlContent
    ));

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $apiKey,
        'Content-Type: application/json'
    ));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return array(
        'success' => ($httpCode >= 200 && $httpCode < 300),
        'httpCode' => $httpCode,
        'response' => json_decode($response, true)
    );
}
?>
