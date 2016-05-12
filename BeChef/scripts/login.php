<?php 

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";

if(!(isset($_POST['email']) &&
     isset($_POST['password']) &&
     filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)))
{
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$user = addslashes(strip_tags(trim($_POST['email'])));
$pass = utf8_decode(addslashes(strip_tags(trim($_POST['password']))));

$pdo = Database::connect();

$user_select = "SELECT user.*, title.titleText FROM user, title WHERE title.titleId=user.titleId AND userEmail='$user' AND userPassword='$pass' LIMIT 1";
$query_user = $pdo->query($user_select);
if(!$query_user) 
{
	$output = new JsonOutput('false', 'Try Later', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

if($query_user->rowCount() == 0){
	$output = new JsonOutput('false', 'Wrong email or password', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$data = $query_user->fetch(PDO::FETCH_ASSOC);
$userRes = new User( utf8_encode($data['userId']),
					 utf8_encode($data['userIdFacebook']),
					 utf8_encode($data['userEmail']),
					 utf8_encode($data['userName']),
					 utf8_encode($data['userMedia']),
					 utf8_encode($data['titleText']),
					 utf8_encode($data['userTotalPoints']));

$output = new JsonOutput('true', '', $userRes);

$_SESSION['userinfos'] = $data;
$resCookie = array( 'userId' => $data['userId'], 'userEmail' => $data['userEmail'], 
	'userName' => $data['userName'], 'userMedia' => $data['userMedia'] , 'userTitle' => $data['titleText']);
setcookie('userinfos', serialize($resCookie));

Database::disconnect();
header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode( $output );
?>