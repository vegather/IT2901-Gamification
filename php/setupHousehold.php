<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Null value for use later in code as parameter
	protected $nullValue = null;
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		//Check if parameters have been set and are not empty.
		if (!empty($_GET["username"])) {
			$username = $_GET["username"];
			$household_id = null;
			
			
			//Check to see if username is available
			$sqlCheckUsernameAvailability = "
				SELECT *
				FROM household
				WHERE username = :username
				";
			$checkUsernameAvailability = dbh->prepare($sqlCheckUsernameAvailability);
			$checkUsernameAvailability->bindParam(':username', $username, PDO::PARAM_STR);
			$checkUsernameAvailability->execute();
			$usernameAvailability = $checkUsernameAvailability->get_result();
			
			
			//If username is available start setting up household in database
			if !($usernameAvailability->num_rows>0) {
				//Insert household into the database with the information provided
				$sqlInsertUser = "
					INSERT INTO household(username, residents, house_type, size, age, electric_heating, electric_car)
					VALUES(:username, :residents, :house_type, :size, :age, :electric_heating, :electric_car)
					";
				$insertUser = $dbh->prepare($sqlInsertUser);
				$insertUser->bindParam(':username', $username, PDO::PARAM_STR);
				$insertUser->bindParam(':residents', $_GET["residents"], PDO::PARAM_INT);
				$insertUser->bindParam(':house_type', $_GET["house_type"], PDO::PARAM_STR);
				$insertUser->bindParam(':size', $_GET["size"], PDO::PARAM_INT);
				$insertUser->bindParam(':age', $_GET["age"], PDO::PARAM_INT);
				$insertUser->bindParam(':electric_heating', $_GET["electric_heating"], PDO::PARAM_BOOL);
				$insertUser->bindParam(':electric_car', $_GET["electric_car"], PDO::PARAM_INT);
				$insertUser->execute();
				
				
				//Retrieves the created household_id from the database for use in the rest of the set up
				$sqlRetrieveHouseholdID = "
					SELECT household_id
					FROM household
					WHERE username = :username
					";
				$retrieveHouseholdID = dbh->prepare($sqlRetrieveHouseholdID);
				$retrieveHouseholdID->bindParam(':username', $username, PDO::PARAM_STR);
				$retrieveHouseholdID->execute();
				$householdIDArray = $retrieveHouseholdID->fetch(PDO::FETCH_ASSOC);
				$household_id = $householdIDArray['household_id'];
				
				
				//Retrieves achievements that exists for use in set up
				$sqlRetrieveAchievementsID = "
					SELECT achievement_id
					FROM achievement
					";
				$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievements);
				$retrieveAchievementsID->execute();
				$achievementsID = $retrieveAchievementsID->fetchAll(PDO::FETCH_NUM);
				
				//Sets up the household connection to the different achievements
				$achievement = null;
				$sqlInsertHouseholdAchievements = "
					INSERT INTO household_achievements(household_household_id, achievement_achievement_id, achieved, date_achieved)
					VALUES(:household_household_id, :achievement_achievement_id, :achieved, :date_achieved)
					";
				$insertHouseholdAchievements = $dbh->prepare($sqlInsertHouseholdAchievements);
				$insertHouseholdAchievements->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$insertHouseholdAchievements->bindParam(':achievement_achievement_id', $achievement, PDO::PARAM_INT);
				$insertHouseholdAchievements->bindParam(':achieved', $achieved = 0, PDO::PARAM_BOOL);
				$insertHouseholdAchievements->bindParam(':date_achieved', $nullValue, PDO::PARAM_STR);
				foreach($achievementsID as &$value) {
					$achievement = $value;
					$insertHouseholdAchievements->execute();
				}
				
				
				//Retrieves the ranks that exist for use in set up
				$sqlRetrieveRanksID = "
					SELECT rank_id
					FROM rank
					";
				$retrieveRanksID = $dbh->prepare($sqlRetrieveRanksID);
				$retrieveRanksID->execute();
				$ranksID = $retrieveRanksID->fetchAll(PDO::FETCH_NUM);
				
				//Sets up the household connection to the different ranks
				$rank = null;
				$sqlInsertHouseholdRanks = "
					INSERT INTO household_ranks(household_household_id, rank_rank_id, date_obtained)
					VALUES(:household_household_id, :rank_rank_id, :date_obtained)
					";
				$insertHouseholdRanks = $dbh->prepare($sqlInsertHouseholdRanks);
				$insertHouseholdRanks->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$insertHouseholdRanks->bindParam(':rank_rank_id', $rank, PDO::PARAM_INT);
				$insertHouseholdRanks->bindParam(':date_obtained', $nullValue, PDO::PARAM_STR);
				foreach($ranksID as &$value2) {
					$rank = $value2;
					$insertHouseholdRanks->execute();
				}
				
				//Sets it so that the household has achieved the first rank
				$sqlSetFirstRank = "
					UPDATE household_ranks
					SET obtained = 1
					WHERE household_household_id = :household_household_id
					AND rank_rank_id = (SELECT MIN(household_ranks.rank_rank_id) FROM household_ranks)
					";
				$setFirstRank = $dbh->prepare($sqlSetFirstRank);
				$setFirstRank->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$setFirstRank->execute();
			} else {
				echo "Username is taken!";
			}
		} else {
			echo "Username must be set and empty values must be null";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>