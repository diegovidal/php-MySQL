<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

$loggedUserId = null;
if(isset($_COOKIE['userinfos'])) {
	$data = unserialize($_COOKIE['userinfos']);
	$loggedUserId = $data['userId'];
}

if(!(isset($_POST['userId']))) {
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$userId = (int) addslashes(strip_tags(trim($_POST['userId'])));

$pdo = Database::connect();

$sql_select= "SELECT * FROM userxachievement WHERE userId='$userId'";
$res = $pdo->query($sql_select);
$v_achieviments = array();

foreach ($res as $row) {
	$achievementId = (int) $row['achievementId'];

	$achievement_select = "SELECT * FROM achievement WHERE achievementId='$achievementId'";
	$data = $pdo->query($achievement_select)->fetch();

 	$v_achieviments[] = new Achievement(utf8_encode($achievementId),
 								   	 utf8_encode($row['challengeId']),
 								   	 utf8_encode($data['achievementTitle']),
 								   	 utf8_encode($data['achievementMedia']));
}

Database::disconnect();

$output = new JsonOutput(false, '' ,$v_achieviments);

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);
?>