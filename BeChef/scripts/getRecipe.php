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

if(!(isset($_POST['recipeId'])))
{
	$output = new JsonOutput('false', 'Invalid parameters', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$recipeId = (int) addslashes(strip_tags(trim($_POST['recipeId'])));

$pdo = Database::connect();

$recipe_select = "SELECT * FROM recipe WHERE recipe.recipeId='$recipeId' LIMIT 1 ";
$query = $pdo->query($recipe_select);

if(!($query && ($query->rowCount() > 0))) {
	$output = new JsonOutput('false', 'Invalid data 1', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return;
}

$recipe_data = $query->fetch();

$recipe = new Recipe(utf8_encode($recipe_data['recipeId']),
					 utf8_encode($recipe_data['recipeDishes']),
					 utf8_encode($recipe_data['recipeCookingTime']),
					 utf8_encode($recipe_data['recipeTitle']),
					 utf8_encode($recipe_data['recipeOrigin']));

$recipeId = (int) $recipe_data['recipeId'];
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

$submissionFull = new SubmissionFull(array(), $recipe, $ingredients, $steps);

$output = new JsonOutput('true', '', $submissionFull);

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo utf8_encode(json_encode($output));
	
?>