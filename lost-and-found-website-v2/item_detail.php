<?php 
// Fetch the item details and related user info as you already have:
session_start();
include('includes/db.php');

if (isset($_GET['item_id'])) {
    $item_id = $_GET['item_id'];

    // Fetch the item details
    $item_query = "SELECT * FROM items WHERE id = '$item_id'";
    $item_result = mysqli_query($conn, $item_query);
    $item = mysqli_fetch_assoc($item_result);
    
    // Fetch the user who posted the item
    $poster_id = $item['user_id'];
    $user_query = "SELECT * FROM users WHERE id = '$poster_id'";
    $user_result = mysqli_query($conn, $user_query);
    $user = mysqli_fetch_assoc($user_result);
    
    // Fetch related items
    $related_query = "SELECT * FROM items WHERE type = '".$item['type']."' AND id != '$item_id' LIMIT 5";
    $related_result = mysqli_query($conn, $related_query);
} else {
    header("Location: index.php");
    exit();
}

// Split the comma-separated image string into an array
$images = explode(',', $item['image']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Details</title>
    <link rel="stylesheet" href="css/item_detail.css">
</head>
<body>
    <?php include('web_navbar.php'); ?>

    <h2>Item Details</h2>
    
<div class="item-detail">

<div class="title-status">
  <h3><?php echo htmlspecialchars($item['title']); ?></h3>
  <h4 class="status <?php echo strtolower(htmlspecialchars($item['type'])); ?>">
    <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($item['type']); ?>
  </h4>
</div>

<hr class="custom-divider">
    <div class="item-info">
            <div class="carousel-container">
            <button class="carousel-btn prev">&lt;</button>
            <div class="carousel-track-container">
                <ul class="carousel-track">
                    <?php foreach ($images as $img): ?>
                        <?php if (!empty(trim($img))): // Ensure image name is not empty ?>
                            <li class="carousel-slide">
                                <img src="uploads/<?php echo htmlspecialchars($img); ?>" alt="Item Image">
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
            <button class="carousel-btn next">&gt;</button>
        </div>

        <div class="item-details">
            <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
            <p>
                <strong>Location:</strong> 
                <a href="https://www.google.com/maps?q=<?php echo urlencode($item['location']); ?>" target="_blank">
                    <?php echo htmlspecialchars($item['location']); ?>
                </a>
            </p>

            <p><strong>Posted By:</strong> <a href="profile.php?user_id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></a></p>
            <p><strong>Posted On:</strong> <?php echo date("F j, Y h:i A", strtotime($item['created_at'])); ?></p>
           
            <p><a href="chat.php?user_id=<?php echo $user['id']; ?>" class="chat-link"><i class="fas fa-comment"></i> Chat</a></p>
        </div>
    </div>
</div>
</div>
        <h4>Similar Listings</h4>
        <div class="related-items">
            <?php while ($related_item = mysqli_fetch_assoc($related_result)) { ?>
                <div class="related-item">
                    <a href="item_detail.php?item_id=<?php echo $related_item['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($related_item['image']); ?>" alt="Related Item" onerror="this.onerror=null;this.src='css/img/no-pictures.png'; this.classList.add('fallback-image');" ></a></p>
                        <p ><?php echo htmlspecialchars($related_item['title']); ?></p>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    </div>
</body>
</html>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const track = document.querySelector('.carousel-track');
  const slides = Array.from(track.children);
  const nextButton = document.querySelector('.carousel-btn.next');
  const prevButton = document.querySelector('.carousel-btn.prev');

  // Set the position of each slide based on the container width
  const slideWidth = slides[0].getBoundingClientRect().width;
  slides.forEach((slide, index) => {
    slide.style.left = slideWidth * index + 'px';
  });

  // If there's only one slide, hide the navigation buttons
  if (slides.length <= 1) {
    nextButton.style.display = 'none';
    prevButton.style.display = 'none';
  }

  let currentSlide = 0;

  function moveToSlide(targetSlide) {
    track.style.transition = 'transform 0.3s ease';  // Add transition for smooth movement
    track.style.transform = 'translateX(-' + targetSlide.style.left + ')';
  }

  nextButton.addEventListener('click', () => {
    currentSlide = (currentSlide + 1) % slides.length;
    moveToSlide(slides[currentSlide]);
  });

  prevButton.addEventListener('click', () => {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    moveToSlide(slides[currentSlide]);
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const carousel = document.querySelector('.related-items');

  // Listen for mouse wheel events on the carousel container
  carousel.addEventListener('wheel', function(e) {
    // Prevent the default vertical scroll behavior
    e.preventDefault();
    // Scroll horizontally by the amount of vertical scroll
    carousel.scrollLeft += e.deltaY;
  });
});

document.addEventListener("DOMContentLoaded", function() {
    const relatedItemsContainer = document.querySelector('.related-items');

    if (relatedItemsContainer) {
        relatedItemsContainer.addEventListener('wheel', function(e) {
            // Prevent the default vertical scroll behavior
            e.preventDefault();
            // Scroll horizontally by the vertical delta (you can adjust the multiplier if needed)
            relatedItemsContainer.scrollLeft += e.deltaY;
        });
    }
});


</script>


<?php 
// Close database connection
mysqli_close($conn);
?>

