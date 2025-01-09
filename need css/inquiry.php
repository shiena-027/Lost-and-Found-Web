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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="assets/styles.css">
    <link rel="stylesheet" href="inquiry-style.css">
</head>
<body>
    <h1>Contact Us</h1>

    <?php
    // Display success/error message
    if (isset($_SESSION['success'])) {
        echo "<div class='success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <form action="inquiry.php" method="POST">
        <textarea name="message" rows="5" placeholder="Your message" required></textarea><br>
        <button type="submit">Send Message</button>
    </form>
</body>
</html>
