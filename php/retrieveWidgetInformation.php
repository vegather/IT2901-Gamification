<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
				
		
		//TODO Write code to put household_id into the variable below from the clientside.
		if (isset($_GET["household_id"])) {
			$household_id = $_GET["household_id"];
			$householdHighestRank = null;
			$resultArray = array();
		
		
			// Notes: Functions like MAX or other such things in MySQL, needs to be defined with the AS if you're going to be able to retrieve them from result set.
			//Fetches the current rank the household is at, for use in the script
			$sqlRetrieveHouseholdHighestRank = "SELECT MAX(rank_rank_id) AS rank
				FROM household_ranks
				WHERE date_obtained IS NOT NULL
				AND household_household_id = :household_household_id";
			$retrieveHouseholdHighestRank = $dbh->prepare($sqlRetrieveHouseholdHighestRank);
			$retrieveHouseholdHighestRank->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
			$retrieveHouseholdHighestRank->execute();
			$householdHighestRank = $retrieveHouseholdHighestRank->fetch(PDO::FETCH_ASSOC);
			$householdHighestRank = $householdHighestRank['rank'];
			
			
			//Fetches the rank information for the users current rank for the widget
			$sqlRetrieveHouseholdRankInformation = "SELECT *
				FROM rank
				WHERE rank_id = :rank_id";
			$retrieveHouseholdRankInformation = $dbh->prepare($sqlRetrieveHouseholdRankInformation);
			$retrieveHouseholdRankInformation->bindParam(':rank_id', $householdHighestRank, PDO::PARAM_INT);
			$retrieveHouseholdRankInformation->execute();
			$householdRankInformation = $retrieveHouseholdRankInformation->fetch(PDO::FETCH_ASSOC);
			$currentRankRequirement = $householdRankInformation['requirement'];
			$resultArray["rank_id"] = $householdRankInformation['rank_id'];
			$resultArray["rank_name"] = $householdRankInformation['rank_name'];
			$resultArray["requirement"] = $householdRankInformation['requirement'];
			$resultArray["rank_image"] = $householdRankInformation['rank_image'];
			
			
			//Fetches the requirement for the next rank for the household, which will be used for calculations in the script
			$sqlRetrieveNextRankRequirement = "SELECT requirement
				FROM rank
				WHERE rank_id = :rank_id";
			$retrieveNextRankRequirement = $dbh->prepare($sqlRetrieveNextRankRequirement);
			$retrieveNextRankRequirement->bindParam(':rank_id', ++$householdHighestRank, PDO::PARAM_INT);
			$retrieveNextRankRequirement->execute();
			$nextRankRequirement = $retrieveNextRankRequirement->fetch(PDO::FETCH_ASSOC);
			$nextRankRequirement = $nextRankRequirement['requirement'];
			
			
			//Fetches the household total score, which will be used for calculations in the script
			$sqlRetrieveHouseholdTotalScore = "SELECT value
				FROM household_scores
				WHERE score_type_score_type_id = :score_type_score_type_id
				AND household_household_id = :household_household_id";
			$retrieveHouseholdTotalScore = $dbh->prepare($sqlRetrieveHouseholdTotalScore);
			$retrieveHouseholdTotalScore->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
			$retrieveHouseholdTotalScore->bindParam(':score_type_score_type_id', $score_type_id = 0, PDO::PARAM_INT);
			$retrieveHouseholdTotalScore->execute();
			$householdTotalScore = $retrieveHouseholdTotalScore->fetch(PDO::FETCH_ASSOC);
			$householdTotalScore = $householdTotalScore['value'];
			
			
			//Calculate the percentage done for the next rank for use in the widget
			$denominator = $nextRankRequirement - $currentRankRequirement;
			$numerator = $householdTotalScore - $currentRankRequirement;
			$percentage = $numerator / $denominator;
			$resultArray["percentage"] = $percentage;
			
			
			//Fetches the households monthly total score for the leaderboard on the widget
			$sqlRetrieveHouseholdsMonthScore = "SELECT username, SUM(value) AS score
				FROM household AS HH
				INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
				WHERE date>:startOfMonth
				AND NOT score_type_score_type_id = 0
				GROUP BY username
				ORDER BY score DESC";
			$retrieveHouseholdsMonthScore = $dbh->prepare($sqlRetrieveHouseholdsMonthScore);
			$retrieveHouseholdsMonthScore->bindParam(':startOfMonth', $date = date('o-m').'-01', PDO::PARAM_STR);
			$retrieveHouseholdsMonthScore->execute();
			$householdsMonthScore = $retrieveHouseholdsMonthScore->fetchAll(PDO::FETCH_ASSOC);
			$resultArray["householdsMonthScore"] = $householdsMonthScore;
			
			echo $jsonResultArray = json_encode($resultArray);
		} else {
			echo "Household ID must be set to retrieve widget information!";
		}
		
		
		// Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>