<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit();
}

$user = $_SESSION['user']; // Get user ID from session

// Fetch user information from the database (optional)
$query = "SELECT * FROM users WHERE id = '$user'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <header>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
        <nav>
            <ul>
                <li><a href="post_item.php">Post Item</a></li>
                <li><a href="search.php">Search & Browse</a></li>
                <li><a href="notification.php">Notifications</a></li>
                <li><a href="chat.php">Chat</a></li>
                <li><a href="faq.php">FAQ</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="inquiry.php">Contact Us</a></li>
                <li><a href="terms.php">Terms & Privacy Policy</a></li>
                 <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h2>Dashboard</h2>
            <p>Here, you can manage your lost and found items, browse other reports, and communicate with other users.</p>
        </section>
    </main>

</body>
</html>
