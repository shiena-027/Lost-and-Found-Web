<?php
session_start();

// Include database connection and functions
include('includes/db.php');
include('includes/functions.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    // If already logged in, redirect to the dashboard or another page
    header("Location: dashboard.php");
    exit();
}

// Handle the registration process
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user'; // Default role is user

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Check if email already exists
        $checkEmailQuery = "SELECT * FROM users WHERE email='$email'";
        $result = mysqli_query($conn, $checkEmailQuery);
        
        if (mysqli_num_rows($result) > 0) {
            echo "Email already exists!";
        } else {
            // Insert the new user into the database with the role
            $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$hashed_password', '$role')";
            if (mysqli_query($conn, $query)) {
                echo "Registration successful!";
                // Redirect to login page after successful registration
                header("Location: auth.php");
                exit();
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Handle the login process
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check if user exists
    $query = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user'] = $user['name']; // Name for the welcome page
            $_SESSION['email'] = $user['email'];
            $_SESSION['user_id'] = $user['id']; // Store user ID in session
            
            // Check if the user is an admin
            if ($user['role'] == 'admin') {
                $_SESSION['is_admin'] = true;
                header("Location: admin_dashboard.php"); // Redirect to admin dashboard
            } else {
                $_SESSION['is_admin'] = false;
                header("Location: dashboard.php"); // Redirect to regular user dashboard
            }
            exit();
        } else {
            echo "Invalid credentials!";
        }
    } else {
        echo "No account found with this email!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link rel="stylesheet" href="assets/auth.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        function toggleForms(form) {
            if(form === 'login') {
                document.getElementById('login-form').style.display = 'block';
                document.getElementById('register-form').style.display = 'none';
            } else {
                document.getElementById('login-form').style.display = 'none';
                document.getElementById('register-form').style.display = 'block';
            }
        }
    </script>
</head>
<body>

    <div class="auth-container">
    <img src="assets/img/logo.png" alt="Description of the image" width="250" height="200">
    <div class="auth-buttons">
        <button onclick="toggleForms('login')">Login</button>
       <!-- <span style="font-size: 40px; margin: 0 10px; font-weight: bold;">|</span> -->
        <button onclick="toggleForms('register')">Register</button>
    </div>


        <!-- Login Form -->
        <div id="login-form" class="auth-form" style="display: block;">
       

           
            <form action="auth.php" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required><br>
                <input type="password" name="password" placeholder="Enter your password" required><br>
                <button type="submit" name="login">Login</button>
            </form>
        </div>

        <!-- Registration Form -->
        <div id="register-form" class="auth-form" style="display: none;">

            <form action="auth.php" method="POST">
                <input type="text" name="name" placeholder="Enter your full name" required><br>
                <input type="email" name="email" placeholder="Enter your email" required><br>
                <input type="password" name="password" placeholder="Create a password" required><br>
                <input type="password" name="confirm_password" placeholder="Confirm password" required><br>
                <button type="submit" name="register">Register</button>
            </form>
        </div>
    </div>

    
</body>
    <script>

$(document).ready(function() {
    // Hover effect for buttons
    $('.auth-buttons button').hover(
        function() {
            $(this).css('color', '#1da1f2');  // Change color on hover
            $(this).find('::after').css('transform', 'scaleX(1)');  // Scale the line
        },
        function() {
            $(this).css('color', '#14171a');  // Reset color on mouse leave
            $(this).find('::after').css('transform', 'scaleX(0)');  // Reset line scale
        }
    );

    // Handle active button effect
    $('.auth-buttons button').click(function() {
        $('.auth-buttons button').removeClass('active');  // Remove active class from all buttons
        $(this).addClass('active');  // Add active class to the clicked button
    });

    // Input field focus effect
    $('.auth-form input').focus(function() {
        $(this).css({
            'background': 'rgba(255, 255, 255, 0.4)',  // Change background color
            'transform': 'scale(1.02)'  // Slightly enlarge the input field
        });
    });

    // Input field blur effect
    $('.auth-form input').blur(function() {
        $(this).css({
            'background': '',  // Reset background color
            'transform': ''  // Reset scale
        });
    });

    // Submit button hover effect
    $('.auth-form button').hover(
        function() {
            $(this).css('background-color', '#1A4B8A');  // Darken the button on hover
        },
        function() {
            $(this).css('background-color', '#1da1f2');  // Reset to original color
        }
    );
});
</script>
    
</html>
