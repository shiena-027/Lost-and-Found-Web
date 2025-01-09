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

// Fetch categories for the dropdown (Lost/Found)
$type_query = "SELECT DISTINCT type FROM items";
$type_result = mysqli_query($conn, $type_query);

// Handle search
$search_query = "SELECT * FROM items WHERE 1=1";  // Default query
$items_result = mysqli_query($conn, $search_query);
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
    <?php require_once(__DIR__.'/sidenav.php'); ?>
    <?php require_once(__DIR__.'/floatpostbtn.php'); ?>
    <?php if (mysqli_num_rows($items_result) > 0) { ?>

    <?php while ($item = mysqli_fetch_assoc($items_result)) { 
                // Fetch the user who posted the item
                $poster_id = $item['user_id'];
                $user_query = "SELECT * FROM users WHERE id = '$poster_id'";
                $user_result = mysqli_query($conn, $user_query);
                $user = mysqli_fetch_assoc($user_result);
            ?>
    <div class="item-card">
        <ul>
            <div class="item-title">

                <p>
                    <li>
                        <?php echo htmlspecialchars($item['title']); ?> (
                        <?php echo htmlspecialchars($item['type']); ?>)
                    </li>
                </p>

            </div>
            <li>
                <img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" class="itemimg">
            </li>

            <div class="desccontain">
                <div class="itemloc">
                    <li>
                        <p> <img src="css/img/location.svg">
                            <?php echo htmlspecialchars($item['location']); ?>
                        </p>
                    </li>
                </div>
                <div class="itemdesc">
                    <li>
                        <p>
                            <?php echo htmlspecialchars($item['description']); ?>
                        </p>
                    </li>
                </div>
                <div class="itemdate">
                    <li>
                        <p>
                            <?php echo date("F j, Y", strtotime($item['created_at'])); ?>
                        </p>
                    </li>
                </div>

                <!-- Private Message Link -->
                <div class="itemchat">
                    <a href="chat.php?user_id=<?php echo $user['id']; ?>"><img src="css/img/comment.svg"></a>
                </div>
            </div>
        </ul>
    </div>

    <?php } ?>


    <?php } else { ?>
    <p>No items found matching your criteria.</p>
    <?php } ?>
</body>

</html>