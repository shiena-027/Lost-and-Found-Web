<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in first.");
}

include('includes/db.php');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];
$photo = 'assets/img/user.png';

// Fetch the current profile picture from the database
$query = "SELECT photo, name FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($db_picture, $name);
if ($stmt->fetch() && $db_picture) {
    $photo = $db_picture;
}
$stmt->close();

// Handle profile picture upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES["photo"]["name"]);
    $target_file = $target_dir . uniqid() . "_" . $file_name;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Validate file type
    if (in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            // Update the database with the new profile picture path
            $update_query = "UPDATE users SET photo = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->bind_param("si", $target_file, $user_id);
            $update_stmt->execute();
            $update_stmt->close();

            // Update the displayed profile picture
            $photo = $target_file;
        } else {
            $_SESSION['error'] = 'Error uploading file.';
        }
    } else {
        $_SESSION['error'] = 'Invalid file type. Only JPG, JPEG, PNG, and GIF are allowed.';
    }
} elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] !== 0) {
    $_SESSION['error'] = 'An error occurred during the file upload.';
}

// Handle profile updates
if (isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];

    // Update name
    if (!empty($_POST['name'])) {
        $name = $_POST['name'];
        $query = "UPDATE users SET name = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $name, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update email
    if (!empty($_POST['email'])) {
        $email = $_POST['email'];
        $query = "UPDATE users SET email = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Update password
    if (!empty($_POST['new_password'])) {
        if (empty($_POST['old_password'])) {
            $_SESSION['error'] = 'Old password is required if changing the password.';
        } else {
            $old_password = $_POST['old_password'];
            $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

            $query = "SELECT password FROM users WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();

            if (password_verify($old_password, $hashed_password)) {
                $query = "UPDATE users SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("si", $new_password, $user_id);
                $stmt->execute();
                $_SESSION['message'] = 'Password updated successfully.';
            } else {
                $_SESSION['error'] = 'Old password is incorrect.';
            }
            $stmt->close();
        }
    }

    if (empty($_SESSION['error'])) {
        $_SESSION['message'] = 'Profile updated successfully.';
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings</title>
    <link rel="stylesheet" href="assets/profile_settings.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<?php include('web_navbar.php'); ?>

<body>
    <!-- Success Modal -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="modal success" id="successModal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-close" onclick="closeModal('successModal')">&times;</span>
                    <h2>Success</h2>
                </div>
                <div class="modal-body">
                    <p><?php echo $_SESSION['message']; ?></p>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['message']); endif; ?>

    <!-- Error Modal -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="modal error" id="errorModal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="modal-close" onclick="closeModal('errorModal')">&times;</span>
                    <h2>Error</h2>
                </div>
                <div class="modal-body">
                    <p><?php echo $_SESSION['error']; ?></p>
                </div>
            </div>
        </div>
    <?php unset($_SESSION['error']); endif; ?>

    <h2>&nbsp;</h2>
    <form action="profile_settings.php" method="post" enctype="multipart/form-data">
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="<?php echo htmlspecialchars($photo); ?>" alt="Profile Picture">
        </div>
        <h1><?php echo htmlspecialchars($name); ?></h1>

        <label for="name">Edit Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>">

        <label for="email">Edit Email:</label>
        <input type="email" id="email" name="email" value="">

        <label for="old_password">Old Password:</label>
        <input type="password" id="old_password" name="old_password">

        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password">

        <label for="photo">Upload New Profile Picture:</label>
        <input type="file" name="photo" id="photo">

        <button type="submit" name="update_profile">Save Changes</button>
        <button type="button" onclick="window.history.back()">Cancel</button>
    </form>

    <script>
        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }
    </script>

</body>

<?php include 'includes/footer.php'; ?>

</html>
