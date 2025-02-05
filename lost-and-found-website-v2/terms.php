<?php
session_start();
include('includes/db.php'); // Include the database connection

// Check if the user is logged in
$user_logged_in = isset($_SESSION['user_id']); // Check if 'user_id' is set in the session

// Default values for guests
$user = 'Guest';
$profile_pic = 'css/img/user.png'; // Default profile picture

if ($user_logged_in) {
    $user_id = $_SESSION['user_id']; // Retrieve the user ID from the session

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user details from the database
    $stmt = $conn->prepare("SELECT name, photo FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_name, $db_profile_pic);

    if ($stmt->fetch()) {
        $name = !empty($db_name) ? $db_name : $name; // Use fetched name or default
        $profile_pic = !empty($db_profile_pic) ? $db_profile_pic : $profile_pic; // Use fetched profile pic or default
    }
    $stmt->close();
}
?>
<?php include('web_navbar.php');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Privacy Policy</title>
    <link rel="stylesheet" href="css/terms.css">
</head>
<body>
    <div class="container">
        <h2>Terms and Privacy Policy</h2>
        <section>
            <h3>1. Terms of Service</h3>
            <p>By using our Lost and Found website, you agree to the following terms:</p>
            <ul>
                <li><strong>Account Responsibility:</strong> You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account. Notify us immediately of any unauthorized access.</li>
                <li><strong>Use of Services:</strong> You agree to use our website only for lawful purposes and in accordance with our terms. You may not use our services to post illegal, offensive, harmful, or misleading content.</li>
                <li><strong>Content Ownership:</strong> All content posted on our website, including but not limited to images, text, and videos, is the property of the Lost and Found platform or its respective owners. You must not infringe on copyrights or other intellectual property rights.</li>
                <li><strong>Termination of Account:</strong> We reserve the right to suspend or terminate your account if you violate any of the terms outlined in this policy. This includes, but is not limited to, fraudulent activities or posting false claims.</li>
                <li><strong>Disclaimer of Liability:</strong> We are not responsible for any damages, losses, or disputes resulting from your use of the website or any third-party services linked to it. Use of our website is at your own risk.</li>
                <li><strong>Third-Party Links:</strong> Our website may contain links to third-party websites. We do not endorse or take responsibility for the content, privacy policies, or practices of these websites.</li>
                <li><strong>Prohibited Activities:</strong> You agree not to engage in activities that could harm the website, its users, or the platform, such as hacking, spreading malware, or using automated systems to extract data without permission.</li>
            </ul>
        </section>
        
        <section>
    <h3>2. Privacy Policy</h3>
    <p>We take your privacy seriously. This Privacy Policy explains how we collect, use, and protect your personal information:</p>
    <ul>
        <li><strong>Information Collection:</strong> We collect personal information that you provide to us, such as your name, email address, and details about lost or found items. We may also collect non-personal data, such as browser type and IP address, for analytics.</li>
        <li><strong>Use of Information:</strong> The information we collect is used to provide you with the best possible service, facilitate item recovery, communicate with you, and improve the functionality of our website.</li>
        <li><strong>Cookies:</strong> We may use cookies to enhance your user experience and track website usage for analytics purposes. You can control the use of cookies through your browser settings. Disabling cookies may limit certain features.</li>
        <li><strong>Data Sharing:</strong> We do not share your personal information with third parties unless required by law, necessary for operational purposes (e.g., payment processing), or with your explicit consent.</li>
        <li><strong>Data Security:</strong> We employ industry-standard security measures to protect your personal information from unauthorized access, alteration, or disclosure. However, no system is entirely secure, and we cannot guarantee absolute protection.</li>
        <li><strong>Your Rights:</strong> You have the right to access, correct, or delete your personal data at any time by contacting us directly. You may also request a copy of the data we hold about you.</li>
        <li><strong>Data Retention:</strong> We retain personal data only for as long as necessary to fulfill the purposes outlined in this policy or as required by law.</li>
    </ul>
</section>

<section>
    <h3>3. Changes to the Terms and Privacy Policy</h3>
    <p>We may update these terms and privacy policy periodically. Any changes will be posted on this page with the updated date. Please review this page regularly to stay informed about our terms and privacy practices. Continued use of our services after changes indicates your acceptance of the updated terms.</p>
</section>

<section>
    <h3>4. User Responsibilities</h3>
    <ul>
        <li>Ensure all information provided on the platform is accurate and truthful.</li>
        <li>Report suspicious or fraudulent activity promptly.</li>
        <li>Comply with all applicable laws while using the website.</li>
        <li>Refrain from using the platform to harass, intimidate, or harm others.</li>
    </ul>
</section>

<section>
    <h3>5. Contact Information</h3>
    <p>If you have any questions, concerns, or feedback about our Terms & Privacy Policy, please contact us through the following channels:</p>
    <ul>
        <li>Email: <a href="mailto:support@lostandfound.com">support@lostandfound.com</a></li>
        <li>Phone: +1 234 567 890</li>
        <li>Address: 123 Lost & Found St., Example City, Country</li>
    </ul>
</section>

<section>
    <h3>6. Governing Law</h3>
    <p>These terms and conditions are governed by and construed in accordance with the laws of [Your Country/Region]. Any disputes arising out of or related to these terms shall be subject to the exclusive jurisdiction of the courts in [Your Country/Region].</p>
</section>
    </div>

</body>

<?php
include 'includes/footer.php';
?>

</html>
