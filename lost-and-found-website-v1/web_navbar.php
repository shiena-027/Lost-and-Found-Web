
<nav class="navbar">
    <div class="navbar-left">
        <a href="dashboard.php" class="navbar-link"><i class="fa fa-home"></i></a> <!-- Home Icon -->
    </div>
    <div class="navbar-center">
        <ul class="navbar-links">
            <li><a href="post_item.php" class="navbar-link">Post</a></li>
            <li><a href="search.php" class="navbar-link">Search</a></li>
            <li><a href="notification.php" class="navbar-link">Notifications</a></li>
            <li><a href="chat.php" class="navbar-link">Chat</a></li>
            <li><a href="faq.php" class="navbar-link">FAQ</a></li>
            <li><a href="about.php" class="navbar-link">About Us</a></li>
            <li><a href="inquiry.php" class="navbar-link">Contact Us</a></li>
            <li><a href="terms.php" class="navbar-link">Terms & Privacy Policy</a></li>
            <li><a href="profile.php" class="navbar-link">Profile</a></li>
        </ul>
    </div>
    <div class="navbar-right">
        <div class="dropdown">
            <a href="#" class="navbar-link"><i class="fa fa-cogs"></i></a> <!-- Settings Icon -->
            <div class="dropdown-content">
                <a href="profile_settings.php">Account Settings</a>
                <a href="logout.php" class="logout">Logout</a>
            </div>
        </div>
    </div>
</nav>

<style>
    nav {
        display: none;
    }

    /* Styles for web */
    @media (min-width: 768px) {
        h2 {
            display: none;
        }

                /* Navbar Styles */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #4b84ff;
            padding: 10px 20px;
            top: 0;
            z-index: 1000; /* Ensure it stays on top of other content */
            width: 100%;
            position: fixed; /* Fixed position for the navbar */
            left: 0;
            right: 0;
            box-sizing: border-box; /* Prevents padding from affecting width */
        }


        body {
            padding-top: 50px; /* Adjust according to your navbar height */
        }


        /* Left side - Home Icon */
        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-link {
            text-decoration: none;
            color: #ffffff;
            font-size: 15px;
            padding: 8px 15px;
            transition: background-color 0.3s ease;
        }

        .navbar-link:hover {
            background-color: #296dff; /* Highlighted color */
            border-radius: 8px;
        }

        /* Center - Navbar Links */
        .navbar-center {
            flex-grow: 1;
            text-align: center;
        }

        .navbar-links {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
        }

        .navbar-links li {
            margin: 0 15px;
        }

        .navbar-link.active {
            background-color: #296dff;
            color: #ffffff;
            border-radius: 8px;
        }

        /* Right side - Settings Icon and Dropdown */
        .navbar-right {
            display: flex;
            align-items: center;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 30px;
            right: 0;
            background-color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            min-width: 150px;
        }

        .dropdown-content a {
            color: #14171a;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 14px;
        }

        .dropdown-content a:hover {
            background-color: #e8f5fe;
            color: #1da1f2;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .logout {
            color: #e0245e;
            font-weight: bold;
        }

        .logout:hover {
            background-color: #ffe6eb;
            color: #a71b3c;
        }

    }
</style>



<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
