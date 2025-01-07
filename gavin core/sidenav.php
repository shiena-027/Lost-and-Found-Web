<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sidenav.css">
</head>

<body>
    <div class="navdiv" id="navdiv">
        <ul>
            <li><span onclick="openNav()"><img src="css/img/menu.svg"></span></li>
        </ul>
    </div>

    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
        <a href="#"><img src="css/img/pfpplaceholder.svg"><?php echo htmlspecialchars($_SESSION['user']); ?></a>
        <a href="faq.php">FAQ</a>
        <a href="about.php">About Us</a>
        <a href="inquiry.php">Contact Us</a>
        <a href="terms.php">Terms & Privacy Policy</a>
        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>


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
</body>

</html>