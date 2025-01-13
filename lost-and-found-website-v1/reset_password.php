<?php
session_start();
include('includes/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle password reset process
if (isset($_POST['reset_password'])) {
    // Get the verification code, email, and new password from the form
    $verification_code = $_POST['verification_code'];
    $new_password = $_POST['new_password'];
    $email = $_GET['email']; // Get email from the URL

    // Step 1: Check if the code exists and is valid
    $query = "SELECT id, password_verification_code, password_verification_expiration FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($user_id, $stored_code, $stored_expiration);
    $stmt->fetch();

    $current_time = date("Y-m-d H:i:s");

    // Step 2: Check if the verification code matches and is not expired
    if ($verification_code == $stored_code && $current_time <= $stored_expiration) {
        // Step 3: Update the password
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Prepare and execute the query to update the password
        $update_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("si", $new_password_hashed, $user_id);
        $update_stmt->execute();

        $_SESSION['message'] = 'Password reset successfully!';
        header('Location: auth.php'); // Redirect to login page after successful reset
        exit();
    } else {
        $_SESSION['error'] = 'Invalid or expired verification code.';
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/reset.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>

<!-- Reset password form -->
<form method="POST" action="">
<img src="assets/img/delivery.png" alt="Logo" style="width: 100px; height: auto; margin-bottom: 20px;">

    <p class="reset-password-text">Reset Password</p>
    <input type="text" name="verification_code" placeholder="Enter verification code" required>
    <input type="password" name="new_password" placeholder="Enter new password" required>
    <button type="submit" name="reset_password">Reset Password</button>
</form>

<svg id="wave" style="transform:rotate(0deg); transition: 0.3s" viewBox="0 0 1440 310" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sw-gradient-0" x1="0" x2="0" y1="1" y2="0"><stop stop-color="rgba(26, 75, 138, 1)" offset="0%"></stop><stop stop-color="rgba(26, 75, 138, 1)" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 0px); opacity:1" fill="url(#sw-gradient-0)" d="M0,0L26.7,25.8C53.3,52,107,103,160,129.2C213.3,155,267,155,320,144.7C373.3,134,427,114,480,98.2C533.3,83,587,72,640,82.7C693.3,93,747,124,800,149.8C853.3,176,907,196,960,186C1013.3,176,1067,134,1120,118.8C1173.3,103,1227,114,1280,139.5C1333.3,165,1387,207,1440,217C1493.3,227,1547,207,1600,191.2C1653.3,176,1707,165,1760,175.7C1813.3,186,1867,217,1920,227.3C1973.3,238,2027,227,2080,191.2C2133.3,155,2187,93,2240,93C2293.3,93,2347,155,2400,160.2C2453.3,165,2507,114,2560,77.5C2613.3,41,2667,21,2720,56.8C2773.3,93,2827,186,2880,191.2C2933.3,196,2987,114,3040,67.2C3093.3,21,3147,10,3200,25.8C3253.3,41,3307,83,3360,98.2C3413.3,114,3467,103,3520,82.7C3573.3,62,3627,31,3680,41.3C3733.3,52,3787,103,3813,129.2L3840,155L3840,310L3813.3,310C3786.7,310,3733,310,3680,310C3626.7,310,3573,310,3520,310C3466.7,310,3413,310,3360,310C3306.7,310,3253,310,3200,310C3146.7,310,3093,310,3040,310C2986.7,310,2933,310,2880,310C2826.7,310,2773,310,2720,310C2666.7,310,2613,310,2560,310C2506.7,310,2453,310,2400,310C2346.7,310,2293,310,2240,310C2186.7,310,2133,310,2080,310C2026.7,310,1973,310,1920,310C1866.7,310,1813,310,1760,310C1706.7,310,1653,310,1600,310C1546.7,310,1493,310,1440,310C1386.7,310,1333,310,1280,310C1226.7,310,1173,310,1120,310C1066.7,310,1013,310,960,310C906.7,310,853,310,800,310C746.7,310,693,310,640,310C586.7,310,533,310,480,310C426.7,310,373,310,320,310C266.7,310,213,310,160,310C106.7,310,53,310,27,310L0,310Z"></path><defs><linearGradient id="sw-gradient-1" x1="0" x2="0" y1="1" y2="0"><stop stop-color="rgba(135, 189, 228, 1)" offset="0%"></stop><stop stop-color="rgba(135, 189, 228, 1)" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 50px); opacity:0.9" fill="url(#sw-gradient-1)" d="M0,186L26.7,191.2C53.3,196,107,207,160,180.8C213.3,155,267,93,320,93C373.3,93,427,155,480,186C533.3,217,587,217,640,206.7C693.3,196,747,176,800,139.5C853.3,103,907,52,960,67.2C1013.3,83,1067,165,1120,180.8C1173.3,196,1227,145,1280,144.7C1333.3,145,1387,196,1440,180.8C1493.3,165,1547,83,1600,56.8C1653.3,31,1707,62,1760,93C1813.3,124,1867,155,1920,139.5C1973.3,124,2027,62,2080,41.3C2133.3,21,2187,41,2240,72.3C2293.3,103,2347,145,2400,175.7C2453.3,207,2507,227,2560,227.3C2613.3,227,2667,207,2720,196.3C2773.3,186,2827,186,2880,186C2933.3,186,2987,186,3040,191.2C3093.3,196,3147,207,3200,175.7C3253.3,145,3307,72,3360,62C3413.3,52,3467,103,3520,144.7C3573.3,186,3627,217,3680,201.5C3733.3,186,3787,124,3813,93L3840,62L3840,310L3813.3,310C3786.7,310,3733,310,3680,310C3626.7,310,3573,310,3520,310C3466.7,310,3413,310,3360,310C3306.7,310,3253,310,3200,310C3146.7,310,3093,310,3040,310C2986.7,310,2933,310,2880,310C2826.7,310,2773,310,2720,310C2666.7,310,2613,310,2560,310C2506.7,310,2453,310,2400,310C2346.7,310,2293,310,2240,310C2186.7,310,2133,310,2080,310C2026.7,310,1973,310,1920,310C1866.7,310,1813,310,1760,310C1706.7,310,1653,310,1600,310C1546.7,310,1493,310,1440,310C1386.7,310,1333,310,1280,310C1226.7,310,1173,310,1120,310C1066.7,310,1013,310,960,310C906.7,310,853,310,800,310C746.7,310,693,310,640,310C586.7,310,533,310,480,310C426.7,310,373,310,320,310C266.7,310,213,310,160,310C106.7,310,53,310,27,310L0,310Z"></path></svg>
    <!-- Display error or success messages -->
<?php if (isset($_SESSION['message'])): ?>
    <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <p><?php echo $_SESSION['message']; ?></p>
        </div>
    </div>
<?php unset($_SESSION['message']); endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div id="errorModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" onclick="closeModal()">&times;</span>
            <p><?php echo $_SESSION['error']; ?></p>
        </div>
    </div>
<?php unset($_SESSION['error']); endif; ?>

<script>
    // Modal script to close
    function closeModal() {
        document.getElementById('messageModal').style.display = 'none';
        document.getElementById('errorModal').style.display = 'none';
    }

    // Display modals on load
    window.onload = function() {
        if (document.getElementById('messageModal')) {
            document.getElementById('messageModal').style.display = 'block';
        }
        if (document.getElementById('errorModal')) {
            document.getElementById('errorModal').style.display = 'block';
        }
    };
</script>

</body>

</html>
