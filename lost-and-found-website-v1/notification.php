<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');  // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch notifications from the database
$query = "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($query);

if ($stmt === false) {
    die('MySQL prepare statement failed: ' . $conn->error);  // If the query fails, output error
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php
include 'navbar.php'; 
include 'sidenav.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Notifications</title>
    <link rel="stylesheet" href="assets/notification.css">
    <link rel="stylesheet" href="assets/no_message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php include('web_navbar.php'); ?>
    <div class="container">
        <h2>&nbsp;</h2>

        <?php if ($result && $result->num_rows > 0): ?>
<ul class="notification-list">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="notification-item">
            <?php if ($row['notification_type'] == 'message'): ?>
                <!-- If it's a message notification, wrap the header in a link -->
                <a href="chat.php?user_id=<?php echo $row['user_id']; ?>" class="notification-header">
                    <?php echo htmlspecialchars($row['message']); ?>
                </a>
            <?php else: ?>
                <!-- If it's another type of notification, display it normally -->
                <p class="notification-header"><?php echo htmlspecialchars($row['message']); ?></p>
            <?php endif; ?>
            
            <p class="notification-date">Posted on: <?php echo $row['created_at']; ?></p>
            
            <?php if ($row['is_read'] == 0): ?>
                <span class="new-notification">(New)</span>
            <?php endif; ?>

            <br>
        </li>
    <?php endwhile; ?>
</ul>

        <?php else: ?>
            <p class="no-items-message">No new notifications. You might not have any notifications yet.</p>
        <?php endif; ?>
    </div>

</body>
</html>

<?php
// Close database connection
$stmt->close();
?>
