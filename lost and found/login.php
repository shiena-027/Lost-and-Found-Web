<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";  // Update with server details
$username = "root";         // Update with database username
$password = "";             // Update with database password
$dbname = "";           // Update with database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname); // database to, replace na lang name ng database and shits

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle registration form submission
if (isset($_POST['register'])) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if the email already exists
    $check_email = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($check_email);

    if ($result->num_rows > 0) {
        $error_message = "Email already exists!";
    } else {
        // Insert new user into the database
        $register_query = "INSERT INTO users (full_name, email, password) VALUES ('$full_name', '$email', '$password')";
        if ($conn->query($register_query) === TRUE) {
            header("Location: index.php"); // Redirect to login page after successful registration
        } else {
            $error_message = "Error: " . $conn->error;
        }
    }
}

// Handle login form submission
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user data based on email
    $login_query = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($login_query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            header("Location: home.php"); // Redirect to home page after successful login
        } else {
            $error_message = "Incorrect password!";
        }
    } else {
        $error_message = "No account found with that email!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found | Login & Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <div class="toggle-container">
                <button id="login-btn" class="toggle-btn active">Login</button>
                <button id="register-btn" class="toggle-btn">Register</button>
            </div>
            
            <!-- Login Form -->
            <form id="login-form" class="form active" method="POST">
                <h2>Welcome Back!</h2>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login" class="submit-btn" onclick="location.href='home.php'>Login</button>
                <p>Don't have an account? <span class="switch">Register here</span></p>
                <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            </form>
            
            <!-- Register Form -->
            <form id="register-form" class="form" method="POST">
                <h2>Create Your Account</h2>
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register" class="submit-btn" onclick="location.href='login.php'>Register</button>
                <p>Already have an account? <span class="switch">Login here</span></p>
                <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            </form>
        </div>
    </div>
</body>
</html>