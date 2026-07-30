<?php
// Dynamic Math Captcha Generator for PHP
session_start();
header('Content-Type: application/json');

$num1 = rand(2, 9);
$num2 = rand(2, 9);
$answer = $num1 + $num2;
$captchaId = 'cap_' . time() . '_' . substr(md5(uniqid()), 0, 6);

// Store captcha answer in PHP session and file cache
$_SESSION['captcha_answer'] = $answer;
$_SESSION['captcha_id'] = $captchaId;

// Also persist to captcha directory for cross-request validation
$captchaDir = __DIR__ . '/data/captchas/';
if (!is_dir($captchaDir)) {
    mkdir($captchaDir, 0755, true);
}
file_put_contents($captchaDir . $captchaId . '.json', json_encode(array(
    'answer' => $answer,
    'createdAt' => time()
)));

echo json_encode(array(
    'captchaId' => $captchaId,
    'question' => "What is $num1 + $num2?"
));
exit;
?>
