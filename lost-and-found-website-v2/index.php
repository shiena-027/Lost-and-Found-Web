<?php session_start(); ?>
<?php include('web_navbar.php');
include 'navbar.php'; 
include 'sidenav.php'; 
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found Platform</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <h1>Welcome to the Lost & Found Platform</h1>
    <p>Your go-to platform for finding lost or found items.</p>
    <nav>
        <a href="faq.php">FAQ</a>
        <a href="about.php">About Us</a>
        <a href="contact.php">Contact</a>
        <a href="auth.php">Login</a>
    </nav>
</body>

<?php
include 'includes/footer.php';
?>

</html>
