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
		
		
		if (isset($_GET["household_id"])) {
			$household_id = $_GET["household_id"];
			
			
			$sqlRetrieveHouseholdRanks = "
				SELECT ((s.score-cr.requirement)/(nr.requirement-cr.requirement)) AS percent, nr.rank_id
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
				ORDER BY cr.rank_id, nr.rank_id
			";
			$retrieveHouseholdRanks = $dbh->prepare($sqlRetrieveHouseholdRanks);
			$retrieveHouseholdRanks->bindParam(":household_household_id", $household_id, PDO::PARAM_INT);
			$retrieveHouseholdRanks->execute();
			$householdRanks = $retrieveHouseholdRanks->fetchAll(PDO::FETCH_ASSOC);
			
			
			$jsonHouseholdRanks = json_encode($householdRanks);
			
			if (isset($_GET["callback"])) {
				$callback = $_GET["callback"];
				echo $callback.'({"ranks":'.$jsonHouseholdRanks.'});';
			}
			else {
				echo $jsonHouseholdRanks;
			}
		} else {
			echo "Need the household_id of household making request to retrieve ranks!";
		}
		
		
		//Close connection
		$dbh = null;
		
		
	} catch(PDOException $e) {
		echo '<h1>An error has occured.</h1><pre>', $e->getMessage(), '</pre>';
	}
?>