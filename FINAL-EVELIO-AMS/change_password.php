<?php
include "pdo_functions.php";
include "session.php";
require_login();
$pdo = new pdoCRUD();

if(isset($_POST['change'])){
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if($new !== $confirm){
        $error = "Passwords do not match";
    }else{
        $ok = $pdo->changePassword($_SESSION['account_id'],$current,$new);
        if($ok){
            header('Location: index.php');
            exit;
        }else{
            $error = "Invalid current password or weak new password";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change Password</title>
    <style>
        body{font-family: Arial, sans-serif;}
        .container{max-width:480px;margin:40px auto;border:1px solid #ddd;padding:20px;border-radius:8px;}
        input{width:100%;padding:10px;margin:8px 0;}
        button{padding:10px 14px;}
    </style>
</head>
<body>
    <div class="container">
        <h3>Change Password</h3>
        <?php if(isset($error)){ echo '<div style="color:red;">'.htmlspecialchars($error,ENT_QUOTES,'UTF-8').'</div>'; } ?>
        <form method="post">
            <input type="password" name="current_password" placeholder="Current Password" required>
            <input type="password" name="new_password" placeholder="New Password (min 8)" minlength="8" required>
            <input type="password" name="confirm_password" placeholder="Confirm New Password" minlength="8" required>
            <button name="change">Change Password</button>
        </form>
    </div>
    
</body>
</html>

