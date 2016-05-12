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
		$nameError = null;
		$detailError = null;
		$startDateError = null;
		$endDateError = null;
		$dishesError = null;
		$maxPointsError = null;
		
		// keep track post values
		$media = basename($_FILES['photo']['name']);
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
		// if (empty($media)) {
		// 	$mediaError = 'Please enter Media';
		// 	$valid = false;
		//}
		// update data
		if ($valid) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

			require_once "../savePhoto.php";

			// Verifica a foto
			if (empty($media)) {
				//echo "Entrou no if";
				$sql = "UPDATE challenge set challengeName = ?, challengeDetail =?, challengeStartDate = ?, challengeEndDate = ?, challengeNumberOfDishes =?, challengeMaxPoints=? WHERE challengeId = ?";
				$q = $pdo->prepare($sql);
				$q->execute(array($name, $detail, $start, $end, $dishes, $points, $id));
			}
			else{

				$mediaSent = savePhoto();
				$sql = "UPDATE challenge set challengeMedia = ?, challengeName = ?, challengeDetail =?, challengeStartDate = ?, challengeEndDate = ?, challengeNumberOfDishes =?, challengeMaxPoints=? WHERE challengeId = ?";
				$q = $pdo->prepare($sql);
				$q->execute(array($mediaSent, $name, $detail, $start, $end, $dishes, $points, $id));			
			}
			
			Database::disconnect();
			header("Location: index.php");
		}
	} 
	else {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "SELECT * FROM challenge WHERE challengeId = ?";
		$q = $pdo->prepare($sql);
		$q->execute(array($id));
		$data = $q->fetch(PDO::FETCH_ASSOC);

		$media = $data['challengeMedia'];
		$name = $data['challengeName'];
		$detail = $data['challengeDetail'];
		$start = $data['challengeStartDate'];
		$end = $data['challengeEndDate'];
		$dishes = $data['challengeNumberOfDishes'];
		$points = $data['challengeMaxPoints'];

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
		    			<h3>Update a Challenge</h3>
		    		</div>
	    			<form class="form-horizontal" action="update.php?id=<?php echo $id?>" method="post" enctype="multipart/form-data">
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
					      	<input name="start" type="text"  placeholder="Stard Date" value="<?php echo !empty($start)?$start:'';?>">
					      	<?php if (!empty($startDateError)): ?>
					      		<span class="help-inline"><?php echo $startDateError;?></span>
					      	<?php endif;?>
					    </div>
					  </div>
					  <div class="control-group <?php echo !empty($endDateError)?'error':'';?>">
					    <label class="control-label">End Date</label>
					    <div class="controls">
					      	<input name="end" type="text"  placeholder="End Date" value="<?php echo !empty($end)?$end:'';?>">
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
						  <button type="submit" class="btn btn-success">Update</button>
						  <a class="btn" href="index.php">Back</a>
						</div>
					</form>
				</div>
				
    </div> <!-- /container -->
  </body>
</html>