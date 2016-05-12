<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

$userId = null;
if(isset($_COOKIE['userinfos'])) {
	$data = unserialize($_COOKIE['userinfos']);
	$userId = $data['userId'];
}

$pdo = Database::connect();

//pego todos os challenges que o usuário tem 
$challenges_user = array();
if($userId != null) {
	$uc_select = "SELECT challengeId FROM userxchallenge WHERE userId=$userId";
	$query_uc_select = $pdo->query($uc_select);
	foreach ($query_uc_select as $row) {
		$challenges_user[] = $row['challengeId'];
	}
}

$sql_select= "SELECT * FROM challenge WHERE challengeStartDate <= CURDATE() AND CURDATE() <= challengeEndDate ORDER BY challengeEndDate ASC";
$res = $pdo->query($sql_select);
$v_challenge = array();

foreach ($res as $row) {
	$challengeId = (int) $row['challengeId'];

	$submissions = '0';
	if($userId != null) {
		$count_select = "SELECT COUNT(*) AS submissions FROM submission WHERE userId=$userId AND challengeId=$challengeId";
		$query = $pdo->query($count_select);
		$data = $query->fetch();
		$submissions = $data['submissions'];
	}

	$associate = 'false';
	if(in_array($challengeId, $challenges_user)) {
		$associate = 'true';
	}

 	$v_challenge[] = new Challenge(utf8_encode($row['challengeId']),
 								   utf8_encode($row['challengeName']),
 								   utf8_encode($row['challengeDetail']),
 								   utf8_encode($row['challengeStartDate']),
 								   utf8_encode($row['challengeEndDate']),
 								   utf8_encode($row['challengeNumberOfDishes']),
 								   utf8_encode($row['challengeMaxPoints']),
 								   utf8_encode($row['challengeMedia']),
 								   $associate,
 								   $submissions);
}

Database::disconnect();

$output = new JsonOutput(false, '' ,$v_challenge);

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);
?>