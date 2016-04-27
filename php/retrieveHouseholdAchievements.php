<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		if (isset($_GET["household_id"])) {
			$sqlRetrieveHouseholdAchievements = "
			SELECT A.achievement_image, A.achievement_name, A.description, HA.achieved
			FROM achievement as A
			INNER JOIN household_achievements AS HA ON A.achievement_id = HA.achievement_achievement_id
			WHERE HA.household_household_id = :household_id
			GROUP BY A.achievement_name
			";
			$retrieveHouseholdAchievements = $dbh->prepare($sqlRetrieveHouseholdAchievements);
			$retrieveHouseholdAchievements->bindParam(":household_id", $_GET["household_id"], PDO::PARAM_STR);
			$retrieveHouseholdAchievements->execute();
			$householdAchievements = $retrieveHouseholdAchievements->fetchAll(PDO::FETCH_ASSOC);
			echo $jsonHouseholdAchievements = json_encode($householdAchievements);
		} else {
			echo "You need to set household_id to the household you want the achievements from!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>