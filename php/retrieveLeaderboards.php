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
		
		
		//Check that leaderboard_mode has been set
		if (!empty($_GET["leaderboard_mode"]) && isset($_GET["household_id"])) {
			$sqlRetrieveLeaderboard = null;
			$resultArray = array();
			
			
			//Fetches the neighbourhood_id of the household requesting the leaderboard
			$sqlRetrieveHouseholdNeighbourhood = "
				SELECT neighbourhood
				FROM household
				WHERE household_id = :household_id
				";
			$retrieveHouseholdNeighbourhood = $dbh->prepare($sqlRetrieveHouseholdNeighbourhood);
			$retrieveHouseholdNeighbourhood->bindParam(":household_id", $_GET["household_id"], PDO::PARAM_INT);
			$retrieveHouseholdNeighbourhood->execute();
			$householdNeighbourhood = $retrieveHouseholdNeighbourhood->fetch(PDO::FETCH_ASSOC);
			$neighbourhood = $householdNeighbourhood["neighbourhood"];
			
			//This will retrieve the leaderboard over total score with parameter leaderboard_mode = total
			if($_GET["leaderboard_mode"] === "total") {
				$sqlRetrieveLeaderboard = "
					SELECT HH.household_id, HH.email_hash, R.rank_id, HH.username, HS.value as score
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
					AND HH.neighbourhood = :neighbourhood
					GROUP BY HH.household_id
					ORDER BY score DESC
					";
				$retrieveLeaderboard = $dbh->prepare($sqlRetrieveLeaderboard);
				$retrieveLeaderboard->bindParam(":neighbourhood", $neighbourhood, PDO::PARAM_STR);
			}
			//This will retrieve the leaderboard over a set timespan with the parameters start_date and end_date in the format yyyy-mm-dd with parameter leaderboard_mode = timed
			elseif($_GET["leaderboard_mode"] === "timed") {
				if(!empty($_GET["start_date"]) && !empty($_GET["end_date"])) {
					$sqlRetrieveLeaderboard = "
						SELECT HH.household_id, HH.email_hash, R.rank_id, HH.username, SUM(HS.value) as score
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
						AND HH.neighbourhood = :neighbourhood
						GROUP BY HH.household_id
						ORDER BY score DESC
						";
					$retrieveLeaderboard = $dbh->prepare($sqlRetrieveLeaderboard);
					$retrieveLeaderboard->bindParam(":startDate", $_GET["start_date"], PDO::PARAM_STR);
					$retrieveLeaderboard->bindParam(":endDate",  $_GET["end_date"], PDO::PARAM_STR);
					$retrieveLeaderboard->bindParam(":neighbourhood", $neighbourhood, PDO::PARAM_STR);
				} else {
					echo "Need the start_date and end_date to retrieve leaderboards from a timespan!";
				}
			}
			$retrieveLeaderboard->execute();
			$leaderboard = $retrieveLeaderboard->fetchAll(PDO::FETCH_ASSOC);
			
			
			//Should find index of household that is currently fetching the leaderboard
			$currentHouseholdIndex= array_search($_GET["household_id"], array_column($leaderboard, "household_id"));
			$resultArray["currentHouseholdIndex"] = $currentHouseholdIndex;
			
			
			//Should remove all household_ids from the leaderboard array as they aren't needed clientside thus reducing overhead
			foreach($leaderboard as $key => $household) {
				foreach($household as $key2 => $value2) {
					if ($key2 == "household_id") {
						unset($leaderboard[$key][$key2]);
					}
				}
			}
			$resultArray["leaderboard"] = $leaderboard;
			
			
			$jsonLeaderboard = json_encode($resultArray);
			
			if (isset($_GET["callback"])) {
				$callback = $_GET["callback"];
				echo $callback.'({"data":'.$jsonLeaderboard.'});';
			} else {
				echo $jsonLeaderboard;
			}
		} else {
			echo "Need the leaderboard_mode and household_id of household making request to retrieve leaderboards!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>