<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	//Null value for use later in code as parameter
	$nullValue = null;
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		$sqlInsertUser = "INSERT INTO household(username, residents, house_type, size, age, electric_heating, electric_car)
			VALUES(:username, :residents, :house_type, :size, :age, :electric_heating, :electric_car)";
		$insertUser = $dbh->prepare($sqlInsertUser);
		$insertUser->bindParam(':username', $insert HTML form method['field'], PDO::PARAM_STR);
		$insertUser->bindParam(':residents', $insert HTML form method['field'], PDO::PARAM_INT);
		$insertUser->bindParam(':house_type', $insert HTML form method['field'], PDO::PARAM_STR);
		$insertUser->bindParam(':size', $insert HTML form method['field'], PDO::PARAM_INT);
		$insertUser->bindParam(':age', $insert HTML form method['field'], PDO::PARAM_INT);
		$insertUser->bindParam(':electric_heating', $insert HTML form method['field'], PDO::PARAM_BOOL);
		$insertUser->bindParam(':electric_car', $insert HTML form method['field'], PDO::PARAM_INT);
		$insertUser->execute();
		
		
		//$householdUsername = $insert HTML form method['field'];
		$sqlRetrieveHouseholdID = "SELECT household_id
			FROM household
			WHERE username = :username";
		$retrieveHouseholdID = dbh->prepare($sqlRetrieveHouseholdID);
		$retrieveHouseholdID->bindParam(':username', $insert HTML form method['username'], PDO::PARAM_STR);
		$retrieveHouseholdID->execute();
		$householdIDArray = $retrieveHouseholdID->fetch();
		$householdID = $householdIDArray['household_id'];
		
	
		$sqlRetrieveAchievementsID = "SELECT achievement_id
			FROM achievement";
		$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievements);
		$retrieveAchievementsID->execute();
		$achievementsID = $retrieveAchievementsID->fetchAll(PDO::FETCH_COLUMN);
		
		
		//$achievement = null;
		$sqlInsertHouseholdAchievements = "INSERT INTO household_achievements(household_household_id, achievement_achievement_id, achieved, date_achieved)
			VALUES(:household_household_id, :achievement_achievement_id, :achieved, :date_achieved)";
		$insertHouseholdAchievements = $dbh->prepare($sqlInsertHouseholdAchievements);
		$insertHouseholdAchievements->bindParam(':household_household_id', $householdID, PDO::PARAM_INT);
		$insertHouseholdAchievements->bindParam(':achievement_achievement_id', $achievement, PDO::PARAM_INT);
		$insertHouseholdAchievements->bindParam(':achieved', 0, PDO::PARAM_BOOL);
		$insertHouseholdAchievements->bindParam(':date_achieved', $nullValue, PDO::PARAM_STR);
		foreach($achievementsID as &$value) {
			$achievement = $value;
			$insertHouseholdAchievements->execute();
		}
		
		
		$sqlRetrieveRanksID = "SELECT rank_id
			FROM rank";
		$retrieveRanksID = $dbh->prepare($sqlRetrieveRanksID);
		$retrieveRanksID->execute();
		$ranksID = $retrieveRanksID->fetchAll(PDO::FETCH_COLUMN);
		
		
		$sqlInsertHouseholdRanks = "INSERT INTO household_ranks(household_household_id, rank_rank_id, obtained, date_obtained)
			VALUES(:household_household_id, :rank_rank_id, :obtained, :date_obtained)";
		$insertHouseholdRanks = $dbh->prepare($sqlInsertHouseholdRanks);
		$insertHouseholdRanks->bindParam(':household_household_id', $householdID, PDO::PARAM_INT);
		$insertHouseholdRanks->bindParam(':rank_rank_id', $rank, PDO::PARAM_INT);
		$insertHouseholdRanks->bindParam(':obtained', 0, PDO::PARAM_BOOL);
		$insertHouseholdRanks->bindParam(':date_obtained', $nullValue, PDO::PARAM_STR);
		foreach($ranksID as &$value2) {
			$rank = $value2;
			$insertHouseholdRanks->execute();
		}
		
		
		$sqlSetFirstRank = "UPDATE household_ranks
			SET obtained = 1
			WHERE household_household_id = :household_household_id
			AND rank_rank_id = (SELECT MIN(household_ranks.rank_rank_id)
				FROM household_ranks
				)";
		$setFirstRank = $dbh->prepare($sqlSetFirstRank);
		$setFirstRank->bindParam(':household_household_id', $householdID, PDO::PARAM_INT);
		$setFirstRank->execute();
		
		
		//Close connection
		$dbh = null;
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>