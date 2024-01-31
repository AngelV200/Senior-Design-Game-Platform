<?php
// update_score.php

// Database variables and information (similar to your existing code)
$host = "arcadewarpzone.ccaow2uqh8ko.us-west-1.rds.amazonaws.com";
$db_username = "admin";
$db_password = "awz12345+";
$db = "awz";

// Establish a database connection
$conn = new mysqli($host, $db_username, $db_password, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve parameters from the AJAX request
$gameName = $_POST['game_name'];
$userID = $_POST['user_id'];

// Check if the user already has a record for the game in the game_scores table
$checkUserQuery = "SELECT * FROM game_scores WHERE user_id = '$userID' AND game_name = '$gameName'";
$result = $conn->query($checkUserQuery);

// Check for errors in the query
if (!$result) {
    echo "Error checking user record: " . $conn->error;
} else {
    // If user not in the table, create a new row; otherwise, update the current score
    if ($result->num_rows == 0) {
        $createScoreQuery = "INSERT INTO game_scores (user_id, game_name, score) VALUES ('$userID', '$gameName', 1)";
        $conn->query($createScoreQuery);
        exit();
    } else {
        $updateScoreQuery = "UPDATE game_scores SET score = score + 1 WHERE user_id = '$userID' AND game_name = '$gameName'";
        $conn->query($updateScoreQuery);
        exit();
    }
}

// Close the database connection
$conn->close();

?>
