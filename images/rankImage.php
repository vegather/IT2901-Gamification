<?php
	$servername = "localhost";
	$username = "root";
	$password = "cossmic";
	$dbname = "Cossmic";

	$conn = new mysqli($servername, $username, $password, $dbname);

	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	} 

	$sql = "SELECT * FROM `WidgetData` WHERE title='RankImage5';";
	$result = $conn->query($sql);

	header("Content-type: image/png");
	
	while ($row = $result->fetch_assoc()) {
		echo $row["rankImage"];
	}

//	echo $result->fetch_assoc()[0]["rankImage"];

	$conn->close();
?>
