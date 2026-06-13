<?php
require 'config.php';

// লগইন চেক
if(isset($_POST['login'])){
    if($_POST['email'] === ADMIN_EMAIL && password_verify($_POST['password'], ADMIN_PASS_HASH)){
        $_SESSION['admin_logged'] = true;
    } else { $error = "ভুল ইমেইল বা পাসওয়ার্ড"; }
}
if(isset($_GET['logout'])){ session_destroy(); header("Location: admin"); exit; }
if(!isset($_SESSION['admin_logged'])){
    // লগইন পেজ দেখাও
    echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Login</title><style>*{margin:0;box-sizing:border-box}body{background:#0f172a;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;min-height:100vh}.box{background:#1e293b;padding:40px;border-radius:12px;width:90%;max-width:400px}input{width:100%;padding:12px;margin:10px 0;background:#334155;border:1px solid #475569;color:#fff;border-radius:6px}button{width:100%;padding:12px;background:#3b82f6;border:none;color:#fff;border-radius:6px;cursor:pointer;font-weight:600}h2{text-align:center;margin-bottom:20px}.err{color:#f87171;text-align:center;margin:10px 0}</style></head><body><div class="box"><h2>booeb.co অ্যাডমিন</h2>'.($error??'').'<form method="POST"><input type="email" name="email" placeholder="ইমেইল" required><input type="password" name="password" placeholder="পাসওয়ার্ড" required><button name="login">লগইন</button></form></div></body></html>'; exit;
}

// নতুন লিংক তৈরি
if(isset($_POST['create'])){
    $url = $_POST['url'];
    $code = $_POST['code'] ?: substr(md5(time().rand()),0,4);
    $title = $_POST['title'];
    $pass = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : NULL;
    $exp = $_POST['expires'] ?: NULL;
    
    $stmt = $conn->prepare("INSERT INTO links (code, original_url, title, password, expires_at) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $code, $url, $title, $pass, $exp);
    $stmt->execute();
    $msg = "লিংক তৈরি হয়েছে: ".SITE_URL."/".$code;
}

// ডিলিট
if(isset($_GET['del'])){
    $conn->query("DELETE FROM links WHERE id=".(int)$_GET['del']);
    header("Location: admin"); exit;
}

$links = $conn->query("SELECT * FROM links ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="bn"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>booeb.co ড্যাশবোর্ড</title>
<style>
*{margin:0;box-sizing:border-box}body{background:#0f172a;color:#e2e8f0;font-family:system-ui;padding:20px}
.container{max-width:1200px;margin:auto}h1{margin-bottom:20px}
.grid{display:grid;grid-template-columns:1fr 2fr;gap:20px;margin-bottom:30px}
.card{background:#1e293b;padding:20px;border-radius:12px;border:1px solid #334155}
input,select{width:100%;padding:10px;margin:8px 0;background:#0f172a;border:1px solid #475569;color:#fff;border-radius:6px}
button{padding:10px 20px;background:#3b82f6;border:none;color:#fff;border-radius:6px;cursor:pointer;font-weight:600}
button:hover{background:#2563eb}table{width:100%;border-collapse:collapse}
th,td{padding:12px;text-align:left;border-bottom:1px solid #334155;font-size:14px}
th{background:#0f172a}a{color:#60a5fa;text-decoration:none}
.badge{background:#334155;padding:4px 8px;border-radius:4px;font-size:12px}
.btn-sm{padding:5px 10px;font-size:12px;margin:2px}
.btn-red{background:#dc2626}.btn-green{background:#16a34a}
.success{background:#14532d;padding:10px;border-radius:6px;margin:10px 0;color:#86efac}
.flex{display:flex;justify-content:space-between;align-items:center;margin-bottom:20px}
@media(max-width:768px){.grid{grid-template-columns:1fr}}
</style></head><body>
<div class="container">
<div class="flex">
    <h1>🔗 booeb.co ড্যাশবোর্ড</h1>
    <a href="?logout=1"><button class="btn-red">লগআউট</button></a>
</div>

<?php if(isset($msg)) echo "<div class='success'>$msg</div>"; ?>

<div class="grid">
<div class="card">
    <h3>নতুন শর্ট লিংক</h3>
    <form method="POST">
        <input type="url" name="url" placeholder="https://example.com" required>
        <input type="text" name="code" placeholder="কাস্টম কোড (খালি রাখলে অটো)">
        <input type="text" name="title" placeholder="টাইটেল">
        <input type="password" name="password" placeholder="পাসওয়ার্ড প্রটেকশন (অপশনাল)">
        <input type="datetime-local" name="expires" title="Expiry Date">
        <button name="create">লিংক তৈরি করুন</button>
    </form>
</div>

<div class="card">
    <h3>পরিসংখ্যান</h3>
    <?php
    $total = $conn->query("SELECT COUNT(*) as c FROM links")->fetch_assoc()['c'];
    $clicks = $conn->query("SELECT SUM(clicks) as c FROM links")->fetch_assoc()['c'];
    ?>
    <p>মোট লিংক: <b><?=$total?></b></p>
    <p>মোট ক্লিক: <b><?=$clicks?></b></p>
</div>
</div>

<div class="card">
    <h3>সকল লিংক</h3>
    <table>
        <tr><th>শর্ট লিংক</th><th>টাইটেল</th><th>ক্লিক</th><th>তৈরি</th><th>অ্যাকশন</th></tr>
        <?php while($l = $links->fetch_assoc()): ?>
        <tr>
            <td>
                <a href="<?=SITE_URL.'/'.$l['code']?>" target="_blank"><?=SITE_URL.'/'.$l['code']?></a>
                <?php if($l['password']) echo '<span class="badge">🔒</span>'; ?>
                <?php if($l['expires_at']) echo '<span class="badge">⏰</span>'; ?>
            </td>
            <td><?=$l['title']?:'-'?></td>
            <td><b><?=$l['clicks']?></b></td>
            <td><?=date('d M',strtotime($l['created_at']))?></td>
            <td>
                <button class="btn-sm btn-green" onclick="navigator.clipboard.writeText('<?=SITE_URL.'/'.$l['code']?>')">কপি</button>
                <a href="qr/<?=$l['code']?>"><button class="btn-sm">QR</button></a>
                <a href="?del=<?=$l['id']?>" onclick="return confirm('ডিলিট করবেন?')"><button class="btn-sm btn-red">ডিলিট</button></a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</div>
</body>
</html>
