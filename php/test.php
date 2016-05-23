<?php
	//Fetches connection information from the config.ini file then sets the connection variables
	$iniArray = parse_ini_file("/var/www/html/config.ini", true);
	$hostname = $iniArray["connectionInfo"]["hostname"];
	$username = $iniArray["connectionInfo"]["username"];
	$password = $iniArray["connectionInfo"]["password"];
	$database = $iniArray["connectionInfo"]["database"];
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		//Check if household_id has been set as a parameter
		if (isset($_GET["household_id"]) && !empty("username")) {
			$household_id = $_POST["household_id"];
			$householdUsername = $_POST["username"];
			
			//Check to see if household_id is available
			$sqlCheckIDAvailability = "
				SELECT COUNT(*)
				FROM household
				WHERE household_id = :household_id
				LIMIT 1
				";
			$checkIDAvailability = $dbh->prepare($sqlCheckIDAvailability);
			$checkIDAvailability->bindParam(':household_id', $household_id, PDO::PARAM_STR);
			$checkIDAvailability->execute();
			
			
			//Check to see if username is available
			$sqlCheckUsernameAvailability = "
				SELECT COUNT(*)
				FROM household
				WHERE username = :username
				LIMIT 1
				";
			$checkUsernameAvailability = $dbh->prepare($sqlCheckUsernameAvailability);
			$checkUsernameAvailability->bindParam(':username', $householdUsername, PDO::PARAM_STR);
			$checkUsernameAvailability->execute();
			
			
			//If username is available start setting up household in database
			if (!($checkUsernameAvailability->fetchColumn()) && !($checkUsernameAvailability->fetchColumn())) {
				echo "Available!";
			} else {
				echo "Taken!";
			}
		} else {
			echo "You need to set household_id & username!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>