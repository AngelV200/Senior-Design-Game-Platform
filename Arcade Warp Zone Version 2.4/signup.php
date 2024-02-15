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
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST['password'];

    // Hash the password (makes it more secured)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Email or username already exists in the database
        $response['message'] = "Email or username already exists.";
        $response['status'] = "error";
    } else {
        // Insert data into database
        $query = "INSERT INTO users (first_name, last_name, email, username, password) VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($query);

        $stmt->bind_param("sssss", $first_name, $last_name, $email, $username, $hashed_password);

        if ($stmt->execute()) {
            $response['message'] = "User registered successfully!";
            $response['status'] = "success";
        } else {
            $response['message'] = "Error: " . $stmt->error;
            $response['status'] = "error";
        }
    }

    $stmt->close();
}


// Close the database connection
$conn->close();

// Return the response in JSON format
echo json_encode($response);
