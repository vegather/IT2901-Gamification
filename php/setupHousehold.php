<?php
	//Required for $_POST calls
	header('Access-Control-Allow-Origin: *');
	
	//Fetches connection information from the config.ini file then sets the connection variables
	$iniArray = parse_ini_file("/var/www/html/config.ini", true);
	$hostname = $iniArray["connectionInfo"]["hostname"];
	$username = $iniArray["connectionInfo"]["username"];
	$password = $iniArray["connectionInfo"]["password"];
	$database = $iniArray["connectionInfo"]["database"];
	
	//Null value for use later in code as parameter
	$nullValue = null;

	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		//Check if parameters have been set and are not empty.
		if (isset($_POST["household_id"]) && !empty($_POST["username"]) && !empty($_POST["location"])) {
			$household_id = $_POST["household_id"];
			$householdUsername = $_POST["username"];
			$email_hash = null;
			$neighbourhood = $_POST["location"];
			
			if (!empty($_POST["email_hash"])) {
				$email_hash = $_POST["email_hash"];
			}
			
			error_log("Got past parameter setting!\n", 3, "/var/log/cossmic.log");
			
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
			
			error_log("Got past usernameAvailability query!\n", 3, "/var/log/cossmic.log");
			
			//If username is available start setting up household in database
			if (!($checkUsernameAvailability->fetchColumn()) && !($checkUsernameAvailability->fetchColumn())) {
				$today = date("Y-m-d");
				
				error_log("Got past parameter usernameAvailability check!\n", 3, "/var/log/cossmic.log");
				
				//Insert household into the database with the information provided
				$sqlInsertUser = "
					INSERT INTO household(household_id, neighbourhood, username, email_hash, joined)
					VALUES(:household_id, :neighbourhood, :username, :email_hash, :joined)
					";
				try {
					$insertUser = $dbh->prepare($sqlInsertUser);
					$insertUser->bindParam(':household_id', $household_id, PDO::PARAM_INT);
					$insertUser->bindParam(':neighbourhood', $neighbourhood, PDO::PARAM_STR);
					$insertUser->bindParam(':username', $householdUsername, PDO::PARAM_STR);
					$insertUser->bindParam(':email_hash', $email_hash, PDO::PARAM_STR);
					$insertUser->bindParam(':joined', $today, PDO::PARAM_STR);
					/*$insertUser->bindValue(':residents', getIfEmpty($_POST["residents"]), PDO::PARAM_INT);
					$insertUser->bindValue(':house_type', getIfEmpty($_POST["house_type"]), PDO::PARAM_STR);
					$insertUser->bindValue(':size', getIfEmpty($_POST["size"]), PDO::PARAM_INT);
					$insertUser->bindValue(':age', getIfEmpty($_POST["age"]), PDO::PARAM_INT);
					$insertUser->bindValue(':electric_heating', getIfEmpty($_POST["electric_heating"]), PDO::PARAM_BOOL);
					$insertUser->bindValue(':electric_car', getIfEmpty($_POST["electric_car"]), PDO::PARAM_INT);*/
					$insertUser->execute();
				} catch (PDOException $e) {
					echo "You've probably inserted an household_id that is already in the database, here is the error log: \n".$e->getMessage();
				}
				
				error_log("Got past insertUser query!\n", 3, "/var/log/cossmic.log");
				
				//Retrieves achievements that exists for use in set up
				$sqlRetrieveAchievementsID = "
					SELECT achievement_id
					FROM achievement
					";
				$retrieveAchievementsID = $dbh->prepare($sqlRetrieveAchievementsID);
				$retrieveAchievementsID->execute();
				$achievementsID = $retrieveAchievementsID->fetchAll(PDO::FETCH_NUM);
				
				error_log("Got past achievement retrival query!\n", 3, "/var/log/cossmic.log");
				
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
				$insertHouseholdAchievements->bindValue(':date_achieved', $nullValue, PDO::PARAM_STR);
				foreach($achievementsID as $value) {
					foreach($value as $value2) {
						$achievement = $value;
						$insertHouseholdAchievements->execute();
					}
				}
				
				error_log("Got past connecting household to achievement query!\n", 3, "/var/log/cossmic.log");
				
				//Makes it so the user achieves the first achievement which is registering to CoSSMUnity
				$sqlSetFirstAchievement = "
					UPDATE household_achievements
					SET achieved = 1, date_achieved = :date
					WHERE household_household_id = :household_household_id
					AND achievement_achievement_id = 0
					";
				$setFirstAchievement = $dbh->prepare($sqlSetFirstAchievement);
				$setFirstAchievement->bindParam(':date', $today, PDO::PARAM_STR);
				$setFirstAchievement->bindParam(':household_household_id', $household_id, PDO::PARAM_STR);
				$setFirstAchievement->execute();
				
				
				
				//Retrieves the ranks that exist for use in set up
				$sqlRetrieveRanksID = "
					SELECT rank_id
					FROM rank
					";
				$retrieveRanksID = $dbh->prepare($sqlRetrieveRanksID);
				$retrieveRanksID->execute();
				$ranksID = $retrieveRanksID->fetchAll(PDO::FETCH_NUM);
				
				error_log("Got past retrieve ranks query!\n", 3, "/var/log/cossmic.log");
				
				//Sets up the household connection to the different ranks
				$rank = null;
				$sqlInsertHouseholdRanks = "
					INSERT INTO household_ranks(household_household_id, rank_rank_id, date_obtained)
					VALUES(:household_household_id, :rank_rank_id, :date_obtained)
					";
				$insertHouseholdRanks = $dbh->prepare($sqlInsertHouseholdRanks);
				$insertHouseholdRanks->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
				$insertHouseholdRanks->bindParam(':rank_rank_id', $rank, PDO::PARAM_INT);
				$insertHouseholdRanks->bindValue(':date_obtained', $nullValue, PDO::PARAM_STR);
				foreach($ranksID as &$value3) {
					foreach($value3 as $value4) {
						$rank = $value2;
						$insertHouseholdRanks->execute();
					}
				}
				
				error_log("Got past connecting household to rank query!\n", 3, "/var/log/cossmic.log");
				
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
				
				error_log("Got past setting the first rank!\n", 3, "/var/log/cossmic.log");
				
				//Is used to check for score types and insert them into the database.
				$scoreTypeKeys = array("Total Score", "PV Score", "Grid Score", "Scheduling Score", "Share Score");
				$scoreType = array(0,1,2,3,4);
				$scoreTypes = array_combine($scoreTypeKeys, $scoreType);
				
				//Is used as parameters in MySQL and DBO
				$type = null;
				$startOfMonth = date("Y-m")."-01";
				$startDate = null;
				
				//MySQL and DBO for checking if a score exists
				$sqlCheckIfHouseholdScoreExist = "
				SELECT *
				FROM household_scores AS HS
				WHERE HS.household_household_id = :household_id
				AND HS.score_type_score_type_id = :score_type_id
				AND HS.date BETWEEN :startDate AND :endDate
				LIMIT 1
				";
				$checkIfHouseholdScoreExist = $dbh->prepare($sqlCheckIfHouseholdScoreExist);
				$checkIfHouseholdScoreExist->bindParam(":household_id", $household_id, PDO::PARAM_INT);
				$checkIfHouseholdScoreExist->bindParam(":score_type_id", $type, PDO::PARAM_INT);
				$checkIfHouseholdScoreExist->bindParam(":startDate", $startDate, PDO::PARAM_STR);
				$checkIfHouseholdScoreExist->bindParam(":endDate", $today, PDO::PARAM_STR);
				
				error_log("Got past score checking!\n", 3, "/var/log/cossmic.log");
				
				//MySQL and DBO for inserting missing household score types
				$sqlInsertHouseholdScoreType = "
				INSERT INTO household_scores(household_household_id, score_type_score_type_id, date, value)
				VALUES (:household_id, :score_type_id, :date, :value)
				";
				$insertHouseholdScoreType = $dbh->prepare($sqlInsertHouseholdScoreType);
				$insertHouseholdScoreType->bindParam(":household_id", $household_id, PDO::PARAM_INT);
				$insertHouseholdScoreType->bindParam(":score_type_id", $type, PDO::PARAM_INT);
				$insertHouseholdScoreType->bindParam(":date", $today, PDO::PARAM_STR);
				$insertHouseholdScoreType->bindParam(":value", $amount = 0, PDO::PARAM_INT);
				
				//Iterate over different household score types and check if each exists, and if not insert them into the table then update the score
				foreach($scoreTypes as $key => $value) {
					$type = $value;
					if ($type == 0) {
						$startDate = "2010-01-01";
						$checkIfHouseholdScoreExist->execute();
						$householdScoreExist = $checkIfHouseholdScoreExist->fetchAll();
						if (count($householdScoreExist) < 1) {
							$insertHouseholdScoreType->execute();
						}
					} else {
						$startDate = $startOfMonth;
						$checkIfHouseholdScoreExist->execute();
						$householdScoreExist = $checkIfHouseholdScoreExist->fetchAll();
						if (count($householdScoreExist) < 1) {
							$insertHouseholdScoreType->execute();
						}
					}
				}
				echo "Success";
				error_log("Got score inserting!\n", 3, "/var/log/cossmic.log");
			} else {
				echo "Household_ID or Username is taken!";
			}
		} else {
			echo "household_id, username and email_hash must be set to a value and can't be empty, while other values that can and are empty must be null";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
	
function getIfEmpty($post) {
    if (empty($post)) {
		return $nullValue;
	} else {
		return $post;
	}
}
?>