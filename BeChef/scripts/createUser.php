<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";
require_once "savePhoto.php";

if(!(isset($_POST['name']) &&
     isset($_POST['email'])  &&
     isset($_POST['password']) &&
     filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
{
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$grava_name = utf8_decode(addslashes(strip_tags(trim($_POST['name']))));
$grava_email = addslashes(strip_tags(trim($_POST['email'])));
$grava_password = utf8_decode(addslashes(strip_tags(trim($_POST['password']))));

$output = new JsonOutput('false', '', '');

$pdo = Database::connect();

$sql_select = "SELECT userId FROM user WHERE userEmail='$grava_email'";
$query_select = $pdo->query($sql_select);

if(!($query_select && $query_select->rowCount() == 0))
{
	$output = new JsonOutput('false', 'Email already exist.', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return; 
}

$grava_facebook = '';
if(isset($_POST['idFacebook'])) {
	$grava_facebook = addslashes(strip_tags(trim($_POST['idFacebook'])));
}

$grava_media = savePhoto();
if($grava_media == '') {
	$grava_media = 'defaultUser.jpg';
}

$sql = "INSERT INTO user (userName, userEmail, userPassword, userIdFacebook, userMedia) VALUES ('$grava_name','$grava_email','$grava_password','$grava_facebook','$grava_media')";
if($grava_facebook == '') {
	$sql = "INSERT INTO user (userName, userEmail, userPassword, userMedia) VALUES ('$grava_name','$grava_email','$grava_password','$grava_media')";
} 

$query = $pdo->query($sql);

if($query && ($query->rowCount() > 0)){
	$output = new JsonOutput('true', 'Created user', '');
}else{
	$output = new JsonOutput('false', 'Facebook or email already used.', '');
}

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>