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

if(!(isset($_POST['userId']) &&
	 isset($_POST['start']) &&
     isset($_POST['quantity'])))
{
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$userId = addslashes(strip_tags(trim($_POST['userId'])));
$start = addslashes(strip_tags(trim($_POST['start'])));
$quantity = addslashes(strip_tags(trim($_POST['quantity'])));

$pdo = Database::connect();
  
$submission_select = "SELECT submissionId, submissionDetail, submissionMedia, submissionPoints, submission.userId, challengeId, recipeId, submissionDate, submissionLikes, userMedia, userName FROM submission, user WHERE submission.userId=user.userId AND submission.userId=$userId AND submission.submissionDate<'$start' ORDER BY submission.submissionDate DESC LIMIT $quantity";


$submissions = $pdo->query($submission_select);
$v_submissions = array();

foreach ($submissions as $row) {
	$liked = 'false';
	if($loggedUserId != null) {
		$id = (int) $row['submissionId'];
		$like_select = "SELECT * FROM s_like WHERE userId=$loggedUserId AND submissionId=$id";
		$like = $pdo->query($like_select);
		$liked = ($like->rowCount() > 0)?'true':'false';
	}

 	$v_submissions[] = new Submission(utf8_encode($row['submissionId']),
 									  utf8_encode($row['submissionDetail']),
 									  utf8_encode($row['submissionMedia']),
 									  utf8_encode($row['submissionPoints']),
 									  utf8_encode($row['userId']),
 									  utf8_encode($row['challengeId']),
 									  utf8_encode($row['recipeId']),
 									  utf8_encode($row['submissionDate']),
 									  $liked,
 									  utf8_encode($row['submissionLikes']),
 									  utf8_encode($row['userName']),
 									  utf8_encode($row['userMedia']));					   	
}

$output = new JsonOutput('true', '', $v_submissions);

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo utf8_encode(json_encode($output));
?>