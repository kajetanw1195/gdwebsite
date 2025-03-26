<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'golebi_dwor');
define('DB_USER', 'golebi_dwor');
define('DB_PASS', 'golebi_dwor123');

// Security settings
define('HASH_COST', 12); // For password hashing
define('MAX_LOGIN_ATTEMPTS', 3);
define('LOGIN_TIMEOUT', 900); // 15 minutes in seconds
?> 