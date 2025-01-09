<?php include 'includes/db.php'; ?>
<?php session_start(); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Lost & Found Platform</title>
    <link rel="stylesheet" href="css/index.css">
</head>

<body>
    <div>
        <ul class="navbar">
            <img src="css/img/logo.png">
            <li class="navitem"><a href="faq.php">FAQ</a></li>
            <li class="navitem"><a href="about.php">About Us</a></li>
            <li class="navitem"><a href="contact.php">Contact Us</a></li>

        </ul>
    </div>
    <div class="maincontainer">
        <div>
            <span class="maintitle">Welcome to Lost & Found Platform!</span></br>
            <span class="mainsub">Your go-to platform for finding lost or found items.</span><br>
            <button><a href="auth.php">Come Join Us!</a></button>
        </div>


    </div>
</body>

</html>