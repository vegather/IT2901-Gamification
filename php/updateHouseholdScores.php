<?php
	//Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; //same as above
	$database = 'CoSSMunity';
	
	//Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		$scoreTypeKeys = array("Total Score", "PV Score", "Grid Score", "Scheduling Score", "Share Score")
		$scoreType = array(0,1,2,3,4)
		$scoreTypesArray = array_combine($scoreTypeKeys, $scoreType)
		$falseArray = [FALSE, FALSE, FALSE, FALSE, FALSE]
		$scoreTypeExistArray = array_combine($scoreTypeKeys, $falseArray);
		
		
		$type = null;
		$startDate = null;
		$endDate = null;
		
		
		if (isset($_GET["household_id"])) {	
			$sqlCheckIfHouseholdScoreExist = "
			SELECT EXISTS
				(SELECT HS.score_type_score_type_id
				FROM household_scores AS HS
				WHERE HS.household_household_id = :household_id
				AND HS.score_type_score_type_id = :score_type_id
				AND HS.date BETWEEN :startDate AND :endDate)
			";
			$checkIfHouseholdScoreExist = $dbh->prepare($sqlCheckIfHouseholdScoreExist);
			$checkIfHouseholdScoreExist->bindParam(":household_id", $_GET["household_id"], PDO::PARAM_STR);
			$checkIfHouseholdScoreExist->bindParam(":score_type_id", $type, PDO::PARAM_INT);
			$checkIfHouseholdScoreExist->bindParam(":startDate", $startDate, PDO::PARAM_STR);
			$checkIfHouseholdScoreExist->bindParam(":endDate", $endDate, PDO::PARAM_STR);
			foreach($scoreTypesArray as &$value) {
				$type = $value;
			}
		}

		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}