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
    <link rel="stylesheet" href="css/auth.css"> <!-- Your CSS file -->
    <script>
        function toggleForms(form) {
            if (form === 'login') {
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
        <div class="auth-buttons">
        </div>

        <!-- Login Form -->
        <div id="login-form" class="auth-form" style="display: block;">
            <h2>LOGIN</h2>
            <form action="auth.php" method="POST">
                <div class="auth-text">
                    <input type="email" name="email" placeholder="Enter your email" required><br>
                    <input type="password" name="password" placeholder="Enter your password" required><br>
                    <button type="submit" name="login">Login</button>
                </div>
            </form>
            <div class="divspace">
                <span>Dont have an account?</span>
                <a onclick="toggleForms('register')" style="color: blue;"><u>Register</u></a>
            </div>

        </div>

        <!-- Registration Form -->
        <div id="register-form" class="auth-form" style="display: none;">
            <h2>REGISTER</h2>
            <form action="auth.php" method="POST">
                <div class="auth-text">
                    <input type="text" name="name" placeholder="Enter your full name" required><br>
                    <input type="email" name="email" placeholder="Enter your email" required><br>
                    <input type="password" name="password" placeholder="Create a password" required><br>
                    <input type="password" name="confirm_password" placeholder="Confirm password" required><br>
                    <button type="submit" name="register">Register</button><br>
                </div>
                <button onclick="toggleForms('login')" class="logbutt"><img src="css/img/arrow_back.svg"></button>
            </form>
        </div>
    </div>

</body>

</html>