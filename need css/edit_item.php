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

// Check if item_id is provided to edit
if (!isset($_GET['item_id'])) {
    echo "Item not found!";
    exit();
}

$item_id = $_GET['item_id'];

// Fetch item details from the database
$query = "SELECT * FROM items WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $item = mysqli_fetch_assoc($result);
} else {
    echo "Item not found or you don't have permission to edit it.";
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $image = $_FILES['image']['name'];

    // Process image upload if a new image is provided
    if ($image) {
        $image_temp = $_FILES['image']['tmp_name'];
        $image_path = 'uploads/' . $image;
        move_uploaded_file($image_temp, $image_path);
    } else {
        // Keep the existing image if no new image is uploaded
        $image = $item['image'];
    }

    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    // Validate latitude and longitude
    if (empty($latitude) || empty($longitude)) {
        echo "<script>
                alert('Error: Latitude and longitude must be provided.');
                window.history.back();
              </script>";
        exit();
    }

    // Update the item in the database
    $query = "UPDATE items SET title = ?, description = ?, location = ?, type = ?, image = ?, latitude = ?, longitude = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssddi", $title, $description, $location, $type, $image, $latitude, $longitude, $item_id, $user_id);

    if ($stmt->execute()) {
        echo "Item updated successfully!";
        header('Location: profile.php'); // Redirect to profile page after successful edit
    } else {
        echo "Error updating item: " . $stmt->error;
    }

    $stmt->close();
}

// Fallback to default coordinates if latitude/longitude are missing
$latitude = !empty($item['latitude']) ? $item['latitude'] : 14.5995; // Default: Manila Latitude
$longitude = !empty($item['longitude']) ? $item['longitude'] : 120.9842; // Default: Manila Longitude
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="edit_item-style.css">
</head>
<body>
    <h2>Edit Item</h2>
    <form action="edit_item.php?item_id=<?php echo $item['id']; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($item['title']); ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($item['description']); ?></textarea>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="Lost" <?php if ($item['type'] == 'Lost') echo 'selected'; ?>>Lost</option>
            <option value="Found" <?php if ($item['type'] == 'Found') echo 'selected'; ?>>Found</option>
            <option value="Claimed" <?php if ($item['type'] == 'Claimed') echo 'selected'; ?>>Claimed</option>
        </select>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($item['location']); ?>" readonly>

        <div id="map"></div>

        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <input type="hidden" id="latitude" name="latitude" value="<?php echo $latitude; ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $longitude; ?>">

        <button type="submit" name="submit">Update Item</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var latitude = <?php echo json_encode($latitude); ?>;
            var longitude = <?php echo json_encode($longitude); ?>;

            // Initialize Map
            var map = L.map('map').setView([latitude, longitude], 12);

            // Add OpenStreetMap Layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            // Add Marker
            var marker = L.marker([latitude, longitude]).addTo(map);

            // Invalidate map size
            setTimeout(() => map.invalidateSize(), 300);

            // Update Location on Click
            map.on('click', function(e) {
                var lat = e.latlng.lat;
                var lng = e.latlng.lng;
                marker.setLatLng(e.latlng);

                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;

                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                    .then(response => response.json())
                    .then(data => document.getElementById('location').value = data.display_name)
                    .catch(error => console.error(error));
            });
        });
    </script>
</body>
</html>
