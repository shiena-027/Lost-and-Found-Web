<nav class="navbar">
    <div class="navbar-left">
        <a onclick="openNav()" class="navbar-link"><img src="css/img/menu.svg"></a>
    </div>
    <div id="mySidenav" class="sidenav">
        <div class="sidehead">
            <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
            <a href="profile.php"><img src="css/img/pfpplaceholder.svg">
                <?php echo htmlspecialchars($_SESSION['user']); ?>
            </a>
        </div>
        <div class="sidecon">
            <a href="profile_settings.php">Account Settings</a>
            <a href="faq.php">FAQ</a>
            <a href="about.php">About Us</a>
            <a href="inquiry.php">Contact Us</a>
            <a href="terms.php">Terms & Privacy Policy</a>
        </div>
        <div class="logout">
            <a href="logout.php"><i class="fa fa-arrow-circle-left"></i> Logout</a>
        </div>

    </div>
    <div class="navbar-right">
        <ul class="navbar-links">
            <li><a href="dashboard.php" class="navbar-link" id="home"><i class="fa fa-home"></i></a></li> <!-- Home Icon -->
            <li><a href="post_item.php" class="navbar-link" id="post"><i class="fa fa-pencil-square"></i></a></li>
            <li><a href="search.php" class="navbar-link" id="search"><i class="fa fa-search"></i></a></li>
            <li><a href="notification.php" class="navbar-link" id="notif"><i class="fa fa-bell"></i></a></li>
            <li><a href="chat.php" class="navbar-link" id="chat"><i class="fa fa-comment"></i></a></li>

        </ul>
    </div>
    <!--
    <div class="navbar-right">
        <div class="dropdown">
            <a href="#" class="navbar-link"><i class="fa fa-cogs"></i></a> 
            <div class="dropdown-content">
                <a href="profile_settings.php">Account Settings</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>
-->
</nav>
<link rel="stylesheet" href="css/web_navbar.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
<script>
    /* Set the width of the side navigation to 250px and the left margin of the page content to 250px and add a black background color to body */
    function openNav() {
        document.getElementById("mySidenav").style.width = "250px";
        document.body.style.backgroundColor = "rgba(0,0,0,0.3)";
    }

    /* Set the width of the side navigation to 0 and the left margin of the page content to 0, and the background color of body to white */
    function closeNav() {
        document.getElementById("mySidenav").style.width = "0";
        document.body.style.backgroundColor = "white";
    }
</script>