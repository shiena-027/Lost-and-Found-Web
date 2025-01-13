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

// Check if the item ID is passed for editing
if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];

    // Fetch the existing item from the database
    $query = "SELECT * FROM items WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $item_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) > 0) {
        $item = mysqli_fetch_assoc($result);
    } else {
        echo "Item not found or you do not have permission to edit this item.";
        exit();
    }

} else {
    echo "No item specified for editing.";
    exit();
}

// Handle form submission for updating the item
if (isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $type = $_POST['type'];
    $location = $_POST['location'];
    $image = $_FILES['image']['name'];

    // Process the image uploaded via the camera (base64 encoded)
    $captured_image = $_POST['captured_image']; // base64 string from the camera

    // Check if there is a captured image
    if (!empty($captured_image)) {
        // Remove the base64 header (data:image/png;base64,)
        $image_data = explode(',', $captured_image)[1];

        // Decode the base64 string into binary data
        $image_data = base64_decode($image_data);

        // Generate a unique filename for the image
        $image_name = uniqid('camera_image_') . '.png';

        // Set the path for the image
        $image_path = 'uploads/' . $image_name;

        // Save the image to the 'uploads' directory
        file_put_contents($image_path, $image_data);

        // Update the $image variable to hold the new image name
        $image = $image_name;
    } else {
        // If no image is captured, use the existing image or handle the uploaded file
        if ($_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
            $image = $item['image']; // Keep the old image if no new image is uploaded
        } else {
            $image_temp = $_FILES['image']['tmp_name'];
            $image_path = 'uploads/' . $image;
            move_uploaded_file($image_temp, $image_path);
        }
    }

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

    // Update the item in the database
    $query = "UPDATE items SET title = ?, description = ?, location = ?, type = ?, image = ?, latitude = ?, longitude = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssddi", $title, $description, $location, $type, $image, $latitude, $longitude, $item_id, $user_id);

    if ($stmt->execute()) {
        // Insert notification for the user
        $message = "Your item '$title' has been successfully updated!";
        $notif_query = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
        $notif_stmt = $conn->prepare($notif_query);
        $notif_stmt->bind_param("is", $user_id, $message);
        $notif_stmt->execute();
        $notif_stmt->close();

        echo "Item updated successfully!";
        header('Location: search.php'); // Redirect to search page after successful update
    } else {
        echo "Error updating item: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/forms.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include('web_navbar.php'); ?>
<?php include('sidenav.php'); ?>
<main class="form-container">
    <form action="edit_item.php?item_id=<?php echo $item_id; ?>" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" value="<?php echo $item['title']; ?>" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?php echo $item['description']; ?></textarea>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="Lost" <?php echo $item['type'] == 'Lost' ? 'selected' : ''; ?>>Lost</option>
            <option value="Found" <?php echo $item['type'] == 'Found' ? 'selected' : ''; ?>>Found</option>
            <option value="Claimed" <?php if ($item['type'] == 'Claimed') echo 'selected'; ?>>Claimed</option>
        </select>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" value="<?php echo $item['location']; ?>" readonly>
       
        <div id="map"></div><br>

        <!-- Image Upload -->
        <div class="camera-container">
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*">
        
        <!-- Camera Upload -->
        <button type="button" id="start-camera" class="camera-button">
            <i class="fas fa-camera"></i>
        </button>
           
        </div>
        <div id="camera-dropdown" class="camera-dropdown">
            <p style="font-size: 12px; font-weight: bold; color: red; text-align: center; margin: 20px;">Please ensure that your camera is enabled to use the capture feature. If you haven't already, grant the necessary permissions for the camera to function.</p>
            <video id="video" width="100%" height="500px" autoplay></video><br>
            <canvas id="canvas" style="display:none;"></canvas>
            
            <!-- Add a container for the buttons -->
            <div class="button-container">
                <button type="button" id="capture" class="capture-button">
                    <i class="fas fa-circle-notch"></i> Capture
                </button>
                <button type="button" id="retake" class="retake-button">
                    <i class="fas fa-sync-alt"></i> Retake
                </button>
            </div>
            <br>
            <div id="preview"></div>
            <input type="hidden" id="captured-image" name="captured_image">
        </div>
        
        <input type="hidden" id="latitude" name="latitude" value="<?php echo $item['latitude']; ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $item['longitude']; ?>">
        <br><button type="submit" name="submit" id="post-item" class="post-button">Update Item</button>
    </form>
</main>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>

    
   // Initialize the map with a default location (e.g., Manila)
var map = L.map('map').setView([14.5995, 120.9842], 12);

// Add OpenStreetMap tile layer
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Add a marker for the initial location
var marker = L.marker([14.5995, 120.9842], { draggable: true }).addTo(map);

// Function to update the location input fields
function updateLocationInputs(lat, lng) {
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;

    // Reverse geocode to get the location name
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
        .then(response => response.json())
        .then(data => {
            var locationName = data.display_name || 'Unknown location';
            document.getElementById('location').value = locationName;
        })
        .catch(error => console.log('Error reverse geocoding:', error));
}

// Use Geolocation API to get user's current location
if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        function (position) {
            var lat = position.coords.latitude;
            var lng = position.coords.longitude;

            // Update map and marker to user's location
            map.setView([lat, lng], 15);
            marker.setLatLng([lat, lng]);

            // Update input fields with current location
            updateLocationInputs(lat, lng);
        },
        function (error) {
            console.log('Geolocation error:', error);
            alert('Unable to fetch your location. Please select a location manually.');
        }
    );
} else {
    alert('Geolocation is not supported by your browser. Please select a location manually.');
}

// Update location inputs when the map is clicked
map.on('click', function (e) {
    var lat = e.latlng.lat;
    var lng = e.latlng.lng;

    // Move the marker to the clicked location
    marker.setLatLng(e.latlng);

    // Update input fields with the clicked location
    updateLocationInputs(lat, lng);
});

// Update location inputs when the marker is dragged
marker.on('dragend', function (e) {
    var position = marker.getLatLng();
    updateLocationInputs(position.lat, position.lng);
});

    // Initialize camera visibility state
const cameraDropdown = document.getElementById('camera-dropdown');

// Initially hide the camera dropdown
cameraDropdown.style.display = 'none';

// Camera functionality
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const captureButton = document.getElementById('capture');
const retakeButton = document.getElementById('retake');
const preview = document.getElementById('preview');
const capturedImage = document.getElementById('captured-image');

document.getElementById('start-camera').addEventListener('click', startCamera);
captureButton.addEventListener('click', captureImage);
retakeButton.addEventListener('click', retakeImage);

function startCamera() {
    // Show the camera dropdown when the start button is clicked
    cameraDropdown.style.display = 'block';

    // Start the camera feed
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(function(stream) {
            video.srcObject = stream;
        })
        .catch(function(error) {
            console.log('Error accessing camera: ', error);
        });
}

function captureImage() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    const dataURL = canvas.toDataURL('image/png');
    preview.innerHTML = `<img src="${dataURL}" alt="Captured Image">`;
    capturedImage.value = dataURL;
    retakeButton.style.display = 'inline-block';
}

function retakeImage() {
    preview.innerHTML = '';
    capturedImage.value = '';
    retakeButton.style.display = 'none';
}

</script>
</body>

<style>

</style>
</html>
