<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

if(!isset($_COOKIE['userinfos'])) {
	$output = new JsonOutput('false', 'do login.', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

if( !(isset($_POST['submissionId'])) ) {
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}	

$data = unserialize($_COOKIE['userinfos']);
$userId = $data['userId'];
$submissionId = addslashes(strip_tags(trim($_POST['submissionId'])));
    
$pdo = Database::connect();

$sql_select = "SELECT * FROM s_like WHERE userId=$userId AND submissionId=$submissionId";
$query_select = $pdo->query($sql_select);

$output = new JsonOutput('false', '', '');

if($query_select && ($query_select->rowCount() == 0))
{
	$sql = "INSERT INTO s_like (userId, submissionId) VALUES ($userId,$submissionId)";
	$query = $pdo->query($sql);

	if($query && ($query->rowCount() > 0)) {
		$output = new JsonOutput('true', 'Liked', '');
	}else{
		$output = new JsonOutput('false', 'Try Later1', '');
	}
}else{
	$sql = "DELETE FROM s_like WHERE userId=$userId AND submissionId=$submissionId";
	$query = $pdo->query($sql);

	if($query && ($query->rowCount() > 0)) {
		$output = new JsonOutput('true', 'Unliked', '');
	}else{
		$output = new JsonOutput('false', 'Try Later2', '');
	}
}		

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>