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
			$achievement = null;
			$sqlRetrieveAchievementsID = "
					SELECT achievement_id
					FROM achievement
					";
			$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievementsID);
			$retrieveAchievementsID->execute();
			$achievementsID = $retrieveAchievementsID->fetchColumn();
			echo json_encode($achievementsID);
			foreach($achievementsID as $value) {
				foreach()
					$achievement = $value;
					echo $achievement."\n";
				}
			} else {
			echo "You need to set household_id!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>