<?php
session_start();
include('includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header("Location: auth.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_id = $_SESSION['user_id']; // Logged in user
    $recipient_id = 1; // Admin's user_id (assuming admin has user_id = 1)
    $message = mysqli_real_escape_string($conn, $_POST['message']); // Sanitize message input

    // Insert the message into the messages table
    $query = "INSERT INTO messages (sender_id, recipient_id, message, timestamp) VALUES (?, ?, ?, NOW())";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "iis", $sender_id, $recipient_id, $message);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = "Message sent to the admin!";
        } else {
            $_SESSION['error'] = "Failed to send message. Please try again.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<?php include('web_navbar.php');
include 'navbar.php'; 
include 'sidenav.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="assets/inquiry.css">
</head>
<body>

    <form action="inquiry.php" method="POST">
    <!-- Contact Information Section -->
    <div class="box">
    <div class="logo-container">
        <img src="assets/img/kitty.png" alt="Image Description" width="150" height="150" name="logo"/>
    </div>
    <div class="contact-info">
        
        <p><strong>Address:</strong> 1234 Some Street, Some City, Some Country</p>
        <p><strong>Email:</strong> <a href="mailto:contact@example.com">support@lostandfound.com</a></p>
        <p><strong>Phone:</strong> +1 (234) 567-8901</p>
    </div>

    
        <textarea name="message" rows="5" placeholder="Your message" required></textarea><br>
        <button type="submit">Send Message</button>
    </div>

    <!-- Optional: Map Section (e.g., using Google Maps embed) -->
    <div class="map">
        <h3>Our Location</h3>
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15035.624561628266!2d121.13659462380847!3d14.69469578147093!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33bd57606d59f1b3%3A0x9602b6b1e22a2079!2sMontalban%2C%20Rizal%2C%20Philippines!5e0!3m2!1sen!2sus!4v1675427240781!5m2!1sen!2sus"
                width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>

    </form>

    
</body>
</html>
