<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Lost and Found</title>
    <link rel="stylesheet" href="assets/styles.css">
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<style>
        /* FAQ Page Styling */
.faq-container {
    margin-left: 220px; /* Adjust based on your sidebar width */
    padding: 20px;
    background-color: #f4f4f4;
}

.faq-container h1 {
    text-align: center;
    font-size: 2.5em;
    color: #256EBB;
    margin-bottom: 20px;
}

.faq-item {
    background-color: white;
    border: 1px solid #ddd;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 5px;
}

.faq-item h2 {
    font-size: 1.5em;
    color: #333;
    display: flex;
    align-items: center;
}

.faq-item h2 i {
    margin-right: 10px;
    font-size: 1.8em;
    color: #256EBB;
}

.faq-item p {
    font-size: 1em;
    color: #555;
    margin-top: 10px;
}

.faq-item h2:hover {
    color: #256EBB;
    cursor: pointer;
}

    </style>
<body>
   
<?php require_once(__DIR__.'/sidenav.php'); ?>
    <!-- FAQ Section -->
    <div class="faq-container">
        <h1>Frequently Asked Questions</h1>

        <div class="faq-item">
            <h2><i class="fas fa-search"></i> How do I report a lost item?</h2>
            <p>If you've lost an item, you can report it through our website by clicking the "Report Lost Item" button. Provide as much detail as possible, such as a description of the item, when and where you lost it, and any other identifying features.</p>
        </div>

        <div class="faq-item">
            <h2><i class="fas fa-handshake"></i> How can I claim a found item?</h2>
            <p>If you've found an item, you can submit it through our "Found Item" form. Please include the item description and location where you found it, and we will assist in reuniting it with the owner.</p>
        </div>

        <div class="faq-item">
            <h2><i class="fas fa-question-circle"></i> What should I do if I can't find my lost item?</h2>
            <p>If your item hasn't been found yet, keep checking the website for any new updates. You can also set up alerts to be notified if an item matching your description is found.</p>
        </div>

        <div class="faq-item">
            <h2><i class="fas fa-info-circle"></i> What types of items can be reported?</h2>
            <p>You can report lost and found items such as personal belongings (e.g., phones, wallets, bags), clothing, pets, and other valuable items. The more details you provide, the easier it will be for us to match the item with its rightful owner.</p>
        </div>

        <div class="faq-item">
            <h2><i class="fas fa-phone-alt"></i> How do I contact customer support?</h2>
            <p>If you have any further questions or need assistance, you can reach our customer support team through the "Contact Us" page or by email at support@lostandfound.com.</p>
        </div>
    </div>

    <script src="assets/script.js"></script>
</body>
</html>
