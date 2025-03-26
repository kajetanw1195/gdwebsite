<?php
require_once 'includes/Database.php';

// Get database instance
$db = Database::getInstance();

// Test contact insertion with validation
echo "Testing contact insertion...\n";

// Test valid contact
$result = $db->insertContact("John Doe", "john@example.com", "This is a valid test message that is long enough.");
if ($result) {
    echo "Valid contact inserted successfully!\n";
} else {
    echo "Error inserting valid contact: " . $db->getLastError() . "\n";
}

// Test invalid email
$result = $db->insertContact("John Doe", "invalid-email", "This is a valid test message that is long enough.");
if ($result) {
    echo "Invalid email contact inserted successfully!\n";
} else {
    echo "Error inserting invalid email contact: " . $db->getLastError() . "\n";
}

// Test invalid name
$result = $db->insertContact("John123", "john@example.com", "This is a valid test message that is long enough.");
if ($result) {
    echo "Invalid name contact inserted successfully!\n";
} else {
    echo "Error inserting invalid name contact: " . $db->getLastError() . "\n";
}

// Test short message
$result = $db->insertContact("John Doe", "john@example.com", "Too short");
if ($result) {
    echo "Short message contact inserted successfully!\n";
} else {
    echo "Error inserting short message contact: " . $db->getLastError() . "\n";
}

// Test getting services
echo "\nTesting services retrieval...\n";
$services = $db->getServices();
if ($services) {
    echo "Services retrieved successfully!\n";
    foreach ($services as $service) {
        echo "- {$service['title']}: {$service['description']}\n";
    }
} else {
    echo "Error retrieving services: " . $db->getLastError() . "\n";
}

// Test getting recent contacts with pagination
echo "\nTesting recent contacts retrieval...\n";
$contacts = $db->getRecentContacts(1, 5);
if ($contacts) {
    echo "Recent contacts retrieved successfully!\n";
    foreach ($contacts as $contact) {
        echo "- {$contact['name']} ({$contact['email']}): {$contact['message']}\n";
    }
} else {
    echo "Error retrieving recent contacts: " . $db->getLastError() . "\n";
}

// Test getting total contacts
echo "\nTesting total contacts count...\n";
$total = $db->getTotalContacts();
if ($total !== false) {
    echo "Total contacts: $total\n";
} else {
    echo "Error getting total contacts: " . $db->getLastError() . "\n";
}
?> 