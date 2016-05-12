<?php

function __autoload($class_name) {
    require_once 'classes/' . $class_name . '.php';
}

require_once "../database.php";
require_once "savePhoto.php";

if(!isset($_COOKIE['userinfos'])) {
	$output = new JsonOutput('false', 'do login.', '');
 	header('Cache-Control: no-cache, must-revalidate');
 	header("content-type:application/json");
 	echo json_encode($output);
 	return ;
}

if(!isset($_POST['json'])) {    
	$output = new JsonOutput('false', 'Invalid parameter', '');
	header('Cache-Control: no-cache, must-revalidate');
	header("content-type:application/json");
	echo json_encode($output);
	return ;
}	

/*
$json ='{
    "recipe": {
        "title": "Lasanha",
        "dishes": "3",
        "cookingTime": "30",
        "origin": "Brasil"
    },
    "ingredients": [{
        "name": "Queijo",
        "quantity": "300",
        "measure": "g"
    },
    {
        "name": "Presunto",
        "quantity": "100",
        "measure": "g"
    }],
    "steps": [{
        "detail": "Coloque na lasanha",
        "stepPhoto": "queijo.jpg"
    },
    {
        "detail": "Coloque na lasanha",
        "stepPhoto": "Coloque na lasanha"
    }]
}';*/

$jsonReceived = json_decode($_POST['json']);

$pdo = Database::connect();
// -> Insert Into da receita - tabela: recipe

$recipeTitle = utf8_decode(addslashes(strip_tags(trim($jsonReceived->recipe->title))));
$recipeDishes = utf8_decode(addslashes(strip_tags(trim($jsonReceived->recipe->dishes))));
$recipeCookingTime = utf8_decode(addslashes(strip_tags(trim($jsonReceived->recipe->cookingTime))));
$recipeOrigin = utf8_decode(addslashes(strip_tags(trim($jsonReceived->recipe->origin))));

// Transação para garantir a consistência dos dados
$pdo->beginTransaction();

$sql = "INSERT INTO recipe (recipeDishes, recipeCookingTime, recipeTitle, recipeOrigin) VALUES ('$recipeDishes','$recipeCookingTime','$recipeTitle','$recipeOrigin')";
$q = $pdo->query($sql);

$sql = "SELECT * FROM recipe WHERE recipeId = (SELECT MAX(recipeId) FROM recipe)";
$q = $pdo->query($sql);

$pdo->commit();

$data = $q->fetch(PDO::FETCH_ASSOC);
$recipeId = $data['recipeId'];


// -> Insert Into dos ingredients - obs: só adiciona na tabela ingredient se não existir, sempre adiciona na recipexingredient

// Verifica se já existe o ingredient cadastrado

$numberOfIngredients = count($jsonReceived->ingredients);

for ($i=0; $i < $numberOfIngredients; $i++) {

    $ingredientName = utf8_decode(addslashes(strip_tags(trim($jsonReceived->ingredients[$i]->name))));
    $ingredientQuantity = utf8_decode(addslashes(strip_tags(trim($jsonReceived->ingredients[$i]->quantity))));
    $ingredientMeasure = utf8_decode(addslashes(strip_tags(trim($jsonReceived->ingredients[$i]->measure))));

    $sql = "SELECT * FROM ingredient WHERE ingredientName='$ingredientName'";
    $q = $pdo->query($sql);
    

    // Se não existir na tabela ingredient
    if($q->rowCount() == 0) {
   
        $sql = "INSERT INTO ingredient (ingredientName) VALUES ('$ingredientName')";
        $q = $pdo->query($sql);

        $sql = "SELECT * FROM ingredient WHERE ingredientName = '$ingredientName'";
        $q = $pdo->query($sql);

    }

    $data = $q->fetch(PDO::FETCH_ASSOC);
    $ingredientId = $data['ingredientId'];

    $sql = "INSERT INTO recipexingredient (recipeId, ingredientId, quantity, measure) VALUES ('$recipeId', '$ingredientId', '$ingredientQuantity', '$ingredientMeasure')";
    $q = $pdo->query($sql);

}

// -> Insert Into dos steps - tabela: step

$steps = $jsonReceived->steps;
$numberOfSteps = count($jsonReceived->steps);

// Salva as fotos e retorna uma array com os respectivos nomes das fotos
$arrayPhotos = saveMultiplePhotos($steps);

for ($i=0; $i < $numberOfSteps; $i++) { 

    $stepMedia = utf8_decode($arrayPhotos[$i]);
    //$stepMedia = $jsonReceived->steps[$i]->stepPhoto;
    $stepDetail = utf8_decode($jsonReceived->steps[$i]->detail);

    // Insere o step no banco de dados
    $sql = "INSERT INTO step (recipeId, stepMedia, stepDetail) VALUES ('$recipeId','$stepMedia','$stepDetail')";
    $q = $pdo->query($sql);

}

// Se deu certo retorna o id da receita criada
$output = new JsonOutput('true', $recipeId,'');

Database::disconnect();

header('Cache-Control: no-cache, must-revalidate');
header("content-type:application/json");
echo json_encode($output);

?>