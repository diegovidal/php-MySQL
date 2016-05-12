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

if( !isset($_POST['challengeId']) ) {
	$output = new JsonOutput('false', 'Invalid parameter.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$challengeId = addslashes(strip_tags(trim($_POST['challengeId'])));

$pdo = Database::connect();

$sql = "SELECT user.*, title.titleText, SUM(submissionLikes)+SUM(submissionPoints) AS TOTAL FROM userxchallenge, submission, user, title WHERE user.titleId=title.titleId AND userxchallenge.userId=submission.userId AND userxchallenge.challengeId=submission.challengeId AND user.userId=submission.userId AND userxchallenge.challengeId=$challengeId GROUP BY user.userId ORDER BY TOTAL DESC";

$res = $pdo->query($sql);
$v_users = array();

// Definição da array
foreach ($res as $row) {
	$v_users[] = new User(utf8_encode($row['userId']),
						  utf8_encode($row['userIdFacebook']),
						  utf8_encode($row['userEmail']),
						  utf8_encode($row['userName']),
						  utf8_encode($row['userMedia']),
						  utf8_encode($row['titleText']),
						  utf8_encode($row['TOTAL']));
}

$output = new JsonOutput('true', '', $v_users);
// Desconecta
Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>