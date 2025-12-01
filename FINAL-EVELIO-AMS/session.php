<?php
session_start();

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