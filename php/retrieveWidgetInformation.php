<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		//Check if parameter has been set from clientside, in this case household_id
		if (isset($_GET["household_id"])) {
			$household_id = $_GET["household_id"];
			$householdHighestRank = null;
			$resultArray = array();
			
			
			$sqlRetrieveHouseholdRankAndProgress = "
				SELECT ((s.score-cr.requirement)/(nr.requirement-cr.requirement)) AS percent, nr.rank_id-1 AS id
				FROM (SELECT SUM(value) AS score
					FROM household_scores
					WHERE score_type_score_type_id = 0
					AND household_household_id = :household_household_id
				) AS s,
					(SELECT rank_id, requirement
					FROM household_ranks AS HR
					INNER JOIN rank AS R ON HR.rank_rank_id = R.rank_id
					WHERE HR.household_household_id = :household_household_id
					ORDER BY rank_id ASC
				) AS cr,
					(SELECT rank_id, requirement
					FROM household_ranks AS HR
					INNER JOIN rank AS R ON HR.rank_rank_id = R.rank_id
					WHERE HR.household_household_id = :household_household_id
					ORDER BY rank_id ASC
				) AS nr
				WHERE nr.rank_id = (cr.rank_id+1)
				AND ((s.score-cr.requirement)/(nr.requirement-cr.requirement))<1
				ORDER BY cr.rank_id, nr.rank_id
				LIMIT 0,1
				";
			$retrieveHouseholdRankAndProgress = $dbh->prepare($sqlRetrieveHouseholdRankAndProgress);
			$retrieveHouseholdRankAndProgress->bindParam(':household_household_id', $household_id, PDO::PARAM_INT);
			$retrieveHouseholdRankAndProgress->execute();
			$householdRankAndProgress = $retrieveHouseholdRankAndProgress->fetch(PDO::FETCH_ASSOC);
			$resultArray["percent"] = $householdRankAndProgress["percent"];
			$resultArray["id"] = $householdRankAndProgress["id"];
			
			
			//Fetches the neighbourhood_id of the household requesting the leaderboard
			$sqlRetrieveHouseholdNeighbourhood = "
				SELECT neighbourhood_id
				FROM household
				WHERE household_id = :household_id
				";
			$retrieveHouseholdNeighbourhood = $dbh->prepare($sqlRetrieveHouseholdNeighbourhood);
			$retrieveHouseholdNeighbourhood->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$retrieveHouseholdNeighbourhood->execute();
			$householdNeighbourhood = $retrieveHouseholdNeighbourhood->fetch(PDO::FETCH_ASSOC);
			$neighbourhood = $householdNeighbourhood["neighbourhood"];
			
			//Fetches the households monthly total score for the leaderboard on the widget
			$sqlRetrieveHouseholdsMonthScore = "
				SELECT username, SUM(value) AS score
				FROM household AS HH
				INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
				WHERE date>:startOfMonth
				AND NOT score_type_score_type_id = 0
				AND HH.neighbourhood = :neighbourhood
				GROUP BY username
				ORDER BY score DESC
				";
			$retrieveHouseholdsMonthScore = $dbh->prepare($sqlRetrieveHouseholdsMonthScore);
			$retrieveHouseholdsMonthScore->bindParam(':startOfMonth', $date = date('Y-m').'-01', PDO::PARAM_STR);
			$retrieveHouseholdsMonthScore->bindParam(":neighbourhood", $neighbourhood, PDO::PARAM_STR);
			$retrieveHouseholdsMonthScore->execute();
			$householdsMonthScore = $retrieveHouseholdsMonthScore->fetchAll(PDO::FETCH_ASSOC);
			$resultArray["monthlyLeaderboard"] = $householdsMonthScore;
			
			
			$jsonResultArray = json_encode($resultArray);
			
			if (isset($_GET["callback"])) {
				$callback = $_GET["callback"];
				echo $callback.'('.$jsonResultArray.');';
			}
			else {
				echo $jsonResultArray;
			}
			
		} else {
			echo "household_id must be set to retrieve widget information!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>