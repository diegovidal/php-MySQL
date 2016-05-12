<?php 

	require_once("../seguranca.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina();

	require '../../database.php';
	//require '../databaselocal.php';

	$id = null;
	if ( !empty($_GET['id'])) {
		$id = $_REQUEST['id'];
	}
	
	if ( null==$id ) {
		header("Location: index.php");
	} else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM challenge where challengeId = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);

		Database::disconnect();
	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link   href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
    
    			<div class="span10 offset1">
    				<div class="row">
		    			<h3>Read a Challenge</h3>
		    		</div>
		    		
	    			<div class="form-horizontal" >
					  <div class="control-group">
					    <label class="control-label">Media</label>
					    <div class="controls">
						    <label class="checkbox">
						     	<?php echo $data['challengeMedia'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Name</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeName'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Detail</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeDetail'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Start Date</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeStartDate'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">End Date</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeEndDate'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Number of Dishes</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeNumberOfDishes'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Max Points</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['challengeMaxPoints'];?>
						    </label>
					    </div>
					  </div>
					    <div class="form-actions">
						  <a class="btn" href="index.php">Back</a>
					   </div>
					
					 
					</div>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>