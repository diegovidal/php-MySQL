<?php 
include("../seguranca.php"); // Inclui o arquivo com o sistema de segurança
protegePagina();


	if ( !empty($_POST)) {
		// keep track validation errors
		$mediaError = null;
		$nameError = null;
		$detailError = null;
		$startDateError = null;
		$endDateError = null;
		$dishesError = null;
		$maxPointsError = null;
		
		// keep track post values
		$media = basename($_FILES["photo"]["name"]);
		$name = $_POST['name'];
		$detail = $_POST['detail'];
		$start = $_POST['start'];
		$end = $_POST['end'];
		$dishes = $_POST['dishes'];
		$points = $_POST['points'];
		
		// validate input
		$valid = true;
		if (empty($name)) {
			$nameError = 'Please enter Name';
			$valid = false;
		}
		if (empty($detail)) {
			$detailError = 'Please enter Detail';
			$valid = false;
		}
		if (empty($start)) {
			$startDateError = 'Please enter Start';
			$valid = false;
		}
		if (empty($end)) {
			$endDateError = 'Please enter End';
			$valid = false;
		}
		if (empty($dishes)) {
			$dishesError = 'Please enter Dishes';
			$valid = false;
		}
		if (empty($points)) {
			$maxPointsError = 'Please enter Points';
			$valid = false;
		}

		if (empty($media)) {
			$mediaError = 'Please enter Media';
			$valid = false;
		}
		
		// insert data
		if ($valid) {
			
			ob_start();
			require_once "../savePhoto.php";
			
			$grava_media = savePhoto();
			if($grava_media == '') {
				$grava_media = 'defaultUser.jpg';
			}
			
			require_once "../../database.php";	
			$pdo = Database::connect();
			//echo "connect";
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "INSERT INTO challenge (challengeMedia,challengeName,challengeDetail,challengeStartDate,challengeEndDate,challengeNumberOfDishes,challengeMaxPoints) values(?,?,?,?,?,?,?)";
			$q = $pdo->prepare($sql);
			//echo "prepare";
			$q->execute(array($grava_media, $name, $detail, $start, $end, $dishes, $points));
			//echo "execute";
			Database::disconnect();

			// Importa o arquivo
			require_once "../../push.php";

			// Define a mensagem
			$message = "O ".$name." acabou de ser criado, entre e confira!";

			// Manda o push
			sendPush($message);

			header("Location:index.php");
			ob_end_flush();
		}

	}

	
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <link   href="../css/bootstrap.min.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>

    <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script> 
    <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script> 

    <script> 
	    $(document).ready(function() { 
	    	$("#start").datepicker({
  				dateFormat: "yy-mm-dd"
			});
	    	$("#end").datepicker({
	    		dateFormat: "yy-mm-dd"
	    	}); 
	    }); 
    </script>
</head>

<body>
    <div class="container">
    
    			<div class="span10 offset1">
    				<div class="row">
		    			<h3>Create a Challenge</h3>
		    		</div>
	    			<form class="form-horizontal" action="create.php" method="post" enctype="multipart/form-data">
	    			<div class="control-group <?php echo !empty($mediaError)?'photo':'';?>">
					    <label class="control-label">Media</label>
					    <div class="controls">
					      	<input type="file" name="photo" value="<?php echo !empty($media)?$media:'';?>" id="photo">
					      	<?php if (!empty($mediaError)): ?>
					      		<span class="help-inline"><?php echo $mediaError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($nameError)?'error':'';?>">
					    <label class="control-label">Name</label>
					    <div class="controls">
					      	<input name="name" type="text"  placeholder="Name" value="<?php echo !empty($name)?$name:'';?>">
					      	<?php if (!empty($nameError)): ?>
					      		<span class="help-inline"><?php echo $nameError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($detailError)?'error':'';?>">
					    <label class="control-label">Detail</label>
					    <div class="controls">
					      	<input name="detail" type="text" placeholder="Detail" value="<?php echo !empty($detail)?$detail:'';?>">
					      	<?php if (!empty($detailError)): ?>
					      		<span class="help-inline"><?php echo $detailError;?></span>
					      	<?php endif;?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($startDateError)?'error':'';?>">
					    <label class="control-label">Start Date</label>
					    <div class="controls">
					      	<input id="start" name="start" type="text"  placeholder="Stard Date" value="<?php echo !empty($start)?$start:'';?>">
					      	<?php if (!empty($startDateError)): ?>
					      		<span class="help-inline"><?php echo $startDateError;?></span>
					      	<?php endif;?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($endDateError)?'error':'';?>">
					    <label class="control-label">End Date</label>
					    <div class="controls">
					      	<input id="end" name="end" type="text"  placeholder="End Date" value="<?php echo !empty($end)?$end:'';?>">
					      	<?php if (!empty($endDateError)): ?>
					      		<span class="help-inline"><?php echo $endDateError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($dishesError)?'error':'';?>">
					    <label class="control-label">Number of dishes</label>
					    <div class="controls">
					      	<input name="dishes" type="text"  placeholder="Dishes" value="<?php echo !empty($dishes)?$dishes:'';?>">
					      	<?php if (!empty($dishesError)): ?>
					      		<span class="help-inline"><?php echo $dishesError;?></span>
					      	<?php endif; ?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($maxPointsError)?'error':'';?>">
					    <label class="control-label">Max Points</label>
					    <div class="controls">
					      	<input name="points" type="text"  placeholder="Points" value="<?php echo !empty($points)?$points:'';?>">
					      	<?php if (!empty($maxPointsError)): ?>
					      		<span class="help-inline"><?php echo $maxPointsError;?></span>
					      	<?php endif; ?>
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