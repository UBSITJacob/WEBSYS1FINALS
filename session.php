<?php
session_start();

// Basic session security controls
if(!isset($_SESSION['initiated'])){
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
    $_SESSION['last_regen'] = time();
}
// Timeout after 30 minutes of inactivity
$now = time();
$timeout = 1800; // 30 minutes
if(isset($_SESSION['last_activity']) && ($now - $_SESSION['last_activity'] > $timeout)){
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = $now;
// Regenerate session id every 10 minutes to limit fixation
if(isset($_SESSION['last_regen']) && ($now - $_SESSION['last_regen'] > 600)){
    session_regenerate_id(true);
    $_SESSION['last_regen'] = $now;
}

function login_user($account){
    session_regenerate_id(true);
    $_SESSION['account_id'] = $account['id'];
    $_SESSION['role'] = $account['role'];
    $_SESSION['email'] = $account['email'];
    $_SESSION['first_login_required'] = $account['first_login_required'] ?? 0;
}

function is_logged_in(){
    return isset($_SESSION['account_id']);
}

function require_role($role){
    if(!isset($_SESSION['role']) || $_SESSION['role'] !== $role){
        header('Location: index.php');
        exit;
    }
}

function require_login(){
    if(!isset($_SESSION['account_id'])){
        header('Location: index.php');
        exit;
    }
}
