// Simulated data
const lostItems = [
    { id: 1, name: "Black Wallet", description: "Lost at Park on 2nd Jan" },
    { id: 2, name: "Gold Ring", description: "Lost in Shopping Mall" },
  ];
  
  const foundItems = [
    { id: 1, name: "Blue Backpack", description: "Found near Bus Station" },
    { id: 2, name: "Keychain", description: "Found in Library" },
  ];
  
  const notifications = [
    "Black Wallet reported as lost",
    "Gold Ring reported as lost",
    "Blue Backpack reported as found",
  ];
  
  // Load Items
  function loadItems() {
    const lostSection = document.querySelector('.lost .item-list');
    const foundSection = document.querySelector('.found .item-list');
  
    // Load Lost Items
    lostItems.forEach(item => {
      const div = document.createElement('div');
      div.className = 'item';
      div.innerHTML = `<strong>${item.name}</strong><p>${item.description}</p>`;
      lostSection.appendChild(div);
    });
  
    // Load Found Items
    foundItems.forEach(item => {
      const div = document.createElement('div');
      div.className = 'item';
      div.innerHTML = `<strong>${item.name}</strong><p>${item.description}</p>`;
      foundSection.appendChild(div);
    });
  }
  
  // Load Notifications
  function loadNotifications() {
    const notificationList = document.getElementById('notification-list');
    notifications.forEach(notification => {
      const li = document.createElement('li');
      li.textContent = notification;
      notificationList.appendChild(li);
    });
  }
  
  // Initialize Page
  document.addEventListener('DOMContentLoaded', () => {
    loadItems();
    loadNotifications();
  });
  