
<?php
session_start();
include('includes/db.php');

// Ensure the user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: auth.php');
    exit();
}

$user = $_SESSION['user']; // Get user ID from session

// Fetch user information from the database (optional)
$query = "SELECT * FROM users WHERE id = '$user'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<?php include('navbar.php'); ?>
<?php include('sidenav.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Privacy Policy</title>
    <link rel="stylesheet" href="css/tos.css">
</head>
<body>
    <div class="container">
        <h1>Terms and Privacy Policy</h1>
        <section>
            <h2>1. Terms of Service</h2>
            <p>By using our Lost and Found website, you agree to the following terms:</p>
            <ul>
                <li><strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</li>
                <li><strong>Use of Services:</strong> You agree to use our website only for lawful purposes and in accordance with our terms. You may not use our services to post illegal, offensive, or harmful content.</li>
                <li><strong>Content Ownership:</strong> All content posted on our website, including but not limited to images, text, and videos, is the property of the Lost and Found platform or its respective owners.</li>
                <li><strong>Termination of Account:</strong> We reserve the right to suspend or terminate your account if you violate any of the terms outlined in this policy.</li>
                <li><strong>Disclaimer of Liability:</strong> We are not responsible for any damages or losses resulting from your use of the website or any third-party services linked to it.</li>
            </ul>
        </section>

        <section>
            <h2>2. Privacy Policy</h2>
            <p>We take your privacy seriously. This Privacy Policy explains how we collect, use, and protect your personal information:</p>
            <ul>
                <li><strong>Information Collection:</strong> We collect personal information that you provide to us when you use our website, such as your name, email address, and other relevant details.</li>
                <li><strong>Use of Information:</strong> The information we collect is used to provide you with the best possible service, to communicate with you, and to improve the functionality of our website.</li>
                <li><strong>Cookies:</strong> We may use cookies to enhance your user experience and track website usage for analytics purposes. You can control the use of cookies through your browser settings.</li>
                <li><strong>Data Sharing:</strong> We do not share your personal information with third parties unless required by law or with your explicit consent.</li>
                <li><strong>Data Security:</strong> We employ various security measures to protect your personal information from unauthorized access, alteration, or disclosure.</li>
                <li><strong>Your Rights:</strong> You have the right to access, correct, and delete your personal data at any time by contacting us directly.</li>
            </ul>
        </section>

        <section>
            <h2>3. Changes to the Terms and Privacy Policy</h2>
            <p>We may update these terms and privacy policy periodically. Any changes will be posted on this page with the updated date. Please review this page regularly to stay informed about our terms and privacy practices.</p>
        </section>

        <section>
            <h2>4. Contact Information</h2>
            <p>If you have any questions or concerns about our Terms & Privacy Policy, please contact us at:</p>
            <ul>
                <li>Email: <a href="mailto:support@lostandfound.com">support@lostandfound.com</a></li>
                <li>Phone: +1 234 567 890</li>
            </ul>
        </section>
    </div>



</body>
</html>
