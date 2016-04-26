<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		if (!empty($_GET["household_id"])) {
			//do something
		} else {
			echo "Household ID must be set!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>