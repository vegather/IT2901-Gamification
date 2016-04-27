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
		
		
		if (isset($_GET["household_id"])) {	
			$sqlCheckIfHouseholdScoreExist = "
			SELECT EXISTS
				(SELECT HS.score_type_score_type_id
				FROM household_scores AS HS
				WHERE HS.household_household_id = :householdID
				AND HS.score_type_score_type_id = :scoreTypeID
				AND HS.date BETWEEN :start_date AND :endDate)
			";
		}

		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}