<?php
// Sample data for demonstration
$lostItems = [
    ["name" => "Black Wallet", "description" => "Lost at Park on 2nd Jan"],
    ["name" => "Gold Ring", "description" => "Lost in Shopping Mall"],
];

$foundItems = [
    ["name" => "Blue Backpack", "description" => "Found near Bus Station"],
    ["name" => "Keychain", "description" => "Found in Library"],
];

$notifications = [
    "Black Wallet reported as lost",
    "Gold Ring reported as lost",
    "Blue Backpack reported as found",
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lost and Found</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
</head>
<body>
  <header>
    <div class="logo">
      <img src="logo.png" alt="Lost and Found Logo">
      <h1>Lost and Found</h1>
    </div>
    <nav>
      <ul>
        <li><a href="#" id="home">Home</a></li>
        <li><a href="report.php" id="report">Item Report</a></li>
        <li><a href="profile.php" id="profile">Profile</a></li>
        <li><a href="login.php" id="signup">Sign Up</a></li> <!-- New Sign Up Link -->
        <li class="notification">
          <a href="#" id="notifications">Notifications</a>
          <div class="dropdown">
            <ul id="notification-list">
              <?php foreach ($notifications as $notification): ?>
                <li><?= htmlspecialchars($notification) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        </li>
      </ul>
    </nav>
  </header>
  <main>
    <section class="lost">
      <h2>Lost Items</h2>
      <div class="item-list">
        <?php foreach ($lostItems as $item): ?>
          <div class="item">
            <strong><?= htmlspecialchars($item['name']) ?></strong>
            <p><?= htmlspecialchars($item['description']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
    <section class="found">
      <h2>Found Items</h2>
      <div class="item-list">
        <?php foreach ($foundItems as $item): ?>
          <div class="item">
            <strong><?= htmlspecialchars($item['name']) ?></strong>
            <p><?= htmlspecialchars($item['description']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>
</body>
</html>
