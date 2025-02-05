<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

$logged_in_user_id = $_SESSION['user_id'];

// Fetch the search term from GET or POST request (if any)
$search_term = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Modify the query to filter based on the search term
$user_query = "SELECT DISTINCT u.id, u.name FROM users u 
               JOIN messages m ON (m.sender_id = u.id AND m.recipient_id = '$logged_in_user_id') 
               OR (m.sender_id = '$logged_in_user_id' AND m.recipient_id = u.id)
               WHERE u.id != '$logged_in_user_id'";

// If a search term is provided, add a WHERE clause for name filtering
if ($search_term) {
    $user_query .= " AND u.name LIKE '%$search_term%'";
}

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
    $profile_photo = isset($user['photo']) && !empty($user['photo']) ? $user['photo'] : 'css/img/user.png';

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/chat.css">
    <title>Chat</title>
    
</head>
<?php include('web_navbar.php'); ?>
<body>
<main="container">
<div class="chat-container">
    <!-- Side Navbar (User List) -->

    <div class="user-list" id="userList">
        <button class="close-btn" onclick="toggleNavbar()">×</button>
        <h3></h3>
        <div class="search-bar">
            <input type="text" id="search-input" placeholder="Search Users...">
            <button onclick="searchUsers()"><i class="fas fa-search"></i></button>
        </div>

        <?php while ($user = mysqli_fetch_assoc($user_result)) { ?>
            <a href="chat.php?user_id=<?php echo $user['id']; ?>" onclick="removeImage()">
                <?php echo htmlspecialchars($user['name']); ?>
            </a>
        <?php } ?>
        <!-- Add if needed <div class="back-btn-container">
            <a href="dashboard.php" class="back-btn no-hover"> <img src="css/img/arrow-left.png" alt="Back" width="45" height="45px"/></a>
        </div> -->
    </div>

    <!-- Toggle (Hamburger) Button -->
    <button class="toggle-btn" onclick="toggleNavbar()"><i class="fas fa-search"></i>
    </button>

    <!-- Conditionally show the image on chat.php -->
    <?php if (!isset($recipient)) { ?>
        <div class="chat-placeholder">
        <p>Select a user to start chatting</p>
        </div>

    <?php } ?>

    <!-- Chat Box -->
    <?php if (isset($recipient)) { ?>
        <div class="chat-box">
    <h3 class="fixed-header">
        <img src="<?php echo htmlspecialchars($profile_photo); ?>" alt="<?php echo htmlspecialchars($user['name']); ?>'s Profile Picture" class="profile-img" >
        <?php echo htmlspecialchars($recipient['name']); ?>
    </h3> 
    <div class="messages">
        <?php while ($message = mysqli_fetch_assoc($message_result)) { ?>
            <div class="message <?php echo $message['sender_id'] == $logged_in_user_id ? 'sent' : 'received'; ?>">
                <p><?php echo htmlspecialchars($message['message']); ?></p>
                <small><?php echo date("F j, Y, g:i a", strtotime($message['timestamp'])); ?></small>
            </div>
        <?php } ?>
    </div>

    <form action="chat.php?user_id=<?php echo $recipient_id; ?>" method="POST" class="message-form">
        <input type="text" name="message" placeholder="Type your message..." required>
        <button type="submit" name="send_message"><i class="fas fa-paper-plane"></i></button>
    </form>
</div>

    <?php } ?>
</div>
</main>
</body>
<script src="css/js/chat.js"></script>

</html>
