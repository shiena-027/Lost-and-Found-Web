<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

// Fetch categories for the dropdown (Lost/Found)
$type_query = "SELECT DISTINCT type FROM items";
$type_result = mysqli_query($conn, $type_query);

// Handle search
$search_query = "SELECT * FROM items WHERE 1=1";  // Default query

// Apply filters based on user input
if (isset($_GET['search'])) {
    $title = $_GET['title'];
    $type = $_GET['type'];
    $location = $_GET['location'];

    if (!empty($title)) {
        $search_query .= " AND title LIKE '%" . mysqli_real_escape_string($conn, $title) . "%'";
    }

    if (!empty($type)) {
        $search_query .= " AND type = '" . mysqli_real_escape_string($conn, $type) . "'";
    }

    if (!empty($location)) {
        $search_query .= " AND location LIKE '%" . mysqli_real_escape_string($conn, $location) . "%'";
    }
}

$items_result = mysqli_query($conn, $search_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Browse Items</title>
</head>
<body>
<?php require_once(__DIR__.'/sidenav.php'); ?>
    <h2>Search and Browse Lost & Found Items</h2>

    <!-- Search Form -->
    <form action="search.php" method="GET">
        <label for="title">Keyword:</label>
        <input type="text" id="title" name="title" value="<?php echo isset($_GET['title']) ? htmlspecialchars($_GET['title']) : ''; ?>"><br><br>

        <label for="type">Category:</label>
        <select id="type" name="type">
            <option value="">All</option>
            <?php while ($row = mysqli_fetch_assoc($type_result)) { ?>
                <option value="<?php echo $row['type']; ?>" <?php echo isset($_GET['type']) && $_GET['type'] == $row['type'] ? 'selected' : ''; ?>>
                    <?php echo $row['type']; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>"><br><br>

        <button type="submit" name="search">Search</button>
    </form>

    <h3>Results:</h3>
    <?php if (mysqli_num_rows($items_result) > 0) { ?>
        <ul>
            <?php while ($item = mysqli_fetch_assoc($items_result)) { 
                // Fetch the user who posted the item
                $poster_id = $item['user_id'];
                $user_query = "SELECT * FROM users WHERE id = '$poster_id'";
                $user_result = mysqli_query($conn, $user_query);
                $user = mysqli_fetch_assoc($user_result);
            ?>
                <li>
                    <h4><?php echo htmlspecialchars($item['title']); ?> (<?php echo htmlspecialchars($item['type']); ?>)</h4>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    <p><strong>Posted on:</strong> <?php echo date("F j, Y", strtotime($item['created_at'])); ?></p>
                    <p><img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" width="100"></p>

                    <!-- Private Message Link -->
                    <p><a href="chat.php?user_id=<?php echo $user['id']; ?>">Chat</a></p>

                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>No items found matching your criteria.</p>
    <?php } ?>
</body>
</html>
<?php require_once(__DIR__.'/navbar.php'); ?>
<?php
// Close database connection
mysqli_close($conn);
?>
