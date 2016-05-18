<?php
//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		//Check if household_id has been set as a parameter
		if (isset($_GET["household_id"])) {
			
			// Parameters for MySQL abd DBO
			$household_id = $_GET["household_id"];
			$startOfLastMonth = date("Y-n-d", strtotime("first day of previous month"));	
			$endOftheLastMonth = date("Y-n-d", strtotime("last day of previous month"));			
			
			//MySQL for retrieving all the achievements that are not yet schieved for the household_id in question
			$sqlRetrieveHouseholdNotAchievedAchievements = "
				SELECT A.achievement_id
				FROM achievement as A
				INNER JOIN household_achievements AS HA ON A.achievement_id = HA.achievement_achievement_id
				WHERE HA.household_household_id = :household_id
				AND HA.achieved = 0
			";
			$sqlRetrieveHouseholdNotAchievedAchievements = $dbh->prepare($sqlRetrieveHouseholdAchievements);
			$sqlRetrieveHouseholdNotAchievedAchievements->execute();
			$householdNotAchieved = $sqlRetrieveHouseholdNotAchievedAchievements->fetchAll(PDO::FETCH_ASSOC);
			
			//MySQL for retrieving the date the household joined
			$sqlRetrieveHouseholdJoined = "
			SELECT joined
			FROM household
			WHERE household_household_id = :household_id
			";
			$sqlRetrieveHouseholdJoined = $dbh->prepare($sqlRetrieveHouseholdJoined);
			$sqlRetrieveHouseholdJoined->execute();
			$householdJoined = $sqlRetrieveHouseholdJoined->fetchAll(PDO::FETCH_ASSOC);
			
			//Checks if the user has been apart of the program for one quarter
			if(in_arry($id = 4, $householdNotAchieved) && $sqlRetrieveHouseholdJoined->add(new DateInterval('P3M') < date){
				achievementAchived ($dbh , $id);
			}
			
			//Checks if the user has been in the program for 1 year
			if( in_arry($id = 5, $householdNotAchieved) && $sqlRetrieveHouseholdJoined->add(new DateInterval('P1Y') < date){
				//MySQL and DBO for updating achieved achievemets
				achievementAchived ($dbh , $id);
			}
			
			//Checks if the achievement is in the householdNotAchieved array and if the user has the requirements to achieve it
			if( in_arry($id = 7, $householdNotAchieved) && getTotalscore($dbh) >= 10000){
				achievementAchived ($dbh , $id);
			}
			
			if (in_arry($id = 1, $householdNotAchieved) && $sqlRetrieveHouseholdJoined->add(new DateInterval('P3M') < date){
				$startOfSecondToLastMonth = date($startOfLastMonth, strtotime("-1 months"));	
				$endOftheSecondToLastMonth = date($endOftheLastMonth, strtotime("-1 months"));	
				scoreLastMonth = getScoreBetweenDates($dbh, $startOfLastMonth, $endOftheLastMonth);
				scoreSecondToLastMonth = getScoreBetweenDates ($dbh, $startOfSecondToLastMonthLastMonth, $endOftheSecondToLastMonth);
				if (scoreLastMonth > scoreSecondToLastMonth){
					achievementAchived($dbh, $id);
				}
			}
			
			if (in_arry($id = 2, $householdNotAchieved) && $sqlRetrieveHouseholdJoined->add(new DateInterval('P7M') < date){
				$startOfLastQuarter = date(startOfLastMonth, strtotime("-2 months"));	
				$StartOfSecondToLastQuarter = date($startOfLastMonth, strtotime("-2 months"));
				$endOftheSecondToLastQuarter = date($endOftheLastMonth, strtotime("-1 months"));
				scoreLastMonth = getScoreBetweenDates($dbh, $endOftheLastMonth, $startOfLastQuarter);
				scoreSecondToLastMonth = getScoreBetweenDates ($dbh, $startOfSecondToLastMonthLastMonth, $endOftheSecondToLastMonth);
				if (scoreLastMonth > scoreSecondToLastMonth){
					achievementAchived($dbh, $id);
				}
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
function achievementAchived ($PDO , $achivement_ID){
		$sqlUpdateHouseholdAchievemnts = "
				UPDATE household_achievements
				SET date_achieved = CURDATE(),
				SET achieved = 1;
				WHERE household_household_id = :household_id
				AND achievement_achievement_id = :achievement_ID
			";
			$UpdateHouseholdAchievements = $PDO->prepare($sqlRetrieveHouseholdAchievements);
			$UpdateHouseholdAchievements->bindParam(":achievement_ID", $achivment_ID, PDO::PARAM_INT);
			$UpdateHouseholdAchievemnts->execute();
	}
}

//MySQL and DBO for getting score between two set dates
function getScoreBetweenDates ($PDO, $startDate, $endDate){
					$sqlRetrieveMonthScore = "
						SELECT SUM(HS.value) as score
						FROM household as HH
						INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
						WHERE NOT HS.score_type_score_type_id = 0
						AND HS.date BETWEEN :startOfLastMonthDate AND :endOftheLastMonthDate
					";
				$retrieveMonthScore; = $dbh->prepare($sqlRetrieveMonthScore);
				$retrieveMonthScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
				$retrieveMonthScore->bindParam(":endDate",  $endDate, PDO::PARAM_STR);
				$retrieveLeaderboard->execute();
				$score = $retrieveLeaderboard->fetchAll(PDO::FETCH_ASSOC);
				return $score
}

//MySQL and DBO for retrieving totalscore for the household
function getTotalscore($PDO){
			$sqlRetrieveHouseholdTotalScore = "
					SELECT value
					FROM household_scores
					WHERE score_type_score_type_id = :score_type_score_type_id
					AND household_household_id = :household_id
				";
			$sqlRetrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
			$sqlRetrieveHouseholdTotalScore->execute();
			$householdTotalScore = $sqlRetrieveHouseholdTotalScore->fetchAll(PDO::FETCH_ASSOC);
			return $householdTotalScore
}
	
?>