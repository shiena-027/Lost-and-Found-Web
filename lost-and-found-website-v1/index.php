<?php session_start(); ?>
<?php 
include('web_navbar.php');
include 'navbar.php'; 
include 'sidenav.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lost & Found Platform</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .profile-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 20px 0;
        }

        .profile {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 80%;
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: #f9f9f9;
        }

        .profile img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
        }

        .profile-info {
            flex: 1;
            margin: 0 20px;
        }

        .profile:nth-child(odd) .profile-img {
            order: 1;
        }

        .profile:nth-child(odd) .profile-info {
            order: 2;
            text-align: right;
        }

        .profile:nth-child(even) .profile-img {
            order: 2;
        }

        .profile:nth-child(even) .profile-info {
            order: 1;
            text-align: left;
        }
    </style>
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

    <div class="profile-container">
        <div class="profile">
            <div class="profile-img">
                <img src="assets/profile1.jpg" alt="Profile 1">
            </div>
            <div class="profile-info">
                <p><strong>Name:</strong> Gavin Marty Del Val</p>
                <p><strong>Nickname:</strong> "Fakefoureyes"</p>
                <p><strong>Email:</strong> john.doe@example.com</p>
            </div>
        </div>
        <div class="profile">
            <div class="profile-img">
                <img src="assets/profile2.jpg" alt="Profile 2">
            </div>
            <div class="profile-info">
                <p><strong>Name:</strong> Joan Mae Ocampo</p>
                <p><strong>Nickname:</strong>"JM"</p>
                <p><strong>Email:</strong> jane.smith@example.com</p>
            </div>
        </div>
        <div class="profile">
            <div class="profile-img">
                <img src="assets/profile3.jpg" alt="Profile 3">
            </div>
            <div class="profile-info">
                <p><strong>Name:</strong> Shiena Mae Miranda</p>
                <p><strong>Nickname:</strong> "Mai<3"</p>
                <p><strong>Email:</strong> mirandamimai984@gmail.com</p>
            </div>
        </div>
    </div>
</body>
</html>
