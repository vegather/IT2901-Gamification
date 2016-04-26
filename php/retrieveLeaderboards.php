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
			$sqlRetrieveLeaderboard = "
					SELECT HH.username, R.rank_image, SUM(HS.value) as score
					FROM (
						SELECT MAX(HR.rank_rank_id) AS householdMaxRank, HR.household_household_id
						FROM household_ranks AS HR
						WHERE HR.date_obtained IS NOT NULL
						GROUP BY HR.household_household_id
					) AS maxRank
					INNER JOIN household AS HH ON maxRank.household_household_id = HH.household_id
					INNER JOIN rank AS R ON maxRank.householdMaxRank = R.rank_id
					INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
					WHERE :scoreType HS.score_type_score_type_id = 0
					:date
					GROUP BY HH.household_id
					ORDER BY HS.value DESC
					";
			$retrieveLeaderboard = $dbh->prepare($sqlRetrieveLeaderboard);
			if($_GET["leaderboard_mode"] === "total") {
				echo "Enters total!";
				$retrieveLeaderboard->bindValue(":scoreType", $scoreType = "", PDO::PARAM_STR);
				$retrieveLeaderboard->bindValue(":date", $date = "", PDO::PARAM_STR);
				echo "Parameters should have been binded!";
				echo $sqlRetrieveLeaderboard;
				$retrieveLeaderboard->execute();
				$leaderboard = $retrieveLeaderboard->fetchAll(PDO::FETCH_ASSOC);
				echo $jsonLeaderboard = json_encode($leaderboard);
			}
			elseif($_GET["leaderboard_mode"] === "timed") {
				echo "Enters timed!";
				if(!empty($_GET["start_date"]) && !empty($_GET["end_date"])) {
					$retrieveLeaderboard->bindValue(":scoreType", $scoreType = "NOT", PDO::PARAM_STR);
					$retrieveLeaderboard->bindValue(":date", $date = "AND HS.date BETWEEN '".$_GET["start_date"]."' AND '".$_GET["end_date"]."'" , PDO::PARAM_STR);
					echo "Parameters should have been binded!";
					echo $sqlRetrieveLeaderboard;
					$retrieveLeaderboard->execute();
					$leaderboard = $retrieveLeaderboard->fetchAll(PDO::FETCH_ASSOC);
					echo $jsonLeaderboard = json_encode($leaderboard);
				} else {
					echo "Need the date to and from to retrieve leaderboards from a timespan!";
				}
			}
			echo $sqlRetrieveLeaderboard;
		} else {
			echo "Need the leaderboard_mode to retrieve leaderboards!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>