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
			
			// Parameters for MySQL abd DBO
			$household_id = $_GET["household_id"];
			$startOfLastMonth = date("Y-m-d", strtotime("first day of previous month"));	
			$endOftheLastMonth = date("Y-m-d", strtotime("last day of previous month"));			
			
			//MySQL for retrieving all the achievements that are not yet schieved for the household_id in question
			$sqlRetrieveHouseholdNotAchievedAchievements = "
				SELECT A.achievement_id
				FROM achievement as A
				INNER JOIN household_achievements AS HA ON A.achievement_id = HA.achievement_achievement_id
				WHERE HA.household_household_id = :household_id
				AND HA.achieved = 0
			";
			$RetrieveHouseholdNotAchievedAchievements = $dbh->prepare($sqlRetrieveHouseholdNotAchievedAchievements);
			$RetrieveHouseholdNotAchievedAchievements->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$RetrieveHouseholdNotAchievedAchievements->execute();
			$householdNotAchieved = $RetrieveHouseholdNotAchievedAchievements->fetchAll(PDO::FETCH_NUM);
			
			$householdNotAchievedArray = array();
			
			foreach($householdNotAchieved as &$value3) {
				foreach($value3 as $value4) {
					$householdNotAchievedArray[]= $value4;
				}
			}
			
			
			//MySQL for retrieving the date the household joined
			$sqlRetrieveHouseholdJoined = "
			SELECT joined
			FROM household
			WHERE household_id = :household_id
			";
			$RetrieveHouseholdJoined = $dbh->prepare($sqlRetrieveHouseholdJoined);
			$RetrieveHouseholdJoined->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$RetrieveHouseholdJoined->execute();
			$householdJoined = $RetrieveHouseholdJoined->fetchAll(PDO::FETCH_ASSOC);
			
			//MySQL and DBO for updating achieved achievemets
			$achievement_ID = null;
			$sqlUpdateHouseholdAchievements = "
				UPDATE household_achievements
				SET date_achieved = CURDATE(), achieved = 1	
				WHERE household_household_id = :household_id
				AND achievement_achievement_id = :achievement_id
			";
			$UpdateHouseholdAchievements = $dbh->prepare($sqlUpdateHouseholdAchievements);
			$UpdateHouseholdAchievements->bindParam(":achievement_id", $achievement_ID, PDO::PARAM_INT);
			$UpdateHouseholdAchievements->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			
			//MySQL and DBO for getting score between two set dates
			$startDate = null;
			$endDate = null;
			$sqlRetrieveMonthScore = "
						SELECT SUM(HS.value) as score
						FROM household_scores as HS
						WHERE NOT HS.score_type_score_type_id = 0
						AND household_household_id = :household_id						
						AND HS.date BETWEEN :startDate AND :endDate
					";
			$retrieveMonthScore = $dbh->prepare($sqlRetrieveMonthScore);
			$retrieveMonthScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			$retrieveMonthScore->bindParam(":endDate",  $endDate, PDO::PARAM_STR);
			$retrieveMonthScore->bindParam(":household_id", $household_id, PDO::PARAM_INT);

			//MySQL and DBO for retrieving totalscore for the household
			$sqlRetrieveHouseholdTotalScore = "
					SELECT value
					FROM household_scores
					WHERE score_type_score_type_id = 0
					AND household_household_id = :household_id
				";
			$RetrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
			$RetrieveHouseholdTotalScore->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$RetrieveHouseholdTotalScore->execute();
			$totalScore = $RetrieveHouseholdTotalScore->fetchAll(PDO::FETCH_ASSOC);			
			
			
			// Monthly Report. Checks if the user has been apart of the program for one month
			if(in_array($id = 1, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 1 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				$achievement_ID = 1;
				$UpdateHouseholdAchievements->execute();
				echo json_encode("Achieved achievement 1");
			}
			
			// Monthly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 months and if the user has the requirements to achieve it
			if (in_array($id = 2, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 2 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				
				//Retrieves the score from last month
				$startDate = $startOfLastMonth;
				$endDate = $endOftheLastMonth;
				$retrieveMonthScore->execute();
				$score1 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				
				//Retrieves the score from second to last month
				$startDate = date("Y-m-d", strtotime("-1 month", strtotime($startOfLastMonth)));
				$endDate = date("Y-m-t", strtotime($startDate));
				$retrieveMonthScore->execute();
				$score2 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);

				//Gets the score from the 2 dimentional array and parses to int, then compares the scores
				if (((int)$score1[0]["score"])> ((int)$score2[0]["score"])){
					$achievement_ID = 2;
					$UpdateHouseholdAchievements->execute();
					echo json_encode("Achieved achievement 2");
				}
			}

			// Quarterly Report. Checks if the user has been apart of the program for one quarter
			if(in_array($id = 3, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 3 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				$achievement_ID = 3;
				$UpdateHouseholdAchievements->execute();
				echo json_encode("Achieved achievement 3");
			}
			
			// Quarterly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 quarters and if the user has the requirements to achieve it
			if(in_array($id = 4, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 7 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				
				//Retrieves the score from last quarter
				$startDate = date("Y-m-d", strtotime("-2 month", strtotime($startOfLastMonth)));
				$endDate =  $endOftheLastMonth;
				$retrieveMonthScore->execute();
				$score1 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				
				//Retrieves the score from secons to last quarter
				$startDate = date("Y-m-d", strtotime("-3 month", strtotime($startDate)));
				$endDate = date("Y-m-t", strtotime("+2 month", strtotime($startDate)));
				$retrieveMonthScore->execute();
				$score2 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
	
				if (((int)$score1[0]["score"])> ((int)$score2[0]["score"])){
					$achievement_ID = 4;
					$UpdateHouseholdAchievements->execute();
					echo json_encode("Achieved achievement 4");
				}
			}
			
			//Yearly Report. Checks if the user has been in the program for 1 year
			if(in_array($id = 5, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 12 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				$achievement_ID = 5;
				$UpdateHouseholdAchievements->execute();
				echo json_encode("Achieved achievement 5");
			}
			
			// Yearly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 years and if the user has the requirements to achieve it
			if(in_array($id = 6, $householdNotAchievedArray) && date("Y-m-d", strtotime(" + 25 month ", strtotime($householdJoined ))) < date("Y-m-d")){
				
				//Retrieves the score from last year
				$startDate = date("Y-m-d", strtotime("-11 month", strtotime($startOfLastMonth)));
				$endDate =  date("Y-m-d", strtotime("+1 month", strtotime($startOfLastMonth)));
				$retrieveMonthScore->execute();
				$score1 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				
				//Retrieves the score from secons to last year
				$startDate = date("Y-m-d", strtotime("-1 year", strtotime($startDate)));
				$endDate = date("Y-m-d", strtotime("-1 year", strtotime($endDate)));
				$retrieveMonthScore->execute();
				$score2 = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				
				if ((((int)$score1[0]["score"])> ((int)$score2[0]["score"]))){
					$achievement_ID = 6;
					$UpdateHouseholdAchievements->execute();
					echo json_encode("Achieved achievement 6");
				}
			}
			
			//Big numbers. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_array($id = 7, $householdNotAchievedArray) && $totalScore[0][value] >= 5000){
				$achievement_ID = 7;
				$UpdateHouseholdAchievements->execute();
				echo json_encode("Achieved achievement 7");
			}
			
			//Incredible Total. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_array($id = 8, $householdNotAchievedArray) && $totalScore[0][value] >= 10000){
				$achievement_ID = 8;
				$UpdateHouseholdAchievements->execute();
				echo json_encode("Achieved achievement 8");
			}
			
			
		} else {
			echo "You need to set household_id to the household if you want the achievements!";
		}
		
		//Close connection
		$dbh = null;
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}		
?>