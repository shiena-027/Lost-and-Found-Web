<?php

$user_id = $_SESSION['user_id'];
$photo = 'css/img/user.png';

// Fetch the current profile picture and name from the database
$query = "SELECT photo, name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($db_picture, $name);
if ($stmt->fetch() && $db_picture) {
    $photo = $db_picture;
}
$stmt->close();

?>


<nav class="navbar">
    <div class="navbar-left">
        <a onclick="openNav()" class="navbar-link"><img src="css/img/menu.svg"></a>
    </div>
    <div id="mySidenav" class="sidenav">
        
        <div class="sidehead">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="profile.php">
                <img src="<?php echo htmlspecialchars($photo); ?>" alt="Profile Picture" class="profile-photo">
            </a>
        </div>

        <h3 class="user-name" style="text-align: center;">
            <a href="profile.php" style="text-decoration: none; color: inherit;">
                <?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']) : 'Guest'; ?>
            </a>
        </h3>
</div>
        <div class="sidecon">
            <a href="faq.php">FAQ</a>
            <a href="about.php">About Us</a>
            <a href="inquiry.php">Contact Us</a>
            <a href="terms.php">Terms & Privacy Policy</a>
            <div class="logout">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

    </div>
    <div class="navbar-right">
        <ul class="navbar-links">
            <li><a href="dashboard.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" id="home"><i class="fa fa-home"></i></a></li>
            <li><a href="post_item.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'post_item.php' ? 'active' : ''; ?>" id="post"><i class="fa fa-pencil-square"></i></a></li>
            <li><a href="search.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'search.php' ? 'active' : ''; ?>" id="search"><i class="fa fa-search"></i></a></li>
            <li><a href="notification.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'notification.php' ? 'active' : ''; ?>" id="notif"><i class="fa fa-bell"></i></a></li>
            <li><a href="chat.php" class="navbar-link <?php echo basename($_SERVER['PHP_SELF']) == 'chat.php' ? 'active' : ''; ?>" id="chat"><i class="fa fa-comment"></i></a></li>
        </ul>
    </div>

</nav>
<link rel="stylesheet" href="css/web_navbar.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    /* Set the width of the side navigation to 250px and the left margin of the page content to 250px and add a black background color to body */
    function openNav() {
        document.getElementById("mySidenav").style.width = "17%";
        document.body.style.backgroundColor = "rgba(0,0,0,0.3)";
    }

    /* Set the width of the side navigation to 0 and the left margin of the page content to 0, and the background color of body to white */
    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
        document.body.style.backgroundColor = "white";
    }
    

</script>