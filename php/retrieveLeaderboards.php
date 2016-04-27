<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		//Check that leaderboard_mode has been set
		if (!empty($_GET["leaderboard_mode"])) {
			$sqlRetrieveLeaderboard = null;
			//This will retrieve the leaderboard over total score with parameter leaderboard_mode = total
			if($_GET["leaderboard_mode"] === "total") {
				$sqlRetrieveLeaderboard = "
					SELECT HH.household_id, IFNULL(R.rank_image, '') as rank_image, HH.username, HS.value as score
					FROM (
						SELECT MAX(HR.rank_rank_id) AS householdMaxRank, HR.household_household_id
						FROM household_ranks AS HR
						WHERE HR.date_obtained IS NOT NULL
						GROUP BY HR.household_household_id
					) AS maxRank
					INNER JOIN household AS HH ON maxRank.household_household_id = HH.household_id
					INNER JOIN rank AS R ON maxRank.householdMaxRank = R.rank_id
					INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
					WHERE HS.score_type_score_type_id = 0
					GROUP BY HH.household_id
					ORDER BY HS.value DESC
					";
				$retrieveLeaderboard = $dbh->prepare($sqlRetrieveLeaderboard);
			}
			//This will retrieve the leaderboard over a set timespan with the parameters start_date and end_date in the format yyyy-mm-dd with parameter leaderboard_mode = timed
			elseif($_GET["leaderboard_mode"] === "timed") {
				if(!empty($_GET["start_date"]) && !empty($_GET["end_date"])) {
					$sqlRetrieveLeaderboard = "
						SELECT HH.household_id, IFNULL(R.rank_image, '') as rank_image, HH.username, SUM(HS.value) as score
						FROM (
							SELECT MAX(HR.rank_rank_id) AS householdMaxRank, HR.household_household_id
							FROM household_ranks AS HR
							WHERE HR.date_obtained IS NOT NULL
							GROUP BY HR.household_household_id
						) AS maxRank
						INNER JOIN household AS HH ON maxRank.household_household_id = HH.household_id
						INNER JOIN rank AS R ON maxRank.householdMaxRank = R.rank_id
						INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
						WHERE NOT HS.score_type_score_type_id = 0
						AND HS.date BETWEEN :startDate AND :endDate
						GROUP BY HH.household_id
						ORDER BY HS.value DESC
						";
					$retrieveLeaderboard = $dbh->prepare($sqlRetrieveLeaderboard);
					$retrieveLeaderboard->bindParam(":startDate", $_GET["start_date"], PDO::PARAM_STR);
					$retrieveLeaderboard->bindParam(":endDate",  $_GET["end_date"], PDO::PARAM_STR);
				} else {
					echo "Need the start_date and end_date to retrieve leaderboards from a timespan!";
				}
			}
			$retrieveLeaderboard->execute();
			$leaderboard = $retrieveLeaderboard->fetchAll(PDO::FETCH_ASSOC);
			$jsonLeaderboard = json_encode($leaderboard);
			if (isset($_GET["callback"])) {
				$callback = $_GET["callback"];
				echo $callback.'({"data":'.$jsonLeaderboard.'});';
			} else {
				echo $jsonLeaderboard;
			}
		} else {
			echo "Need the leaderboard_mode to retrieve leaderboards!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>