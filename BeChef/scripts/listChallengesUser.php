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

$userId = addslashes(strip_tags(trim($_POST['userId'])));

$pdo = Database::connect();

$sql_select= "SELECT challenge.* FROM challenge, userxchallenge WHERE userxchallenge.challengeId=challenge.challengeId AND userxchallenge.userId='$userId' ORDER BY challengeEndDate ASC";
$res = $pdo->query($sql_select);
$v_challenge = array();

foreach ($res as $row) {
	$challengeId = (int) $row['challengeId'];

	$submissions = '0';
	if($loggedUserId != null) {
		$count_select = "SELECT COUNT(*) AS submissions FROM submission WHERE userId=$loggedUserId AND challengeId=$challengeId";
		$query = $pdo->query($count_select);
		$data = $query->fetch();
		$submissions = $data['submissions'];
	}

 	$v_challenge[] = new Challenge(utf8_encode($row['challengeId']),
 								   utf8_encode($row['challengeName']),
 								   utf8_encode($row['challengeDetail']),
 								   utf8_encode($row['challengeStartDate']),
 								   utf8_encode($row['challengeEndDate']),
 								   utf8_encode($row['challengeNumberOfDishes']),
 								   utf8_encode($row['challengeMaxPoints']),
 								   utf8_encode($row['challengeMedia']),
 								   'true',
 								   $submissions);
}

Database::disconnect();

$output = new JsonOutput(false, '' ,$v_challenge);

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);
?>