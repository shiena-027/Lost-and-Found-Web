<?php
session_start();
session_unset(); // Clear session variables
session_destroy(); // Destroy the session
header("Location: auth.php"); // Redirect to login/home page
exit();
?>
