<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		if (isset($_GET["start_date"], $_GET["end_date"])) {
			$sqlRetrieveLeaderboards = "";
			$retrieveLeaderboards = dbh->prepare($sqlRetrieveLeaderboards);
			$retrieveLeaderboards->bindParam(':startDate', $_GET["start_date"], PDO::PARAM_STR);
			$retrieveLeaderboards->bindParam(':endDate', $_GET["end_date"], PDO::PARAM_STR);
		} else {
			echo "Need the start date and end date to retrieve leaderboards!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>