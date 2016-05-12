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
	}
	
	if ( !empty($_POST)) {

		// keep track validation errors
		$mediaError = null;
		$titleError = null;
		$challengelError = null;
		
		// keep track post values
		$media = $_POST['media'];
		$title = $_POST['title'];
		$challenge = $_POST['challenge'];
		
		// validate input
		$valid = true;
		if (empty($title)) {
			$titleError = 'Please enter Title';
			$valid = false;
		}
		if (empty($challenge)) {
			$challengeError = 'Please enter Challenge';
			$valid = false;
		}
		if (empty($media)) {
			$mediaError = 'Please enter Media';
			$valid = false;
		}
		
		// update data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE achievement set achievementMedia = ?, achievementTitle = ?, challengeId =? WHERE achievementId= ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($media, $title, $challenge, $id));
			Database::disconnect();
			header("Location: index.php");
		}
	} else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM achievement WHERE achievementId = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);

		$media = $data['achievementMedia'];
		$title = $data['achievementTitle'];
		$challengeId = $data['challengeId'];
		

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
		    			<h3>Update a Customer</h3>
		    		</div>
    		
	    			<form class="form-horizontal" action="update.php?id=<?php echo $id?>" method="post">
					  <div class="control-group <?php echo !empty($mediaError)?'error':'';?>">
					    <label class="control-label">Media</label>
					    <div class="controls">
					      	<input name="media" type="text"  placeholder="URL" value="<?php echo !empty($media)?$media:'';?>">
					      	<?php if (!empty($mediaError)): ?>
					      		<span class="help-inline"><?php echo $mediaError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($titleError)?'error':'';?>">
					    <label class="control-label">Title</label>
					    <div class="controls">
					      	<input name="title" type="text"  placeholder="title" value="<?php echo !empty($title)?$title:'';?>">
					      	<?php if (!empty($titleError)): ?>
					      		<span class="help-inline"><?php echo $titleError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($challengeError)?'error':'';?>">
					    <label class="control-label">Challenge Id</label>
					    <div class="controls">
					      	<input name="challenge" type="text" placeholder="challengeId" value="<?php echo !empty($challengeId)?$challengeId:'';?>">
					      	<?php if (!empty($challengeError)): ?>
					      		<span class="help-inline"><?php echo $challengeError;?></span>
					      	<?php endif;?>
					    </div>
					  </div>
					  
					  <div class="form-actions">
						  <button type="submit" class="btn btn-success">Update</button>
						  <a class="btn" href="index.php">Back</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>