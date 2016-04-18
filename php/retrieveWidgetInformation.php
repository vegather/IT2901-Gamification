<?php
	// Connection info for database
	$hostname = 'localhost';
	$username = 'root'; //Temporarily for testing purposes, create a MySQL user for this later
	$password = 'cossmic'; // same as above
	$database = 'CoSSMunity';
	
	// Connection to the database
	try {
		$dbh = new PDO('mysql:host='.$hostname.';dbname='.$database, $username, $password);
		
		
		$sqlRetrieveRank = "SELECT MAX(rank_rank_id)
			FROM household_ranks
			WHERE date_obtained IS NOT NULL
			AND household_household_id = :household_household_id";
		$retrieveRank = $dbh->prepare($sqlRetrieveRank);
		$retrieveRank->bindParam(':household_household_id', $insert HTML method['household_id'], PDO::PARAM_INT)
		$retrieveRank->execute();
		while ($rankInformation = $retrieveRank->fetch(PDO::FETCH_ASSOC)) {
			echo $rankInformation['rank'];
		}
		
		
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
		$retrieveScore->bindParam(':startOfMonth', date('o-m').'-01', PDO::PARAM_STR);
		$retrieveScore->execute();
		while ($userScores = $retrieveScore->fetch(PDO::FETCH_ASSOC)) {
			echo $userScores['username'].' '.$userScores['score'];
		}
		
		
		// Close connection
		$dbh = null;
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>