<?php
	//Connection info for database
	//Fetches connection information from the config.ini file then sets the connection variables
	$iniArray = parse_ini_file("/var/www/html/config.ini", true);
	$hostname = $iniArray["connectionInfo"]["hostname"];
	$username = $iniArray["connectionInfo"]["username"];
	$password = $iniArray["connectionInfo"]["password"];
	$database = $iniArray["connectionInfo"]["database"];
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		if (isset($_GET["household_id"])) {
			//NOTE: Any change in database score types or order must be done here aswell.
			//Is used to check for score types and insert them into the database.
			$scoreTypes = array(0,1,2,3,4);
			
			//Is used as parameters in MySQL and DBO
			$household_id = $_GET["household_id"];
			$scoreType = null;
			$score = null;
			$startOfMonth = date("Y-m")."-01";
			$startDate = null;
			
			//The data on household throughput for the month
			//TODO insert fetching method of data from clientside.
			$consumption = null;
			$production = null;
			$shared = null;
			$numberOfDevicesScheduled = null;
			$totalNumberOfDevices = null;
			
			//Fetches the multipliers from the game.ini file which are used to keep the scores within the same ranges
			$multipliers = parse_ini_file("/var/www/html/config.ini", true);
			
			//Calculates and stores the different score amounts.
			$pvScore = ($production/$consumption) * $multipliers["multipliers"]["pv"];
			$gridScore = (1/$consumption) * $multipliers["multipliers"]["grid"];
			$schedulingScore = ($numberOfDevicesScheduled/$totalNumberOfDevices)  * $multipliers["multipliers"]["scheduling"];
			$sharingScore = ($shared/$consumption) * $multipliers["multipliers"]["sharing"];
			$totalScore = $pvScore+$gridScore+$schedulingScore+$sharingScore;
			$scores = array($totalScore, $pvScore, $gridScore, $schedulingScore, $sharingScore);
			
			
			//MySQL and DBO for checking if a score exists for a household
			$sqlCheckIfHouseholdScoreExist = "
			SELECT *
			FROM household_scores AS HS
			WHERE HS.household_household_id = :household_id
			AND HS.score_type_score_type_id = :score_type_id
			AND HS.date BETWEEN :startDate AND CURDATE()
			LIMIT 1
			";
			$checkIfHouseholdScoreExist = $dbh->prepare($sqlCheckIfHouseholdScoreExist);
			$checkIfHouseholdScoreExist->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$checkIfHouseholdScoreExist->bindParam(":score_type_id", $scoreType, PDO::PARAM_INT);
			$checkIfHouseholdScoreExist->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			
			
			//MySQL and DBO for inserting missing household score types for the household
			$sqlInsertHouseholdScoreType = "
			INSERT INTO household_scores(household_household_id, score_type_score_type_id, date, value)
			VALUES (:household_id, :score_type_id, CURDATE(), 0)
			";
			$insertHouseholdScoreType = $dbh->prepare($sqlInsertHouseholdScoreType);
			$insertHouseholdScoreType->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$insertHouseholdScoreType->bindParam(":score_type_id", $scoreType, PDO::PARAM_INT);
			
			
			//MySQL and DBO for updating scores for the household
			$sqlUpdateHouseholdScore = "
			UPDATE household_scores
			SET date = CURDATE(),
			value = value + :value
			WHERE household_household_id = :household_id
			AND score_type_score_type_id = :score_type_id
			AND date BETWEEN :startDate AND CURDATE()
			";
			$updateHouseholdScore = $dbh->prepare($sqlUpdateHouseholdScore);
			$updateHouseholdScore->bindParam(":value", $score, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":score_type_id", $scoreType, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			
			
			//Iterate over different household score types and check if each exists. If they don't exist insert them into the table then update the score based on the values calculated.
			foreach($scoreTypes as $value) {
				$scoreType = $value;
				if ($scoreType == 0) {
					$startDate = "2010-01-01";
					$checkIfHouseholdScoreExist->execute();
					$householdScoreExist = $checkIfHouseholdScoreExist->fetchAll();
					if (count($householdScoreExist) < 1) {
						$insertHouseholdScoreType->execute();
					}
					$score = $scores[$scoreType];
					$updateHouseholdScore->execute();
				} else {
					$startDate = $startOfMonth;
					$checkIfHouseholdScoreExist->execute();
					$householdScoreExist = $checkIfHouseholdScoreExist->fetchAll();
					if (count($householdScoreExist) < 1) {
						$insertHouseholdScoreType->execute();
					}
					$score = $scores[$scoreType];
					$updateHouseholdScore->execute();
				}
			}
			
			
			/*NOTE: It is very important that the ranks have sufficient amount of points between requirements,
			so that a household doesn't gain more than one rank per score update.
			This is because the query below picks out the highest rank_id that the household has not obtained and sets it so that the household has obtained it.
			The problem occurs if there are two ranks that the household has obtained for one month as only the highest rank actually gets updated in the database,
			while the lower will still stand as not achieved.
			The subquery will also only pick the rank_id that is the highest rank_id that the household meets the requirement for, even if it has been obtained.*/
			
			//Updates the households rank if they meet the criteria for the next rank, after having updated their scores.
			$sqlUpdateHouseholdRank = "
			UPDATE household_ranks as HR
			SET HR.date_obtained = CURDATE()
			WHERE HR.household_household_id = :household_id
			AND HR.date_obtained IS NULL
			AND HR.rank_rank_id = (SELECT R.rank_id
				FROM rank AS R
				WHERE R.requirement <= (SELECT value
					FROM household_scores AS HS
					WHERE HS.household_household_id = :household_id
					AND HS.score_type_score_type_id = 0)
				ORDER BY R.rank_id DESC
				LIMIT 0, 1)
			";
			$updateHouseholdRank = $dbh->prepare($sqlUpdateHouseholdRank);
			$updateHouseholdRank->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$updateHouseholdRank->execute();
			echo "Success!"
		} else {
			echo "household_id must be set in order to update scores for the household!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}