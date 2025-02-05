<?php
session_start();
include('includes/db.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Get the logged-in user's location (latitude and longitude) from the users table
$user_query = "SELECT latitude, longitude FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_location = mysqli_fetch_assoc($user_result);

if ($user_location && !empty($user_location['latitude']) && !empty($user_location['longitude'])) {
    $user_lat = $user_location['latitude'];
    $user_lon = $user_location['longitude'];

    // Query to fetch items within a 5km radius
    $items_query = "
        SELECT *, 
            (6371 * ACOS(
                COS(RADIANS($user_lat)) * COS(RADIANS(latitude)) * 
                COS(RADIANS(longitude) - RADIANS($user_lon)) + 
                SIN(RADIANS($user_lat)) * SIN(RADIANS(latitude))
            )) AS distance 
        FROM items 
        WHERE type != 'Claimed'
        HAVING distance <= 5
        ORDER BY distance ASC
    ";
} else {
    // If the user doesn't have location data, fetch all items (location is optional)
    $items_query = "SELECT * FROM items WHERE type != 'Claimed'";
}

$items_result = mysqli_query($conn, $items_query);

// Query to get total number of lost items
$query_lost = "SELECT COUNT(*) AS total_lost FROM items WHERE type = 'Lost'";
$result_lost = mysqli_query($conn, $query_lost);
$total_lost = mysqli_fetch_assoc($result_lost)['total_lost'];

// Query to get total number of found items
$query_found = "SELECT COUNT(*) AS total_found FROM items WHERE type = 'Found'";
$result_found = mysqli_query($conn, $query_found);
$total_found = mysqli_fetch_assoc($result_found)['total_found'];

// Query to get total number of claimed items
$query_claimed = "SELECT COUNT(*) AS total_claimed FROM items WHERE type = 'Claimed'";
$result_claimed = mysqli_query($conn, $query_claimed);
$total_claimed = mysqli_fetch_assoc($result_claimed)['total_claimed'];

// Query to get place with the most missing items
$query_place = "SELECT location, COUNT(*) AS missing_items_count FROM items WHERE type = 'Lost' GROUP BY location ORDER BY missing_items_count DESC LIMIT 1";
$result_place = mysqli_query($conn, $query_place);
$place_data = mysqli_fetch_assoc($result_place);

// Check if location parts are valid
// Ensure $place_data['location'] is set and not null before splitting
if (isset($place_data['location']) && !empty($place_data['location'])) {
    $location_parts = explode(",", $place_data['location']); // Split by commas
    $place_with_most_missing = isset($location_parts[2]) ? trim($location_parts[2]) : 'Unknown'; // Fallback to 'Unknown' if there's no third element
} else {
    $place_with_most_missing = 'Unknown';
}

// Fetch the count of missing items
$missing_items_count = isset($place_data['missing_items_count']) ? $place_data['missing_items_count'] : 0;


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lost and Found Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/no_message.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

</head>
<body>
<?php include('web_navbar.php'); ?>
<div class="items">
    <h3>Listing Near You</h3>
    <?php if (mysqli_num_rows($items_result) > 0) { ?>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <?php while ($item = mysqli_fetch_assoc($items_result)) { 
                    $poster_id = $item['user_id'];
                    $user_query = "SELECT * FROM users WHERE id = '$poster_id'";
                    $user_result = mysqli_query($conn, $user_query);
                    $user = mysqli_fetch_assoc($user_result);

                    $profile_photo = isset($user['photo']) && !empty($user['photo']) ? $user['photo'] : 'css/img/user.png';

                    date_default_timezone_set('Asia/Manila');
                    $created_at = $item['created_at']; 
                    $posted_time = new DateTime($created_at); 
                    $posted_time->setTimezone(new DateTimeZone('Asia/Manila')); 
                    $current_time = new DateTime();
                    $current_time->setTimezone(new DateTimeZone('Asia/Manila'));
                    $time_diff = $current_time->diff($posted_time);

                    if ($time_diff->y > 0) {
                        $time_ago = ($time_diff->y == 1) ? '1 year ago' : $time_diff->y . ' years ago';
                    } elseif ($time_diff->m > 0) {
                        $time_ago = ($time_diff->m == 1) ? '1 month ago' : $time_diff->m . ' months ago';
                    } elseif ($time_diff->d > 0) {
                        $time_ago = ($time_diff->d == 1) ? '1 day ago' : $time_diff->d . ' days ago';
                    } elseif ($time_diff->h > 0) {
                        $time_ago = ($time_diff->h == 1) ? '1 hour ago' : $time_diff->h . ' hours ago';
                    } elseif ($time_diff->i > 0) {
                        $time_ago = ($time_diff->i == 1) ? '1 minute ago' : $time_diff->i . ' minutes ago';
                    } else {
                        $time_ago = 'Just now'; 
                    }
                ?>
                <div class="swiper-slide item-card">
                    <a href="item_detail.php?item_id=<?php echo $item['id']; ?>" class="item-link">
                        <div class="item-content">
                            <?php 
                           
                            $images = explode(',', $item['image']);
                            $first_image = $images[0]; // Get the first image in the list
                            ?>
                            <img src="uploads/<?php echo htmlspecialchars($first_image); ?>" alt="Item Image" class="item-img" onerror="this.onerror=null;this.src='css/img/no-pictures.png'; this.classList.add('fallback-image');" />
                            <h4 class="item-title"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <p class="item-description"><?php echo htmlspecialchars($item['description']); ?></p>
                            <p class="item-location">
                                <i class="fas fa-map-marker-alt"></i> 
                                <a href="https://www.google.com/maps?q=<?php echo urlencode(htmlspecialchars($item['location'])); ?>" target="_blank">
                                    <?php echo htmlspecialchars($item['location']); ?>
                                </a>
                            </p>
                            <p class="item-time"><i class="fas fa-clock"></i> <?php echo $time_ago; ?></p>
                        </div>
                    </a>
                </div>
                <?php } ?>
            </div>
            <div class="swiper-pagination"></div>
        </div>

        <!-- View More Button -->
        <div class="view-more-container">
            <a href="search.php" class="view-more-btn">View More</a>
        </div>

    <?php } else { ?>
        <p class="no-items-message">No items found.</p>
    <?php } ?>
</div>


<!-- Stats Section -->
<div class="stats">

    <div class="stat">
        <img src="css/img/search.gif" alt="Search GIF" class="stat-gif">
        <h3><?php echo $total_lost; ?></h3>
            <p>Total items reported <br>missing by users</p>
    </div>
    <div class="stat">
        <img src="css/img/vision.gif" alt="Found GIF" class="stat-gif"> 
        <h3><?php echo $total_found; ?></h3>
            <p>Total items found and<br> listed by users</p>
    </div>
    <div class="stat">
        <img src="css/img/handshake.gif" alt="Claimed GIF" class="stat-gif"> 
            <h3>
            <?php echo $total_claimed; ?></h3>
            <p>Total lost items <br> successfully returned <br> to owners</p>
    </div>
    <div class="stat">
        <img src="css/img/map.gif" alt="Map GIF" class="stat-gif"> 
            <h3>
            <?php echo $place_with_most_missing . ''; ?></h3>
            <p>Location with the <br>most missing <br> item reports</p>
    </div>
</div>
<!--
<div class="container">
  <input type="radio" name="slider" id="item-1" checked>
  <input type="radio" name="slider" id="item-2">
  <input type="radio" name="slider" id="item-3">
  
  <div class="cards">
    <label class="card" for="item-1" id="song-1">
      <div class="confession">
        <h3>Confession #1</h3>
        <p>This is the first confession from our users.</p>
      </div>
    </label>
    <label class="card" for="item-2" id="song-2">
      <div class="confession">
        <h3>Confession #2</h3>
        <p>This is the second confession from our users.</p>
      </div>
    </label>
    <label class="card" for="item-3" id="song-3">
      <div class="confession">
        <h3>Confession #3</h3>
        <p>This is the third confession from our users.</p>
      </div>
    </label>
  </div>
</div>
    -->
        
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

    function openReportModal(itemId) {
document.getElementById('report-item-id').value = itemId;
document.getElementById('report-modal').style.display = 'block';
}

// Close the modal when the user clicks the close button
document.getElementById('report-modal').querySelector('.close-btn').onclick = function() {
    document.getElementById('report-modal').style.display = 'none';
};

// Close the modal when the user clicks outside of the modal content
window.onclick = function(event) {
    if (event.target === document.getElementById('report-modal')) {
        document.getElementById('report-modal').style.display = 'none';
    }
};

function toggleOtherReason() {
    var reason = document.getElementById('reason').value;
    var otherReasonContainer = document.getElementById('other-reason-container');
    
    if (reason === 'Other') {
        otherReasonContainer.style.display = 'block';
    } else {
        otherReasonContainer.style.display = 'none';
    }
};
// Add event listener to the image for toggling zoom
document.querySelector('.modal-content img').onclick = function () {
    if (this.classList.contains('zoomed')) {
        this.classList.remove('zoomed'); // Remove zoom
    } else {
        this.classList.add('zoomed'); // Add zoom
    }
};
</script>

<!-- Include Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

<script>
    
var swiper = new Swiper(".swiper-container", {
    slidesPerView: 1, 
    spaceBetween: 10,
    loop: true,
    autoplay: {
        delay: 3000,
        disableOnInteraction: false,
    },
    breakpoints: {
        1200: { slidesPerView: 4 }, 
        1024: { slidesPerView: 3 }, 
        768: { slidesPerView: 2 }, 
        576: { slidesPerView: 1.5 }, 
        480: { slidesPerView: 1 }, 
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
    },
});

</script>


</body>
</html>
<?php
include 'includes/footer.php';
?>
<?php

mysqli_close($conn);
?>
