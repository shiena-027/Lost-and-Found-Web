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

// Fetch user information from the database
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $user = mysqli_fetch_assoc($result);
} else {
    echo "Error fetching user details: " . mysqli_error($conn);
    exit();
}

// Handle form submission
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $image = $_FILES['image']['name'];

    // Process image upload
    $image_temp = $_FILES['image']['tmp_name'];
    $image_path = 'uploads/' . $image;
    move_uploaded_file($image_temp, $image_path);

    // Get coordinates from the form
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

    // Insert item into the database
    $query = "INSERT INTO items (user_id, title, description, location, type, image, latitude, longitude) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    // Bind parameters - ensure types match the data
    $stmt->bind_param("ssssssdd", $user_id, $title, $description, $location, $type, $image, $latitude, $longitude);

    echo "<div class='item'>";
    echo "<h3>" . $item['name'] . "</h3>"; // Item name
    echo "<p>" . $item['description'] . "</p>"; // Item description
    echo "<p>Posted by: " . $poster['name'] . "</p>"; // Poster name
    echo "<a href='chat.php?user_id=$poster_id'>Private Message</a>"; // Message link
    echo "</div>";
    // Execute the statement
    if ($stmt->execute()) {
        // Insert notification for the user
        $message = "Your item '$title' has been successfully posted!";
        $notif_query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $notif_stmt = $conn->prepare($notif_query);
        $notif_stmt->bind_param("is", $user_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        echo "Item posted successfully!";
        header('Location: search.php'); // Redirect to search page after successful post
    } else {
        echo "Error posting item: " . $stmt->error;
    }

    $stmt->close();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Item - Lost and Found</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map { height: 400px; }
    </style>
</head>
<body>
    <h2>Post Item</h2>
    <form action="post_item.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="type">Type:</label><br>
        <select id="type" name="type" required>
            <option value="Lost">Lost</option>
            <option value="Found">Found</option>
        </select><br><br>

        <label for="location">Location:</label><br>
        <input type="text" id="location" name="location" readonly><br><br>

        <!-- Display the Map -->
        <div id="map"></div><br>

        <label for="image">Image:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">

        <button type="submit" name="submit">Post Item</button>
    </form>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Initialize the map
        var map = L.map('map').setView([14.5995, 120.9842], 12); // Default location: Manila, Philippines

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add a marker for the initial location
        var marker = L.marker([14.5995, 120.9842]).addTo(map);

        // Update location input field when map is clicked
        map.on('click', function(e) {
            var lat = e.latlng.lat;
            var lng = e.latlng.lng;

            // Move the marker to the clicked location
            marker.setLatLng(e.latlng);

            // Use Nominatim to reverse geocode and get location name
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    // Get the location name from the response
                    var locationName = data.display_name;
                    document.getElementById('location').value = locationName;

                    // Set latitude and longitude values in hidden fields
                    document.getElementById('latitude').value = lat;
                    document.getElementById('longitude').value = lng;
                })
                .catch(error => console.log(error));
        });
    </script>
</body>
</html>
