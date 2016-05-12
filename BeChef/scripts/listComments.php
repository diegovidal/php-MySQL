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

if( !(isset($_POST['submissionId']) &&
	  isset($_POST['start']) &&
      isset($_POST['quantity']))) {
	$output = new JsonOutput('false', 'Invalid parameter.','');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}

$start = addslashes(strip_tags(trim($_POST['start'])));
$quantity = addslashes(strip_tags(trim($_POST['quantity'])));
$submissionId = addslashes(strip_tags(trim($_POST['submissionId'])));

$pdo = Database::connect();

$sql = "SELECT user.userId, comment.commentId, comment.commentDate, comment.commentText, user.userName, user.userMedia FROM comment,user WHERE user.userId=comment.userId AND comment.submissionId='$submissionId' AND comment.commentDate<'$start' ORDER BY comment.commentDate DESC LIMIT $quantity";
if(isset($userId)) {
	$sql = "SELECT u.userId, c.commentId, c.commentDate, c.commentText, u.userName, u.userMedia FROM comment c, user u WHERE u.userId=c.userId AND c.submissionId='$submissionId' AND c.commentDate<'$start' AND c.commentId NOT IN ( SELECT cr.commentId FROM commentreport cr WHERE cr.commentId = c.commentId AND userId='$userId') ORDER BY c.commentDate DESC LIMIT $quantity";
}

$res = $pdo->query($sql);

$v_comments = array();

foreach ($res as $row) {
	$v_comments[] = new Comment(utf8_encode($row['userId']),
								utf8_encode($row['userName']),
							    utf8_encode($row['userMedia']),
							    utf8_encode($row['commentId']),
							    utf8_encode($row['commentText']),
							    utf8_encode($row['commentDate']));
}

$output = new JsonOutput('true', '', $v_comments);
// Desconecta
Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>