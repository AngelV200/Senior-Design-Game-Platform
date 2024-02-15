<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Redirect to the login page or any other page after logout
header("Location: login.html");
exit();
