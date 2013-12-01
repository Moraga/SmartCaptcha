<?php

require '../SmartCaptcha.php';

session_start();

$captcha = new SmartCaptcha;

// stores the Captcha's answer in session
$_SESSION['answer'] = $captcha->create();

$captcha->output();

?>