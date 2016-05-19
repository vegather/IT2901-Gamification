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
			//MySQL for retrieving all the achievements for the household_id in question
			$sqlRetrieveHouseholdAchievements = "
			SELECT A.achievement_id, A.achievement_name, A.description, HA.achieved
			FROM achievement as A
			INNER JOIN household_achievements AS HA ON A.achievement_id = HA.achievement_achievement_id
			WHERE HA.household_household_id = :household_id
			ORDER BY HA.date_achieved IS NULL ASC
			";
			$retrieveHouseholdAchievements = $dbh->prepare($sqlRetrieveHouseholdAchievements);
			$retrieveHouseholdAchievements->bindParam(":household_id", $_GET["household_id"], PDO::PARAM_STR);
			$retrieveHouseholdAchievements->execute();
			$householdAchievements = $retrieveHouseholdAchievements->fetchAll(PDO::FETCH_ASSOC);
			
			$jsonHouseholdAchievements = json_encode($householdAchievements);
			
			if (isset($_GET["callback"])) {
				$callback = $_GET["callback"];
				echo $callback.'({"data":'.$jsonHouseholdAchievements.'});';
			} else {
				echo $jsonHouseholdAchievements;
			}
		} else {
			echo "You need to set household_id to the household you want the achievements from!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>