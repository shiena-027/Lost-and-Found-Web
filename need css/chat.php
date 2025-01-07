<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

// Fetch the list of users who have sent messages to the logged-in user
$logged_in_user_id = $_SESSION['user_id'];
$user_query = "SELECT DISTINCT u.id, u.name FROM users u 
               JOIN messages m ON (m.sender_id = u.id AND m.recipient_id = '$logged_in_user_id') 
               OR (m.sender_id = '$logged_in_user_id' AND m.recipient_id = u.id)
               WHERE u.id != '$logged_in_user_id'"; // Exclude the logged-in user from the list
$user_result = mysqli_query($conn, $user_query);

// Check if we are in chat mode (if a recipient ID is provided)
if (isset($_GET['user_id'])) {
    $recipient_id = $_GET['user_id'];

    // Fetch recipient data
    $recipient_query = "SELECT * FROM users WHERE id = '$recipient_id'";
    $recipient_result = mysqli_query($conn, $recipient_query);
    if ($recipient_result && mysqli_num_rows($recipient_result) > 0) {
        $recipient = mysqli_fetch_assoc($recipient_result);
    } else {
        echo "Recipient not found.";
        exit();
    }

    // Fetch the chat history
    $message_query = "SELECT * FROM messages WHERE (sender_id = '$logged_in_user_id' AND recipient_id = '$recipient_id') 
                      OR (sender_id = '$recipient_id' AND recipient_id = '$logged_in_user_id') ORDER BY timestamp ASC";
    $message_result = mysqli_query($conn, $message_query);

    // Handle new message submission
    if (isset($_POST['send_message'])) {
        $message = mysqli_real_escape_string($conn, $_POST['message']);
        
        if (!empty($message)) {
            // Insert the new message into the messages table
            $insert_message_query = "INSERT INTO messages (sender_id, recipient_id, message) 
                                     VALUES ('$logged_in_user_id', '$recipient_id', '$message')";
            mysqli_query($conn, $insert_message_query);
            
            // Insert a notification for the recipient
            $notification_message = "You have received a new message from " . htmlspecialchars($recipient['name']);
            $insert_notification_query = "INSERT INTO notifications (user_id, message, is_read) 
                                          VALUES ('$recipient_id', '$notification_message', 0)";
            mysqli_query($conn, $insert_notification_query);
    
            // Redirect to the same page to refresh the chat
            header("Location: chat.php?user_id=$recipient_id");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="chat-container">
        <!-- User List -->
        <div class="user-list">
            <h3>Your Chats</h3>
            <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
                <a href="chat.php?user_id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></a>
            <?php } ?>
        </div>

        <!-- Chat Box -->
        <?php if (isset($recipient)) { ?>
            <div class="chat-box">
                <h3>Chat with <?php echo htmlspecialchars($recipient['name']); ?></h3>

                <div class="messages">
                    <?php while ($message = mysqli_fetch_assoc($message_result)) { ?>
                        <div class="message <?php echo $message['sender_id'] == $logged_in_user_id ? 'sent' : 'received'; ?>">
                            <p><?php echo htmlspecialchars($message['message']); ?></p>
                            <small><?php echo date("F j, Y, g:i a", strtotime($message['timestamp'])); ?></small>
                        </div>
                    <?php } ?>
                </div>

                <!-- Message Input Form -->
                <form action="chat.php?user_id=<?php echo $recipient_id; ?>" method="POST" class="message-form">
                    <input type="text" name="message" placeholder="Type your message..." required>
                    <button type="submit" name="send_message">Send</button>
                </form>
            </div>
        <?php } ?>
    </div>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
