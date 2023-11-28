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

//Response variable
$response = array();

// Retrieve submitted data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usernameInput = $_POST["username"];
    $passwordInput = $_POST['password'];

    // Retrieve the hashed password from the database for the given username
    $query = "SELECT username, password FROM users WHERE username = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $usernameInput);
    $stmt->execute();
    $stmt->bind_result($username, $hashed_password);


    if ($stmt->fetch() && password_verify($passwordInput, $hashed_password)) {
        $response['message'] = "Login Successful!";
        $response['status'] = "success";
    } else {
        $response['message'] = "Invalid Credentials.";
        $response['status'] = "error";
    }

}

// Close the database connection
$conn->close();

// Return the response in JSON format
echo json_encode($response);