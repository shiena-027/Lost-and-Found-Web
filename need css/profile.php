<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in (check if user_id exists in session)
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Fetch user's items from the database
$query = "SELECT * FROM items WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    echo "<h2>Your Posted Items</h2>";
    while ($item = mysqli_fetch_assoc($result)) {
        echo "<div class='item'>";
        echo "<h3>" . $item['title'] . "</h3>";
        echo "<p>" . $item['description'] . "</p>";
        echo "<p>Location: " . $item['location'] . "</p>";
        echo "<p>Type: " . $item['type'] . "</p>";
        echo "<img src='uploads/" . $item['image'] . "' alt='" . $item['title'] . "'><br>";
        echo "<a href='edit_item.php?item_id=" . $item['id'] . "'>Edit</a> | ";
        echo "<a href='delete_item.php?item_id=" . $item['id'] . "'>Delete</a>";
        echo "</div>";
    }
} else {
    echo "You have no posted items.";
}

$stmt->close();
?>
