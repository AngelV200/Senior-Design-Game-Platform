<?php
// Start Session
session_start();

// Database variables and information
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

// Check if the username is set in the session
if (isset($_SESSION['username']) && isset($_POST['new_profile_picture'])) {
    // Access the username stored in the session
    $currentUser = $_SESSION['username'];
    $newProfilePicture = $_POST['new_profile_picture'];

    // Update query
    $updateQuery = "UPDATE users SET profile_picture = ? WHERE username = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ss", $newProfilePicture, $currentUser);
    $updateStmt->execute();
    $updateStmt->close();
}

// Close the database connection
$conn->close();
?>