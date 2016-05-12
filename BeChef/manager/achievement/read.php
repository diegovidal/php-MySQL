<?php 
	
	include("../seguranca.php"); // Inclui o arquivo com o sistema de segurança
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
		$sql = "SELECT * FROM achievement where achievementId = ?";
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
		    			<h3>Read a Achievement</h3>
		    		</div>
		    		
	    			<div class="form-horizontal" >
					  <div class="control-group">
					    <label class="control-label">Media</label>
					    <div class="controls">
						    <label class="checkbox">
						     	<?php echo $data['achievementMedia'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Title</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php echo $data['achievementTitle'];?>
						    </label>
					    </div>
					  </div>
					  <div class="control-group">
					    <label class="control-label">Challenge</label>
					    <div class="controls">
					      	<label class="checkbox">
						     	<?php 
						     		$sqlChallenge = "SELECT challengeName FROM challenge WHERE challengeId=" . $data['challengeId'] . " LIMIT 0, 1";
	 				   				$challengeName = $pdo->query($sqlChallenge)->fetch();
	 				   				echo $challengeName['challengeName'];
	 				   			?>
						    </label>
					    </div>
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