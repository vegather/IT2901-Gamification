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
			$RetrieveHouseholdNotAchievedAchievements = $dbh->prepare($sqlRetrieveHouseholdAchievements);
			$RetrieveHouseholdNotAchievedAchievements->execute();
			$householdNotAchieved = $RetrieveHouseholdNotAchievedAchievements->fetchAll(PDO::FETCH_ASSOC);
			
			//MySQL for retrieving the date the household joined
			$sqlRetrieveHouseholdJoined = "
			SELECT joined
			FROM household
			WHERE household_household_id = :household_id
			";
			$RetrieveHouseholdJoined = $dbh->prepare($sqlRetrieveHouseholdJoined);
			$RetrieveHouseholdJoined->execute();
			$householdJoined = $RetrieveHouseholdJoined->fetchAll(PDO::FETCH_ASSOC);
			
			// Monthly Report. Checks if the user has been apart of the program for one month
			if(in_arry($id = 1, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 1 month ")) < date("Y-m-d")){
				achievementAchieved ($dbh , $id);
			}
			
			// Monthly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 months and if the user has the requirements to achieve it
			if (in_arry($id = 2, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 2 month ")) < date("Y-m-d")){
				$startOfSecondToLastMonth = date("Y-m-d", strtotime($startOfLastMonth."-1 month"));	
				$endOftheSecondToLastMonth = date("Y-m-t", strtotime($startOfSecondToLastMonth));	
				$scoreLastMonth =& getScoreBetweenDates($dbh, $startOfLastMonth, $endOftheLastMonth);
				$scoreSecondToLastMonth =& getScoreBetweenDates ($dbh, $startOfSecondToLastMonth, $endOftheSecondToLastMonth);
				if (scoreLastMonth > scoreSecondToLastMonth){
					achievementAchieved($dbh, $id);
				}
			}
			
			// Quarterly Report. Checks if the user has been apart of the program for one quarter
			if(in_arry($id = 3, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 3 month ")) < date("Y-m-d")){
				achievementAchieved ($dbh , $id);
			}
			
			// Quarterly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 quarters and if the user has the requirements to achieve it
			if(in_arry($id = 4, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 7 month ")) < date("Y-m-d")){
				$startOfLastQuarter = date("Y-m-d", strtotime($startOfLastMonth."-2 month"));				
				$startOfSecondToLastQuarter = date("Y-m-d", strtotime($startOfLastQuarter."-3 month"));
				$endOftheSecondToLastQuarter = date("Y-m-t", strtotime($startOfSecondToLastMonth."-1 month"));
				$scoreLastQuarter =& getScoreBetweenDates($dbh, $endOftheLastMonth, $startOfLastQuarter);
				$scoreSecondToLastQuarter=& getScoreBetweenDates ($dbh, $startOfSecondToLastQuarter, $endOftheSecondToLastQuarter);
				if (scoreLastQuarter > scoreSecondToLastQuarter){
					achievementAchieved($dbh, $id);
				}
			}
			
			//Yearly Report. Checks if the user has been in the program for 1 year
			if(in_arry($id = 5, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 1 year")) < date("Y-m-d")){
				//MySQL and DBO for updating achieved achievemets
				achievementAchieved ($dbh , $id);
			}
			
			// Yearly Improver. Checks if the achievement is in the householdNotAchieved array, if the user has been a menber for more then 2 years and if the user has the requirements to achieve it
			if(in_arry($id = 6, $householdNotAchieved) && date('Y-m-d',strtotime(date("Y-m-d", $householdJoined) . " + 25 month ")) < date("Y-m-d")){
				$startOfLastYear = date("Y-m-d", strtotime($startOfLastMonth."-1 year"));				
				$startOfSecondToLastYear = date("Y-m-d", strtotime($startOfLastYear."-1 year"));
				$endOftheSecondToLastYear = date("Y-m-t", strtotime($startOfSecondToLastYear."-1 month"));
				$scoreLastYear =& getScoreBetweenDates($dbh, $endOftheLastMonth, $startOfLastYear);
				$scoreSecondToLastYear =& getScoreBetweenDates ($dbh, $startOfSecondToLastYear, $endOftheSecondToLastYear);
				if (scoreLastYear > scoreSecondToLastYear){
					achievementAchieved($dbh, $id);
				}
			}
			
			//Big numbers. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_arry($id = 7, $householdNotAchieved) && getTotalscore($dbh) >= 5000){
				achievementAchieved ($dbh , $id);
			}
			
			//Incredible Total. Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if(in_arry($id = 8, $householdNotAchieved) && getTotalscore($dbh) >= 10000){
				achievementAchieved ($dbh , $id);
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
function achievementAchieved ($PDO , $achievement_ID){
		$sqlUpdateHouseholdAchievements = "
				UPDATE household_achievements
				SET date_achieved = CURDATE(),
				SET achieved = 1;
				WHERE household_household_id = :household_id
				AND achievement_achievement_id = :achievement_id
			";
			$UpdateHouseholdAchievements = $PDO->prepare($sqlRetrieveHouseholdAchievements);
			$UpdateHouseholdAchievements->bindParam(":achievement_id", $achievment_ID, PDO::PARAM_INT);
			$UpdateHouseholdAchievemnts->execute();
}

//MySQL and DBO for getting score between two set dates
function getScoreBetweenDates ($PDO, $startDate, $endDate){
				$sqlRetrieveMonthScore = "
						SELECT SUM(HS.value) as score
						FROM household as HH
						INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
						WHERE NOT HS.score_type_score_type_id = 0
						AND HS.date BETWEEN :startDate AND :endDate
					";
				$retrieveMonthScore; = $dbh->prepare($sqlRetrieveMonthScore);
				$retrieveMonthScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
				$retrieveMonthScore->bindParam(":endDate",  $endDate, PDO::PARAM_STR);
				$retrieveMonthScore->execute();
				$score = $retrieveMonthScore->fetchAll(PDO::FETCH_ASSOC);
				return $score;
}

//MySQL and DBO for retrieving totalscore for the household
function getTotalscore($PDO){
			$sqlRetrieveHouseholdTotalScore = "
					SELECT value
					FROM household_scores
					WHERE score_type_score_type_id = 0
					AND household_household_id = :household_id
				";
			$RetrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
			$RetrieveHouseholdTotalScore->execute();
			$householdTotalScore = $RetrieveHouseholdTotalScore->fetchAll(PDO::FETCH_ASSOC);
			return $householdTotalScore;
}
	
?>