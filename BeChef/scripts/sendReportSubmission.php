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

if(!( isset($_POST['reportText']) &&
      isset($_POST['submissionId']))) {
	$output = new JsonOutput('false', 'Invalid parameter.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$data = unserialize($_COOKIE['userinfos']);
$userId = $data['userId'];
$submissionId = addslashes(strip_tags(trim($_POST['submissionId'])));
$submissionreportDesc = utf8_decode(addslashes(strip_tags(trim($_POST['reportText']))));

$pdo = Database::connect();
//Verificar se existe uma submissão feita pelo usuário
$sql = "SELECT * FROM submissionreport WHERE userId='$userId' AND submissionId='$submissionId'";
$q = $pdo->query($sql);

if(!($q && ($q->rowCount() == 0))) {
	$output = new JsonOutput('false', 'Report already created.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$sql = "INSERT INTO submissionreport (submissionId, submissionreportText, userId) VALUES ('$submissionId','$submissionreportDesc','$userId')";
$query = $pdo->query($sql);
if(!($query && $query->rowCount() > 0)) {
	$output = new JsonOutput('false', 'Try Later.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$output = new JsonOutput('true', 'Report created', '');

// Desconecta
Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>