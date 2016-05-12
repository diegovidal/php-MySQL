<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";
require_once "savePhoto.php";

if(!isset($_COOKIE['userinfos'])) {
	$output = new JsonOutput('false', 'Do login.', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$data = unserialize($_COOKIE['userinfos']);
$userId = $data['userId'];

$pdo = Database::connect();

$user_select = "SELECT user.*, title.titleText FROM user, title WHERE title.titleId=user.titleId AND userId=$userId LIMIT 1";
$user_query = $pdo->query($user_select);

if( !($user_query && ($user_query->rowCount() > 0))) {
	$output = new JsonOutput('false', 'Do Login.', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

if(isset($_POST['idFacebook'])) {
	$u_facebook = addslashes(strip_tags(trim($_POST['idFacebook'])));
	$sql = "UPDATE user SET userIdFacebook='$u_facebook' WHERE userId=$userId";
	$query = $pdo->query($sql);
}

if(isset($_POST['name'])) {
	$u_name = utf8_decode(addslashes(strip_tags(trim($_POST['name']))));
	$sql = "UPDATE user SET userName='$u_name' WHERE userId=$userId";
	$query = $pdo->query($sql);
}

if(isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	$u_email = addslashes(strip_tags(trim($_POST['email'])));
	$sql = "UPDATE user SET userEmail='$u_email' WHERE userId=$userId";
	$query = $pdo->query($sql);
}

if(isset($_POST['password'])) {
	$u_password = utf8_decode(addslashes(strip_tags(trim($_POST['password']))));
	$sql = "UPDATE user SET userPassword='$u_password' WHERE userId=$userId";
	$query = $pdo->query($sql);
}

$u_media = savePhoto();
if($u_media != '') {
	$sql = "UPDATE user SET userMedia='$u_media' WHERE userId=$userId";
	$query = $pdo->query($sql);
}

$sql = "SELECT user.*, title.titleText FROM user, title WHERE title.titleId=user.titleId AND userId=$userId LIMIT 1";
$query = $pdo->query($sql);
$row = $query->fetch(PDO::FETCH_ASSOC);

if($query && ($query->rowCount() > 0)) {
	$userRes = new User( utf8_encode($userId),
					 utf8_encode($row['userIdFacebook']),
					 utf8_encode($row['userEmail']),
					 utf8_encode($row['userName']),
					 utf8_encode($row['userMedia']),
					 utf8_encode($row['titleText']),
					 '0');

	$output = new JsonOutput('true', 'User updated', $userRes);
	$_SESSION['userinfos'] = $data;
	$resCookie = array( 'userId' => $userId, 'userEmail' => $u_email, 'userName' => $u_name, 'userMedia' => $u_media , 'userTitle' => $row['titleText']);
	unset($_COOKIE['userinfos']);
	setcookie('userinfos', serialize($resCookie));
}else{
	$output = new JsonOutput('false', 'Try Later', '');
}

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);
 
?>