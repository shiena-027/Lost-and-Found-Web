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
    <link rel="stylesheet" href="css/dashboard.css">
</head>

<body>
    <div id="main">
        <?php require_once(__DIR__.'/sidenav.php'); ?>
        <?php require_once(__DIR__.'/floatpostbtn.php'); ?>
        <header>
            <h1>Welcome,
                <?php echo htmlspecialchars($_SESSION['user']); ?>!
            </h1>
        </header>

        <main>
            <section>
                <h2>Dashboard</h2>
                <p>Here, you can manage your lost and found items, browse other reports, and communicate with other
                    users.</p>
            </section>
        </main>
        <?php require_once(__DIR__.'/navbar.php'); ?>
    </div>
</body>

</html>