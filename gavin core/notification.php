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

// Check if the query was successful and notifications were retrieved
if ($result === false) {
    die('MySQL query failed: ' . $stmt->error);  // If the query fails, output error
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Notifications</title>
    <link rel="stylesheet" href="css/notification.css">
</head>

<body>
<?php require_once(__DIR__.'/sidenav.php'); ?>
    <h2>Your Notifications</h2>

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="messages">
        <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <p class="notification-header">
                    <?php echo htmlspecialchars($row['message']); ?>
                </p>
                <p class="notification-date">Posted on:
                    <?php echo $row['created_at']; ?>
                </p>
                <?php if ($row['is_read'] == 0): ?>
                <span class="new-notification">(New)</span>
                <?php endif; ?>
                <br>
                <a href="notifications.php?notification_id=<?php echo $row['id']; ?>">Mark as Read</a>
            </li>
            <?php endwhile; ?>
        </ul>
    </div>
    <?php else: ?>
    <p>No new notifications. You might not have any notifications yet.</p>
    <?php endif; ?>
    <?php require_once(__DIR__.'/navbar.php'); ?>
</body>

</html>
<?php
// Close database connection
$stmt->close();
?>