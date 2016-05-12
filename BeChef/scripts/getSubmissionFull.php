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

if(!(isset($_POST['submissionId'])))
{
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$submissionId = (int) addslashes(strip_tags(trim($_POST['submissionId'])));

$pdo = Database::connect();

$submission_select = "SELECT submission.*, userName, userMedia, recipeTitle, recipeDishes, recipeCookingTime, recipeOrigin FROM submission, recipe, user WHERE user.userId=submission.userId AND submission.recipeId=recipe.recipeId AND submission.submissionId='$submissionId' LIMIT 1 ";
$query = $pdo->query($submission_select);

if(!($query && ($query->rowCount() > 0))) {
	$output = new JsonOutput('false', 'Invalid data 1', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$submission_data = $query->fetch();

$liked = 'false';
if($userId != null) {
	$id = (int) $submission_data['submissionId'];
	$like_select = "SELECT * FROM s_like WHERE userId=$userId AND submissionId=$id";
	$like = $pdo->query($like_select);
	$liked = ($like->rowCount() > 0)?'true':'false';
}

$submission = new Submission(utf8_encode($submission_data['submissionId']),
							 utf8_encode($submission_data['submissionDetail']),
							 utf8_encode($submission_data['submissionMedia']),
							 utf8_encode($submission_data['submissionPoints']),
							 utf8_encode($submission_data['userId']),
							 utf8_encode($submission_data['challengeId']),
							 utf8_encode($submission_data['recipeId']),
							 utf8_encode($submission_data['submissionDate']),
							 $liked,
							 utf8_encode($submission_data['submissionLikes']),
							 utf8_encode($submission_data['userName']),
							 utf8_encode($submission_data['userMedia']));

$recipe = new Recipe(utf8_encode($submission_data['recipeId']),
					 utf8_encode($submission_data['recipeDishes']),
					 utf8_encode($submission_data['recipeCookingTime']),
					 utf8_encode($submission_data['recipeTitle']),
					 utf8_encode($submission_data['recipeOrigin']));

$recipeId = (int) $submission_data['recipeId'];
$ingredients_select = "SELECT ingredient.ingredientId, ingredientName, quantity, measure FROM ingredient, recipexingredient WHERE ingredient.ingredientId=recipexingredient.ingredientId AND recipexingredient.recipeId='$recipeId'";

$query = $pdo->query($ingredients_select);

if(!($query && ($query->rowCount() > 0))) {
	$output = new JsonOutput('false', 'Invalid data 2', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$ingredients = array();
foreach ($query as $row) {
	$ingredients[] =  new Ingredient(utf8_encode($row['ingredientId']),
									 utf8_encode($row['ingredientName']),
									 utf8_encode($row['quantity']),
									 utf8_encode($row['measure']));
}

$steps_select = "SELECT stepId, stepDetail, stepMedia FROM step WHERE recipeId='$recipeId' ORDER BY stepId ASC";

$query = $pdo->query($steps_select);

if(!($query && ($query->rowCount() > 0))) {
	$output = new JsonOutput('false', 'Invalid data 3', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$steps = array();
foreach ($query as $row) {
	$steps[] =  new Step(utf8_encode($row['stepId']),
						 utf8_encode($row['stepDetail']),
						 utf8_encode($row['stepMedia']));
}

$submissionFull = new SubmissionFull($submission, $recipe, $ingredients, $steps);

$output = new JsonOutput('true', '', $submissionFull);

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo utf8_encode(json_encode($output));
	
?>