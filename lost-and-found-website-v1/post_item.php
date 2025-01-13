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

    // Process multiple image uploads
    $uploaded_images = [];
    if (!empty($_FILES['image']['name'])) { // Check if any file was uploaded
        if (is_array($_FILES['image']['name'])) {
            $image_count = count($_FILES['image']['name']);
            for ($i = 0; $i < $image_count; $i++) {
                $image_name = $_FILES['image']['name'][$i];
                $image_temp = $_FILES['image']['tmp_name'][$i];
    
                // Generate a unique filename for each image
                $unique_name = uniqid('image_') . '_' . $image_name;
                $image_path = 'uploads/' . $unique_name;
    
                // Move the uploaded image to the 'uploads' folder
                if (move_uploaded_file($image_temp, $image_path)) {
                    $uploaded_images[] = $unique_name; // Add the image name to the array
                }
            }
        } else {
            // Handle the single image case
            $image_name = $_FILES['image']['name'];
            $image_temp = $_FILES['image']['tmp_name'];
    
            // Generate a unique filename for the image
            $unique_name = uniqid('image_') . '_' . $image_name;
            $image_path = 'uploads/' . $unique_name;
    
            // Move the uploaded image to the 'uploads' folder
            if (move_uploaded_file($image_temp, $image_path)) {
                $uploaded_images[] = $unique_name; // Add the image name to the array
            }
        }
    }
    

    // If images were uploaded, save the filenames as a comma-separated string
    $image_list = implode(',', $uploaded_images);

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
    $stmt->bind_param("ssssssdd", $user_id, $title, $description, $location, $type, $image_list, $latitude, $longitude);

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
    <title>Post Item</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/forms.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php include('web_navbar.php'); ?>
<?php include('sidenav.php'); ?>
<main class="form-container">
    <form action="post_item.php" method="POST" enctype="multipart/form-data">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" placeholder="Enter the title (e.g., Lost Pet, Missing Item, Found Person)" required>

        <label for="description">Description:</label>
        <textarea id="description" name="description" placeholder="Provide a detailed description (e.g., item features, appearance, or circumstances)" required></textarea>

        <label for="type">Type:</label>
        <select id="type" name="type" required>
            <option value="Lost">Lost</option>
            <option value="Found">Found</option>
        </select>

        <label for="location">Location:</label>
        <input type="text" id="location" name="location" placeholder="Select or pin your location on the map" readonly>
       
        <div id="map"></div><br>

        <!-- Image Upload -->
        <div class="camera-container">
        <label for="image">Image:</label>
        <input type="file" id="image" name="image" accept="image/*" multiple>
        
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
        
        <input type="hidden" id="latitude" name="latitude">
        <input type="hidden" id="longitude" name="longitude">
        <br><button type="submit" name="submit" id="post-item" class="post-button">Post Item</button>
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
    canvas.style.display = 'block';
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    const imageData = canvas.toDataURL('image/png');
    capturedImage.value = imageData;

    // Show a preview of the captured image
    preview.innerHTML = `<img src="${imageData}" alt="Captured Image" style="max-width: 100%; height: auto;">`;

    // Hide the video and capture button, show the retake button
    video.style.display = 'none';
    captureButton.style.display = 'none';
    retakeButton.style.display = 'block';
}

function retakeImage() {
    // Hide the canvas and preview
    canvas.style.display = 'none';
    preview.innerHTML = '';

    // Show the video and capture button again
    video.style.display = 'block';
    captureButton.style.display = 'block';
    retakeButton.style.display = 'none';
}


</script>
</body>

<style>
/* Button Styles */
.camera-container {
    display: flex;
    align-items: center; /* Vertically align the items */
    gap: 10px; /* Optional: Adds space between the items */
}

.camera-container label {
    margin-right: 10px; /* Adds some space between the label and input */
}

.camera-container input {
    margin-right: 10px; /* Adds some space between the input and the button */
}

/* Camera Button */
.camera-button {
    display: inline-flex;
    align-items: center; /* Centers the icon inside the button */
    justify-content: center;
    padding: 15px 25px;
    background-color: #256EBB; /* Blue background */
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

/* Capture Button */
.capture-button {
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    background-color: #4CAF50; /* Green background */
    color: white; /* White text */
    border: none;
    border-radius: 4px;
}

.capture-button:hover {
    background-color: #45a049; /* Slightly darker green on hover */
}

/* Retake Button */
.retake-button {
    padding: 10px 20px;
    font-size: 14px;
    cursor: pointer;
    background-color: #f44336; /* Red background */
    color: white; /* White text */
    border: none;
    border-radius: 4px;
}

.retake-button:hover {
    background-color: #da190b; /* Slightly darker red on hover */
}

/* Button Container Layout */
.button-container {
    display: flex;
    justify-content: center; /* Centers the buttons horizontally */
    gap: 10px; /* Adds space between the buttons */
}

.camera-button i {
    font-size: 20px; /* Adjusts the icon size */
}

#preview {
    display: flex;
    justify-content: center;
    align-items: center;
}
</style>
</html>
