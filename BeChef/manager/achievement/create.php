<?php 

	include("../seguranca.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina();
	
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
		
		// insert data
		if ($valid) {
			include '../../database.php';
			//include '../databaselocal.php';

			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO achievement (achievementMedia,challengeId, achievementTitle) values(?,?,?)";
			$q = $pdo->prepare($sql);
			$q->execute(array($media, $challenge, $title));
			Database::disconnect();
			header("Location:index.php");
		}
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
		    			<h3>Create a Achievement</h3>
		    		</div>
	    			<form class="form-horizontal" action="create.php" method="post">
	    				<div class="control-group <?php echo !empty($mediaError)?'media':'';?>">
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
					      		<input name="title" type="text"  placeholder="Title" value="<?php echo !empty($title)?$title:'';?>">
					      		<?php if (!empty($titleError)): ?>
					      			<span class="help-inline"><?php echo $titleError;?></span>
					      		<?php endif; ?>
					    	</div>
					  	</div>
					  	<div class="control-group <?php echo !empty($challengeError)?'error':'';?>">
					    	<label class="control-label">Challenge</label>
					    	<div class="controls">
					      		<input name="challenge" type="text" placeholder="Challenge" value="<?php echo !empty($challenge)?$challenge:'';?>">
					      		<?php if (!empty($challengeError)): ?>
					      			<span class="help-inline"><?php echo $challengeError;?></span>
					      		<?php endif;?>
					    	</div>
					  	</div>
					  	<div class="form-actions">
						 	<button type="submit" class="btn btn-success">Create</button>
						    <a class="btn" href="index.php">Back</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>