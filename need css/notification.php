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
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h2 {
            color: #333;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            background-color: #f9f9f9;
            margin: 10px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .notification-header {
            font-weight: bold;
        }
        .notification-date {
            font-size: 0.9em;
            color: gray;
        }
        .new-notification {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h2>Your Notifications</h2>

    <?php if ($result && $result->num_rows > 0): ?>
        <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <p class="notification-header"><?php echo htmlspecialchars($row['message']); ?></p>
                <p class="notification-date">Posted on: <?php echo $row['created_at']; ?></p>
                <?php if ($row['is_read'] == 0): ?>
                    <span class="new-notification">(New)</span>
                <?php endif; ?>
                <br>
                <a href="notifications.php?notification_id=<?php echo $row['id']; ?>">Mark as Read</a>
            </li>
        <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No new notifications. You might not have any notifications yet.</p>
    <?php endif; ?>

</body>
</html>

<?php
// Close database connection
$stmt->close();
?>
