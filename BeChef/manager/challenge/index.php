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
    			<h3>BeChef - Challenges</h3>
    		</div>
			<div class="row">
				<p>
					<a href="create.php" class="btn btn-success">Create</a>
				</p>
				
				<table class="table table-striped table-bordered">
		              <thead>
		                <tr>
		                  <th>Media</th>
		                  <th>Name</th>
		                  <th>Detail</th>
		                  <th>Start Date</th>
		                  <th>End Date</th>
		                  <th>Dishes</th>
		                  <th>Max Points</th>
		                </tr>
		              </thead>
		              <tbody>
		              <?php 

					  include '../../database.php';

					   $pdo = Database::connect();
					   $sql = "SELECT * FROM challenge ORDER BY challengeId DESC";
	 				   foreach ($pdo->query($sql) as $row) {
						   		echo '<tr>';
						   	    echo '<td>'. $row['challengeMedia'] . '</td>';
							   	echo '<td>'. $row['challengeName'] . '</td>';
							   	echo '<td>'. $row['challengeDetail'] . '</td>';
							   	echo '<td>'. $row['challengeStartDate'] . '</td>';
							   	echo '<td>'. $row['challengeEndDate'] . '</td>';
							   	echo '<td>'. $row['challengeNumberOfDishes'] . '</td>';
							   	echo '<td>'. $row['challengeMaxPoints'] . '</td>';
							   	echo '<td width=250>';
							   	echo '<a class="btn" href="read.php?id='.$row['challengeId'].'">Read</a>';
							   	echo '&nbsp;';
							   	echo '<a class="btn btn-success" href="update.php?id='.$row['challengeId'].'">Update</a>';
							   	echo '&nbsp;';
							   	echo '<a class="btn btn-danger" href="delete.php?id='.$row['challengeId'].'">Delete</a>';
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