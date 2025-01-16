<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Handle item deletion
if (isset($_GET['delete_item_id'])) {
    $delete_item_id = intval($_GET['delete_item_id']);
    
    // Delete the item from the database
    $delete_query = "DELETE FROM items WHERE id = ? AND user_id = ?";
    $delete_stmt = mysqli_prepare($conn, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "ii", $delete_item_id, $user_id);
    
    if (mysqli_stmt_execute($delete_stmt)) {
        // Redirect to the same page to refresh the list
        header('Location: profile.php');
        exit();
    } else {
        echo "<p class='error-message'>Failed to delete the item. Please try again.</p>";
    }
    mysqli_stmt_close($delete_stmt);
}

// Fetch user's items from the database
$query = "SELECT * FROM items WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="assets/profile.css">
    <link rel="stylesheet" href="assets/no_message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<?php include('web_navbar.php');?>
<body>
    <main class="container">
        <h2 class="section-title">Posted Items</h2>
        <div class="items-container">
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($item = mysqli_fetch_assoc($result)): ?>
                    <div class="item-card">
                        <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                        <p><strong>Description: </strong><?php echo htmlspecialchars($item['description']); ?></p>
                        <p><strong>Location: </strong> <?php echo htmlspecialchars($item['location']); ?></p>
                        <p>
                            <strong>Status:</strong> 
                            <span class="status <?php echo strtolower(htmlspecialchars($item['type'])); ?>">
                                <?php echo htmlspecialchars($item['type']); ?>
                            </span>
                        </p>

                        <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <div class="item-actions">
                            <a href="edit_item.php?item_id=<?php echo $item['id']; ?>" class="btn btn-edit">Edit</a>
                            <a href="profile.php?delete_item_id=<?php echo $item['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-items-message">You have no posted items.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
<?php 
mysqli_stmt_close($stmt);
?>
