<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

if(!isset($_COOKIE['userinfos'])) {
	$output = new JsonOutput('false', 'Do login.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

if(!(isset($_POST['commentText']) &&
      isset($_POST['submissionId']))) {
	$output = new JsonOutput('false', 'Invalid parameter.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$commentText = utf8_decode(addslashes(strip_tags(trim($_POST['commentText']))));
$submissionId = addslashes(strip_tags(trim($_POST['submissionId'])));
$data = unserialize($_COOKIE['userinfos']);
$userId = $data['userId'];

$pdo = Database::connect();

$sql = "INSERT INTO comment (commentText, userId, submissionId) VALUES ('$commentText','$userId','$submissionId')";
$query = $pdo->query($sql);
$dataTest = $query->fetch(PDO::FETCH_ASSOC);

if($query && ($query->rowCount() > 0)) {
	$output = new JsonOutput('true', '','');
}else {
	$output = new JsonOutput('false', 'Try Later', '');
}

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode( $output);

?>