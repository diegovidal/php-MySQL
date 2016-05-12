<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";
require_once "savePhoto.php";

if(!isset($_COOKIE['userinfos'])) {
	$output = new JsonOutput('false', 'do login.', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

if( !(isset($_POST['detail']) &&
	  isset($_POST['challengeId']) &&
	  isset($_FILES['photo']))) {
	$output = new JsonOutput('false', 'Invalid parameter', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}	

$data = unserialize($_COOKIE['userinfos']);
$userId = $data['userId'];
$detail = utf8_decode(addslashes(strip_tags(trim($_POST['detail']))));
$challengeId = addslashes(strip_tags(trim($_POST['challengeId'])));

$pdo = Database::connect();

$challenge_select = "SELECT challengeMaxPoints, challengeNumberOfDishes FROM challenge WHERE challengeId='$challengeId'";
$challenge_query = $pdo->query($challenge_select);

if(!($challenge_query->rowCount() > 0)) { //challenge not exist
	$output = new JsonOutput('false', 'invalid challengeId', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$challenge_info = $challenge_query->fetch();
//Contando número de submissions
$count_select = "SELECT COUNT(*) FROM submission WHERE userId='$userId' and challengeId='$challengeId'";
$count = $pdo->query($count_select)->fetch(); 

if(!($count['COUNT(*)'] < $challenge_info['challengeNumberOfDishes'])) { 
	$output = new JsonOutput('false', 'number max of dishes', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$grava_media = savePhoto();
if($grava_media == '') {
	$grava_media = 'defaultSubmission.jpg';
}

$grava_points = $challenge_info['challengeMaxPoints'] / $challenge_info['challengeNumberOfDishes'];
$sql = "";
if(!isset($_POST['recipeId'])) {
	$grava_points = $grava_points / 2.0;
	$sql = "INSERT INTO submission (submissionDetail, userId, challengeId, submissionMedia, submissionPoints) VALUES ('$detail','$userId','$challengeId','$grava_media','$grava_points')";
} else {
	$grava_recipe = $_POST['recipeId'];
	$sql = "INSERT INTO submission (submissionDetail, userId, challengeId, submissionMedia, submissionPoints, recipeId) VALUES ('$detail','$userId','$challengeId','$grava_media','$grava_points', '$grava_recipe')";
}

$insert_query = $pdo->query($sql);
if($insert_query->rowCount() == 0) { 
	$output = new JsonOutput('false', 'Try Later', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}


$info = array();
//Avaliação de mudança de titulo
//Avaliação da conquista
//Feito no trigger da submissão, tem que informar se houve uma mudança

$user_select = "SELECT titleId FROM user WHERE userId='$userId'";
$user_row= $pdo->query($challenge_select)->fetch();
$info['userTitle'] = $user_row['userTitle'];

$achievementId = (int) $challenge_info['achievementId'];
if ($achievementId != 0) {
	$archievement_select = "SELECT * FROM userxachievement WHERE userId='$userId' AND achievementId='$achievementId' AND challengeId='$challengeId'";
	$archievement_row = $pdo->query($archievement_select)->fetch();
	if($archievement_row != false) {
		$info['archievementId'] = $challenge_info['achievementId'];
	}
}

$output = new JsonOutput('true', 'Submission created.', $info);

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>