<?php

session_start();

if (strcasecmp($_POST['captcha'], $_SESSION['answer']) === 0) {
	echo '<h4>You can pass!</h4>';
}
else {
	echo '<h4>you can not pass. :(</h4>';
}

// debug
echo '<pre>';

echo "POST\n";
print_r($_POST);

echo "\nSESSION\n";
print_r($_SESSION);

echo '</pre>';


?>

<a href="form.html">Back</a>