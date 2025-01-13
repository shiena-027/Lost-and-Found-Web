<?php
// Database credentials
$servername = "localhost"; 
$username = "root";         
$password = "";             
$dbname = "lost_and_found"; 

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

