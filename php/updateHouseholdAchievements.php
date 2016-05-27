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
			
			echo json_encode($householdJoined);
			$householdJoined = $householdJoined[0]["joined"];
			echo json_encode($householdJoined);
			

			
			// Monthly Report. Checks if the user has been apart of the program for one month
			if(in_arry($id = 1, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 1 month ")) < date("Y-m-d")){
				achievementAchieved ($dbh , $id, $household_id);
				echo json_encode("1");
			}
			
			// Monthly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 months and if the user has the requirements to achieve it
			if (in_arry($id = 2, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 2 month ")) < date("Y-m-d")){
				$startOfSecondToLastMonth = date("Y-m-d", strtotime($startOfLastMonth."-1 month"));	
				$endOftheSecondToLastMonth = date("Y-m-t", strtotime($startOfSecondToLastMonth));	
				$scoreLastMonth =& getScoreBetweenDates($dbh, $startOfLastMonth, $endOftheLastMonth, $household_id);
				$scoreSecondToLastMonth =& getScoreBetweenDates ($dbh, $startOfSecondToLastMonth, $endOftheSecondToLastMonth, $household_id);
				if (scoreLastMonth > scoreSecondToLastMonth){
					achievementAchieved($dbh, $id, $household_id);
					echo json_encode("2");
				}
				echo json_encode("2");
			}
			
			// Quarterly Report. Checks if the user has been apart of the program for one quarter
			if(in_arry($id = 3, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 3 month ")) < date("Y-m-d")){
				achievementAchieved ($dbh , $id, $household_id);
				echo json_encode("3");
			}
			
			// Quarterly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 quarters and if the user has the requirements to achieve it
			if(in_arry($id = 4, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 7 month ")) < date("Y-m-d")){
				$startOfLastQuarter = date("Y-m-d", strtotime($startOfLastMonth."-2 month"));				
				$startOfSecondToLastQuarter = date("Y-m-d", strtotime($startOfLastQuarter."-3 month"));
				$endOftheSecondToLastQuarter = date("Y-m-t", strtotime($startOfSecondToLastMonth."-1 month"));
				$scoreLastQuarter =& getScoreBetweenDates($dbh, $endOftheLastMonth, $startOfLastQuarter, $household_id);
				$scoreSecondToLastQuarter=& getScoreBetweenDates ($dbh, $startOfSecondToLastQuarter, $endOftheSecondToLastQuarter, $household_id);
				if (scoreLastQuarter > scoreSecondToLastQuarter){
					achievementAchieved($dbh, $id, $household_id);
					echo json_encode("4");
				}
				echo json_encode("4");
			}
			
			//Yearly Report. Checks if the user has been in the program for 1 year
			if(in_arry($id = 5, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 1 year")) < date("Y-m-d")){
				achievementAchieved ($dbh , $id, $household_id);
				echo json_encode("5");
			}
			
			// Yearly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 years and if the user has the requirements to achieve it
			if(in_arry($id = 6, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 25 month ")) < date("Y-m-d")){
				$startOfLastYear = date("Y-m-d", strtotime($startOfLastMonth."-1 year"));				
				$startOfSecondToLastYear = date("Y-m-d", strtotime($startOfLastYear."-1 year"));
				$endOftheSecondToLastYear = date("Y-m-t", strtotime($startOfSecondToLastYear."-1 month"));
				$scoreLastYear =& getScoreBetweenDates($dbh, $endOftheLastMonth, $startOfLastYear, $household_id);
				$scoreSecondToLastYear =& getScoreBetweenDates ($dbh, $startOfSecondToLastYear, $endOftheSecondToLastYear, $household_id);
				if (scoreLastYear > scoreSecondToLastYear){
					achievementAchieved($dbh, $id, $household_id);
					echo json_encode("6");
				}
				echo json_encode("6");
			}
			
			//Big numbers. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_arry($id = 7, $householdNotAchieved) && getTotalscore($dbh, $household_id) >= 50){
				achievementAchieved ($dbh , $id, $household_id);
				echo json_encode("7");
			}
			
			//Incredible Total. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_arry($id = 8, $householdNotAchieved) && getTotalscore($dbh, $household_id) >= 10000){
				achievementAchieved ($dbh , $id, $household_id);
				echo json_encode("8");
			}
			
			
		} else {
			echo "You need to set household_id to the household if you want the achievements!";
		}
		
		//Close connection
		$dbh = null;
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}	

//MySQL and DBO for updating achieved achievemets
function achievementAchieved ($PDO , $achievement_ID, $household_ID){
		$sqlUpdateHouseholdAchievements = "
				UPDATE household_achievements
				SET date_achieved = CURDATE(), achieved = 1	
				WHERE household_household_id = :household_id
				AND achievement_achievement_id = :achievement_id
			";
			$UpdateHouseholdAchievements = $PDO->prepare($sqlUpdateHouseholdAchievements);
			$UpdateHouseholdAchievements->bindParam(":achievement_id", $achievment_ID, PDO::PARAM_INT);
			$UpdateHouseholdAchievements->bindParam(":household_id", $household_ID, PDO::PARAM_INT);
			$UpdateHouseholdAchievemnts->execute();
}

//MySQL and DBO for getting score between two set dates
function getScoreBetweenDates ($PDO, $startDate, $endDate, $household_ID){
				$sqlRetrieveMonthScore = "
						SELECT SUM(HS.value) as score
						FROM household as HH
						INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
						WHERE NOT HS.score_type_score_type_id = 0
						AND household_household_id = :household_id						
						AND HS.date BETWEEN :startDate AND :endDate
					";
				$retrieveMonthScore = $dbh->prepare($sqlRetrieveMonthScore);
				$retrieveMonthScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
				$retrieveMonthScore->bindParam(":endDate",  $endDate, PDO::PARAM_STR);
				$retrieveMonthScore->bindParam(":household_id", $household_ID, PDO::PARAM_INT);
				$retrieveMonthScore->execute();
				$score = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				return $score;
}

//MySQL and DBO for retrieving totalscore for the household
function getTotalscore($PDO, $household_ID){
			$sqlRetrieveHouseholdTotalScore = "
					SELECT value
					FROM household_scores
					WHERE score_type_score_type_id = 0
					AND household_household_id = :household_id
				";
			$RetrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
			$RetrieveHouseholdTotalScore->bindParam(":household_id", $household_ID, PDO::PARAM_INT);
			$RetrieveHouseholdTotalScore->execute();
			$householdTotalScore = $RetrieveHouseholdTotalScore->fetchAll(PDO::FETCH_ASSOC);
			return $householdTotalScore;
}
	
?>