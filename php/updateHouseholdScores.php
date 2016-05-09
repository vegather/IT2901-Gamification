<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		if (isset($_GET["household_id"])) {
			//Note: Any change in database score types or order must be done here aswell.
			//Is used to check for score types and insert them into the database.
			$scoreTypeKeys = array("Total Score", "PV Score", "Grid Score", "Scheduling Score", "Share Score");
			$scoreType = array(0,1,2,3,4);
			$scoreTypes = array_combine($scoreTypeKeys, $scoreType);
			
			
			//Is used as parameters in MySQL and DBO
			$household_id = $_GET["household_id"];
			$type = null;
			$points = null;
			$pointsArray = array_combine($scoreType, $scoreType);
			$startOfMonth = date("Y-m")."-01";
			$startDate = null;
			$endDate = date("Y-m-d");
			
			
			//Fetches the multipliers from the game.ini file
			$multipliers = parse_ini_file("/var/www/html/game.ini");
			
			
			//Calculates and stores the different score amounts.
			
			
			
			//MySQL and DBO for checking if a score exists
			$sqlCheckIfHouseholdScoreExist = "
			SELECT *
			FROM household_scores AS HS
			WHERE HS.household_household_id = :household_id
			AND HS.score_type_score_type_id = :score_type_id
			AND HS.date BETWEEN :startDate AND :endDate
			LIMIT 1
			";
			$checkIfHouseholdScoreExist = $dbh->prepare($sqlCheckIfHouseholdScoreExist);
			$checkIfHouseholdScoreExist->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$checkIfHouseholdScoreExist->bindParam(":score_type_id", $type, PDO::PARAM_INT);
			$checkIfHouseholdScoreExist->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			$checkIfHouseholdScoreExist->bindParam(":endDate", $endDate, PDO::PARAM_STR);
			
			
			//MySQL and DBO for inserting missing household score types
			$sqlInsertHouseholdScoreType = "
			INSERT INTO household_scores(household_household_id, score_type_score_type_id, date, value)
			VALUES (:household_id, :score_type_id, :date, :value)
			";
			$insertHouseholdScoreType = $dbh->prepare($sqlInsertHouseholdScoreType);
			$insertHouseholdScoreType->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$insertHouseholdScoreType->bindParam(":score_type_id", $type, PDO::PARAM_INT);
			$insertHouseholdScoreType->bindParam(":date", $endDate, PDO::PARAM_STR);
			$insertHouseholdScoreType->bindParam(":value", $amount = 0, PDO::PARAM_INT);
			
			
			//MySQL and DBO for updating scores for the household
			$sqlUpdateHouseholdScore = "
			UPDATE household_scores
			SET date = :date,
			value = value + :value
			WHERE household_household_id = :household_id
			AND score_type_score_type_id = :score_type_id
			AND date BETWEEN :startDate AND :endDate
			";
			$updateHouseholdScore = $dbh->prepare($sqlUpdateHouseholdScore);
			$updateHouseholdScore->bindParam(":date", $endDate, PDO::PARAM_STR);
			$updateHouseholdScore->bindParam(":value", $points, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":household_id", $household_id, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":score_type_id", $type, PDO::PARAM_INT);
			$updateHouseholdScore->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			$updateHouseholdScore->bindParam(":endDate", $endDate, PDO::PARAM_STR);
			
			
			//Iterate over different household score types and check if each exists, and if not insert them into the table then update the score
			foreach($scoreTypes as $key => $value) {
				$type = $value;
				if ($type == 0) {
					$startDate = "2010-01-01";
					$checkHouseholdScoreExist->execute();
					$householdScoreExist = $checkHouseholdScoreExist->fetchAll();
					if (count($householdScoreExist)) < 1) {
						$insertHouseholdScoreType->execute();
					}
				} else {
					$startDate = $startOfMonth;
					$checkHouseholdScorExist->execute();
					$householdScoreExist = $checkHouseholdScoreExist->fetchAll();
					if (count($householdScoreExist)) < 1) {
						$insertHouseholdScoreType->execute();
					}
					$points = $pointsArray[($type-1)];
					$updateHouseholdScore->execute();
				}
			}
			//Has to be run once afterwards to update the total score for the household
			$type = 0;
			$points = $totalPoints;
			$startDate = "2010-01-01";
			$updateHouseholdScore->execute();
		}

		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}