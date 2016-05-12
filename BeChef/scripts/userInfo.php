<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

if( !(isset($_POST['userId'])) ) {
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}	

$userId = addslashes(strip_tags(trim($_POST['userId'])));
    
$pdo = Database::connect();

$sql_select = "SELECT user.*, title.titleText FROM user, title WHERE title.titleId=user.titleId AND userId='$userId'";
$query_select = $pdo->query($sql_select);

$output = new JsonOutput('false', '', '');

if(!($query_select)) {
	$output = new JsonOutput('false', 'Invalid parameters 2', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$data = $query_select->fetch(PDO::FETCH_ASSOC);
$userRes = new User( utf8_encode($data['userId']),
				 utf8_encode($data['userIdFacebook']),
				 utf8_encode($data['userEmail']),
				 utf8_encode($data['userName']),
				 utf8_encode($data['userMedia']),
				 utf8_encode($data['titleText']),
				 utf8_encode($data['userTotalPoints']));

$output = new JsonOutput('true', '', $userRes);

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>