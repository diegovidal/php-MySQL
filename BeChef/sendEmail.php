<?php 

$name = utf8_decode(addslashes(strip_tags(trim($_POST['demo-name']))));
$email = utf8_decode(addslashes(strip_tags(trim($_POST['demo-email']))));
$human = utf8_decode(addslashes(strip_tags(trim($_POST['demo-human']))));
$message = utf8_decode(addslashes(strip_tags(trim($_POST['demo-message']))));

$to      = 'bechefcontato@gmail.com'; 
$subject = 'Feedback - BeChef';
$message = $message;
$headers = 'From: ' . $email . "\r\n" .
    'Reply-To: ' . $email . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


if (strlen($human) > 0) {
	mail($to, $subject, $message, $headers);
	header("Location:emailSent.html");
}
else{
	header("Location:index.html");
}



?>