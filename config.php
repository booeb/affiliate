<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'u123456789_user'); // আপনার DB ইউজার
define('DB_PASS', 'your_password'); // আপনার DB পাসওয়ার্ড  
define('DB_NAME', 'u123456789_booeb'); // আপনার DB নাম
define('SITE_URL', 'https://booeb.co');
define('ADMIN_EMAIL', 'booeb.com@gmail.com');

// noorhasan1N এর hash করা পাসওয়ার্ড
define('ADMIN_PASS_HASH', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) die("DB Error");
session_start();
date_default_timezone_set('Asia/Dhaka');

function is_mobile() {
    return preg_match("/(android|iphone|ipad|mobile)/i", $_SERVER['HTTP_USER_AGENT']);
}
?>
