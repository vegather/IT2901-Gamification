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
		if (isset($_GET["household_id"])) {
			$sqlRetrieveAchievementsID = "
					SELECT achievement_id
					FROM achievement
					WHERE achievement_id > :achievement_id
					";
			$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievements);
			$retrieveAchievementsID->bindParam(":achievement_id", $requiredForFetching = -1, PDO::PARAM_INT);
			$retrieveAchievementsID->execute();
			$achievementsID = $retrieveAchievementsID->fetchAll(PDO::FETCH_NUM);
			echo json_encode($achievementsID);
			} else {
			echo "You need to set household_id!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>