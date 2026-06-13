<?php
require 'config.php';
$code = $conn->real_escape_string($_GET['c'] ?? '');

$stmt = $conn->prepare("SELECT * FROM links WHERE code=? AND (expires_at IS NULL OR expires_at > NOW()) LIMIT 1");
$stmt->bind_param("s", $code);
$stmt->execute();
$link = $stmt->get_result()->fetch_assoc();

if($link){
    // পাসওয়ার্ড প্রটেক্টেড হলে
    if($link['password'] && !isset($_SESSION['unlock_'.$code])){
        header("Location: ".SITE_URL."/unlock/".$code); exit;
    }

    // ক্লিক কাউন্ট + লগ
    $conn->query("UPDATE links SET clicks=clicks+1 WHERE id=".$link['id']);
    $device = is_mobile() ? 'Mobile' : 'Desktop';
    $ip = $_SERVER['REMOTE_ADDR'];
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    $conn->query("INSERT INTO click_logs (link_id, ip, device, referer) VALUES ({$link['id']}, '$ip', '$device', '$ref')");

    header("Location: ".$link['original_url'], true, 301); exit;
}
http_response_code(404);
include '404.php';
?>
