<?php

	include("../seguranca.php"); // Inclui o arquivo com o sistema de segurança
	protegePagina();

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
    		<div class="row">
    			<h3>BeChef - Achivements</h3>
    		</div>
			<div class="row">
				<p>
					<a href="create.php" class="btn btn-success">Create</a>
				</p>
				
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>Media</th>
		                  <th>Title</th>
		                  <th>Challenge</th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 
					   include '../../database.php';
					   //include '../databaselocal.php';

					   $pdo = Database::connect();
					   $sql = "SELECT * FROM achievement ORDER BY achievementId DESC";

	 				   foreach ($pdo->query($sql) as $row) {
		   				   		$sqlChallenge = "SELECT challengeName FROM challenge WHERE challengeId=" . $row['challengeId'] . " LIMIT 0, 1";
	 				   			$challengeName = $pdo->query($sqlChallenge)->fetch();
	 				   			echo $challengeName['challengeName'];
						   		echo '<tr>';
						   	    echo '<td>'. $row['achievementMedia'] . '</td>';
							   	echo '<td>'. $row['achievementTitle'] . '</td>';
							   	echo '<td><a class="btn" href="../challenge/read.php?id=' .$row['challengeId']. '">'. $challengeName['challengeName'] .'</a></td>';
							   	echo '<td width=250>';
							   	echo '<a class="btn" href="read.php?id='.$row['achievementId'].'">Read</a>';
							   	echo '&nbsp;';
							   	echo '<a class="btn btn-success" href="update.php?id='.$row['achievementId'].'">Update</a>';
							   	echo '&nbsp;';
							   	echo '<a class="btn btn-danger" href="delete.php?id='.$row['achievementId'].'">Delete</a>';
							   	echo '</td>';
							   	echo '</tr>';
					   }
					   Database::disconnect();
					   
					  ?>
				      </tbody>
	            </table>
    	</div>
    </div> <!-- /container -->
  </body>
</html>