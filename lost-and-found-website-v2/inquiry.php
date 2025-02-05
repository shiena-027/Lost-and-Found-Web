<?php
session_start();

// Include PHPMailer
require 'assets/PHPMailer/src/Exception.php';
require 'assets/PHPMailer/src/PHPMailer.php';
require 'assets/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the database connection file
require 'includes/db.php'; // This file should contain the database connection logic

// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_id']); // Check if 'user_id' is set in the session

// Default values for guests
$user = 'Guest';
$profile_pic = 'css/img/user.png'; // Default profile picture

if ($user_logged_in) {
    $user_id = $_SESSION['user_id']; // Retrieve the user ID from the session

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT name, photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_name, $db_profile_pic);

    if ($stmt->fetch()) {
        $name = !empty($db_name) ? $db_name : $name; // Use fetched name or default
        $profile_pic = !empty($db_profile_pic) ? $db_profile_pic : $profile_pic; // Use fetched profile pic or default
    }
    $stmt->close();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Capture the form data
    $name = $_POST['name']; // User's name
    $email = $_POST['email']; // User's email
    $message = $_POST['message']; // The message content

    // Recipient email address
    $recipient = "annocmpo@gmail.com";

    // Email subject
    $subject = "Inquiry Message from " . $name;

    // Email body
    $body = "<p><strong>Name:</strong> " . $name . "</p>";
    $body .= "<p><strong>Email:</strong> " . $email . "</p>";
    $body .= "<p><strong>Message:</strong> " . nl2br($message) . "</p>";

    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                         // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                     // Enable SMTP authentication
        $mail->Username = 'jnm.cmp@gmail.com';  // Set your SMTP email here
        $mail->Password = 'uqtizyeiyfqyinis';                  // SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           // Enable TLS encryption
        $mail->Port       = 587;                                      // TCP port to connect to

        // Recipients
        $mail->setFrom($email, $name);                               // Set the sender's email and name
        $mail->addAddress($recipient);                               // Add recipient (admin email)

        // Content
        $mail->isHTML(true);                                          // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Send the email
        $mail->send();
        $message_sent = "Message sent successfully!";
        $message_class = "success"; // Set the success class
    } catch (Exception $e) {
        $message_sent = "Failed to send message. Mailer Error: {$mail->ErrorInfo}";
        $message_class = "error"; // Set the error class
    }
}
?>
<?php include('web_navbar.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="css/inquiry.css">
</head>
<body>

    <form action="" method="POST">
    <!-- Contact Information Section -->
    <div class="box">
    <div class="logo-container">
        <img src="css/img/kitty.png" alt="Image Description" width="150" height="150" name="logo"/>
        <h3 style="color: #1da1f2;">We'd Love to Hear From You</h3>

    </div>

    <div class="contact-info">
        <p><strong>Address:</strong> 1234 Some Street, Some City, Some Country</p>
        <p><strong>Email:</strong> <a href="mailto:annocmpo@gmail.com">support@lostandfound.com</a></p>
        <p><strong>Phone:</strong> +1 (234) 567-8901</p>
    </div>

    <!-- Form Fields for Name, Email, and Message -->
    <label for="name">Name:</label>
    <input type="text" name="name" required>
    
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    
    <textarea name="message" rows="5" placeholder="Write your message here..." required></textarea><br>
    
    <button type="submit">Send Message</button>
    <?php
    // Display message after form submission
    if (isset($message_sent)) {
        echo "<div class='message-status'>$message_sent</div>";
    }
    ?>
    </div>

    <!-- Optional: Map Section (e.g., using Google Maps embed) -->
    <div class="map">
        <h3>Our Location</h3>
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6092.353951690452!2d120.99820237316161!3d14.574407162129646!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c9ed93f4c213%3A0x256db62ecb27be09!2sPolytechnic%20University%20of%20the%20Philippines%20-%20Sta.%20Mesa%2C%20Manila!5e0!3m2!1sen!2sph!4v1673964448707!5m2!1sen!2sph" 
            width="62%" 
            height="450" 
            style="border:0;" 
            allowfullscreen="" 
            loading="lazy">
        </iframe>
    </div>

    </form>

    <script>
        // Wait for the document to load
        window.onload = function() {
            // Check if there is a message displayed
            var message = document.querySelector('.message-status');
            
            // If message exists, hide it after 3 seconds
            if (message) {
                setTimeout(function() {
                    message.style.display = 'none';
                }, 3000); // Message will disappear after 3 seconds
            }
        }
    </script>

</body>
<?php
include 'includes/footer.php';
?>

</html>
