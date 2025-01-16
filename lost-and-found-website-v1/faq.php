<?php
session_start();
include('includes/db.php');
if (!isset($_SESSION['user'])) {
    include('navbar.php');
}else{
    include('web_navbar.php');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Lost and Found</title>
    <link rel="stylesheet" href="assets/terms.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="container">
    <h2>Frequently Asked Questions (FAQ)</h2>

    <section>
        <h3>1. What is the Lost and Found website?</h3>
        <p>The Lost and Found website is a platform designed to help individuals report and recover lost items or find items they may have lost. Users can post information about lost or found items and connect with others to reunite with their possessions.</p>
    </section>

    <section>
        <h3>2. How do I create an account?</h3>
        <p>To create an account, click on the "Sign Up" button on the homepage and fill out the required information, such as your name, email address, and a password. After signing up, you'll receive a verification email to confirm your account.</p>
    </section>

    <section>
        <h3>3. How do I report a lost item?</h3>
        <p>To report a lost item, log into your account and navigate to the "Report Lost Item" section. Provide details about the item, its description, and where it was lost. You may also upload an image of the item to help others identify it.</p>
    </section>

    <section>
        <h3>4. How do I claim a found item?</h3>
        <p>If you've found an item and wish to claim it, navigate to the "Found Items" section and fill out the form with the item details. Include a brief description and any additional information that might help the owner identify it. You can also upload a picture of the found item.</p>
    </section>

    <section>
        <h3>5. How can I protect my privacy on the website?</h3>
        <p>We take your privacy seriously. We will never share your personal information with third parties unless required by law or with your explicit consent. You can manage your privacy settings in your account preferences, and you have the right to request access, correction, or deletion of your personal data at any time.</p>
    </section>

    <section>
        <h3>6. What should I do if I forget my password?</h3>
        <p>If you've forgotten your password, click on the "Forgot Password" link on the login page. Enter your registered email address, and we'll send you a password reset link to regain access to your account.</p>
    </section>

    <section>
        <h3>7. Can I report an item without signing up?</h3>
        <p>While signing up is encouraged to access all features of the Lost and Found platform, you can still report lost or found items as a guest. However, creating an account will allow you to track your reports, receive notifications, and engage with others more easily.</p>
    </section>

    <section>
        <h3>8. How do I contact customer support?</h3>
        <p>If you have any questions or need assistance, you can contact our customer support team at:</p>
        <ul>
            <li>Email: <a href="mailto:support@lostandfound.com">support@lostandfound.com</a></li>
            <li>Phone: +1 (234) 567-8901</li>
        </ul>
    </section>
</div>

</body>

<?php include 'assets/footer.php';?>



</html>
