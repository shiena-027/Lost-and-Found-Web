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
$search_query = "SELECT * FROM items WHERE 1=1 AND type != 'Claimed'";  // Exclude Claimed items by default

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
    <title>Search</title>
    <link rel="stylesheet" href="assets/search.css">
    <link rel="stylesheet" href="assets/no_message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<?php include('web_navbar.php');
include 'navbar.php'; 
include 'sidenav.php'; 
?>
<h2>Search</h2>

<form action="search.php" method="GET">
    <div class="form-row">
        <label for="title">Keyword:</label>
        <input type="text" id="title" name="title" value="<?php echo isset($_GET['title']) ? htmlspecialchars($_GET['title']) : ''; ?>">

        <label for="type">Category:</label>
        <select id="type" name="type">
            <option value="">All</option>
            <?php while ($row = mysqli_fetch_assoc($type_result)) { 
                if ($row['type'] != 'Claimed') { ?>
                    <option value="<?php echo $row['type']; ?>" <?php echo isset($_GET['type']) && $_GET['type'] == $row['type'] ? 'selected' : ''; ?>>
                        <?php echo $row['type']; ?>
                    </option>
            <?php } } ?>
        </select>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>">

        <button type="submit" name="search">Search</button>
    </div>
</form>


    <?php if (mysqli_num_rows($items_result) > 0) { ?>
        <ul class="items-list">
            <?php while ($item = mysqli_fetch_assoc($items_result)) { 
                // Fetch the user who posted the item
                $poster_id = $item['user_id'];
                $user_query = "SELECT * FROM users WHERE id = '$poster_id'";
                $user_result = mysqli_query($conn, $user_query);
                $user = mysqli_fetch_assoc($user_result);
            ?>
                <li class="item-card">
                
                <h4 class="status <?php echo strtolower(htmlspecialchars($item['type'])); ?>">
                    <i class="fa fa-exclamation-circle"></i>
                    <strong>Status:</strong> <?php echo htmlspecialchars($item['type']); ?>
                </h4>

                <h4 class="status <?php echo htmlspecialchars($item['type']); ?>">
                    <?php echo htmlspecialchars($item['title']); ?> 
                </h4>

                    <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    <p><strong>Posted on:</strong> <?php echo date("F j, Y", strtotime($item['created_at'])); ?></p>
                    <p><a href="#" class="zoom-link"><img src="uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="Item Image" class="zoom-image" width="100"></a></p>

                    <!-- Private Message Link -->
                    <p><a href="chat.php?user_id=<?php echo $user['id']; ?>&message=Hi" class="chat-link"><i class="fas fa-comment"></i> Chat</a></p>


                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p class="no-items-message">No items found matching your criteria.</p>
    <?php } ?>
    
    <!-- Modal for Image Zoom -->
    <div id="image-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <img id="modal-image" src="" alt="Zoomed Image">
        </div>
    </div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Open modal with image
        $('.zoom-link').on('click', function() {
            var imgSrc = $(this).find('img').attr('src');
            $('#modal-image').attr('src', imgSrc);
            $('#image-modal').show();
        });

        // Close the modal
        $('.close-btn').on('click', function() {
            $('#image-modal').hide();
        });

        // Close the modal if clicked outside the image
        $('#image-modal').on('click', function(e) {
            if ($(e.target).is('#image-modal')) {
                $('#image-modal').hide();
            }
        });
    });
</script>
</body>
</html>

<?php
// Close database connection
mysqli_close($conn);
?>
