<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		$returnArray = array();
		
		// Notes: Functions like MAX or other such things in MySQL, needs to be defined with the AS if you're going to be able to retrieve them from result set.
		$sqlRetrieveRank = "SELECT MAX(rank_rank_id)
			FROM household_ranks
			WHERE date_obtained IS NOT NULL
			AND household_household_id = :household_household_id";
		$retrieveRank = $dbh->prepare($sqlRetrieveRank);
		$retrieveRank->bindParam(':household_household_id', $household_id = 0, PDO::PARAM_INT);
		$retrieveRank->execute();
		$rank = null;
		$rankInformation = $retrieveRank->fetchAll(PDO::FETCH_ASSOC);
		$rank = $rankInformation['rank'];
		echo $jsonRank=json_encode($rankInformation);
		
		
		$sqlRetrieveScore = "SELECT username, SUM(value) AS score
			FROM household AS HH
			INNER JOIN household_scores AS HS ON HH.household_id = HS.household_household_id
			WHERE DATE
			BETWEEN :startOfMonth
			AND CURDATE()
			AND NOT score_type_score_type_id = 0
			GROUP BY username
			ORDER BY score DESC";
		$retrieveScore = $dbh->prepare($sqlRetrieveScore);
		$retrieveScore->bindParam(':startOfMonth', $date = date('o-m').'-01', PDO::PARAM_STR);
		$retrieveScore->execute();
		$userScores = $retrieveScore->fetchAll(PDO::FETCH_ASSOC);
		echo $jsonUserScores = json_encode($userScores);
		
		
		// Close connection
		$dbh = null;
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>