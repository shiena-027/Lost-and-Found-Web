<?php
session_start();

// Include PHPMailer
require 'assets/PHPMailer/src/Exception.php';
require 'assets/PHPMailer/src/PHPMailer.php';
require 'assets/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include('includes/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle forgot password process
if (isset($_POST['forgot_password'])) {
    // Get the email from the form
    $email = $_POST['email'];

    // Step 1: Check if the email exists in the database
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If the email exists
    if ($stmt->num_rows > 0) {
        // Generate a verification code
        $verification_code = rand(100000, 999999);
        $expiration_time = date("Y-m-d H:i:s", strtotime("+15 minutes")); // Expire in 15 minutes

        // Get user ID from the database
        $stmt->bind_result($user_id);
        $stmt->fetch();

        // Update the database with the verification code and expiration time
        $update_query = "UPDATE users SET password_verification_code = ?, password_verification_expiration = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssi", $verification_code, $expiration_time, $user_id);
        $update_stmt->execute();

        // Step 2: Send the verification email
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '';  // Set your SMTP email here
            $mail->Password = ''; // Set your SMTP password here
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('', 'Lost and Found');
            $mail->addAddress($email); // Send to the user's email

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Verification Code';
            $mail->Body = 'Your password reset verification code is: <strong>' . $verification_code . '</strong>';

            $mail->send();
            $_SESSION['message'] = 'A verification code has been sent to your email. Please check your inbox and enter the code on the next page.';
            header('Location: reset_password.php?email=' . urlencode($email)); // Redirect to reset_password.php
            exit();
        } catch (Exception $e) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            $_SESSION['error'] = 'Failed to send verification email.';
        }
    } else {
        $_SESSION['error'] = 'No account found with that email address.';
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
    <title>Forgot Password</title>
    <link rel="stylesheet" href="assets/forgot.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

</head>
<body>

<!-- Forgot password form -->
<form method="POST" action="">
<img src="assets/img/subscription.png" alt="Logo" style="width: 100px; height: auto; margin-bottom: 20px;">
<p class="forgot-password-text">Forgot Password</p>

        <input type="email" name="email" placeholder="Enter your email" required>
    <button type="submit" name="forgot_password">Send Verification Code</button>
    
</form>
<svg id="wave" style="transform:rotate(0deg); transition: 0.3s" viewBox="0 0 1440 310" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sw-gradient-0" x1="0" x2="0" y1="1" y2="0"><stop stop-color="rgba(26, 75, 138, 1)" offset="0%"></stop><stop stop-color="rgba(26, 75, 138, 1)" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 0px); opacity:1" fill="url(#sw-gradient-0)" d="M0,0L26.7,25.8C53.3,52,107,103,160,134.3C213.3,165,267,176,320,155C373.3,134,427,83,480,87.8C533.3,93,587,155,640,165.3C693.3,176,747,134,800,129.2C853.3,124,907,155,960,155C1013.3,155,1067,124,1120,98.2C1173.3,72,1227,52,1280,41.3C1333.3,31,1387,31,1440,41.3C1493.3,52,1547,72,1600,98.2C1653.3,124,1707,155,1760,139.5C1813.3,124,1867,62,1920,36.2C1973.3,10,2027,21,2080,46.5C2133.3,72,2187,114,2240,108.5C2293.3,103,2347,52,2400,25.8C2453.3,0,2507,0,2560,15.5C2613.3,31,2667,62,2720,82.7C2773.3,103,2827,114,2880,103.3C2933.3,93,2987,62,3040,51.7C3093.3,41,3147,52,3200,67.2C3253.3,83,3307,103,3360,139.5C3413.3,176,3467,227,3520,232.5C3573.3,238,3627,196,3680,186C3733.3,176,3787,196,3813,206.7L3840,217L3840,310L3813.3,310C3786.7,310,3733,310,3680,310C3626.7,310,3573,310,3520,310C3466.7,310,3413,310,3360,310C3306.7,310,3253,310,3200,310C3146.7,310,3093,310,3040,310C2986.7,310,2933,310,2880,310C2826.7,310,2773,310,2720,310C2666.7,310,2613,310,2560,310C2506.7,310,2453,310,2400,310C2346.7,310,2293,310,2240,310C2186.7,310,2133,310,2080,310C2026.7,310,1973,310,1920,310C1866.7,310,1813,310,1760,310C1706.7,310,1653,310,1600,310C1546.7,310,1493,310,1440,310C1386.7,310,1333,310,1280,310C1226.7,310,1173,310,1120,310C1066.7,310,1013,310,960,310C906.7,310,853,310,800,310C746.7,310,693,310,640,310C586.7,310,533,310,480,310C426.7,310,373,310,320,310C266.7,310,213,310,160,310C106.7,310,53,310,27,310L0,310Z"></path><defs><linearGradient id="sw-gradient-1" x1="0" x2="0" y1="1" y2="0"><stop stop-color="rgba(135, 189, 228, 1)" offset="0%"></stop><stop stop-color="rgba(135, 189, 228, 1)" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 50px); opacity:0.9" fill="url(#sw-gradient-1)" d="M0,186L26.7,186C53.3,186,107,186,160,170.5C213.3,155,267,124,320,134.3C373.3,145,427,196,480,222.2C533.3,248,587,248,640,253.2C693.3,258,747,269,800,258.3C853.3,248,907,217,960,170.5C1013.3,124,1067,62,1120,46.5C1173.3,31,1227,62,1280,62C1333.3,62,1387,31,1440,56.8C1493.3,83,1547,165,1600,175.7C1653.3,186,1707,124,1760,87.8C1813.3,52,1867,41,1920,77.5C1973.3,114,2027,196,2080,191.2C2133.3,186,2187,93,2240,93C2293.3,93,2347,186,2400,196.3C2453.3,207,2507,134,2560,98.2C2613.3,62,2667,62,2720,72.3C2773.3,83,2827,103,2880,113.7C2933.3,124,2987,124,3040,108.5C3093.3,93,3147,62,3200,77.5C3253.3,93,3307,155,3360,175.7C3413.3,196,3467,176,3520,144.7C3573.3,114,3627,72,3680,72.3C3733.3,72,3787,114,3813,134.3L3840,155L3840,310L3813.3,310C3786.7,310,3733,310,3680,310C3626.7,310,3573,310,3520,310C3466.7,310,3413,310,3360,310C3306.7,310,3253,310,3200,310C3146.7,310,3093,310,3040,310C2986.7,310,2933,310,2880,310C2826.7,310,2773,310,2720,310C2666.7,310,2613,310,2560,310C2506.7,310,2453,310,2400,310C2346.7,310,2293,310,2240,310C2186.7,310,2133,310,2080,310C2026.7,310,1973,310,1920,310C1866.7,310,1813,310,1760,310C1706.7,310,1653,310,1600,310C1546.7,310,1493,310,1440,310C1386.7,310,1333,310,1280,310C1226.7,310,1173,310,1120,310C1066.7,310,1013,310,960,310C906.7,310,853,310,800,310C746.7,310,693,310,640,310C586.7,310,533,310,480,310C426.7,310,373,310,320,310C266.7,310,213,310,160,310C106.7,310,53,310,27,310L0,310Z"></path></svg>
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
