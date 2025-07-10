<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $users = get_users();
    
    if (!isset($users['patients'][$username])) {
        $users['patients'][$username] = $password;
        save_users($users);
        
        $_SESSION['user'] = $username;
        $_SESSION['role'] = 'patient';
        header('Location: index.php');
        exit;
    }
    
    $_SESSION['error'] = 'Username already exists';
    header('Location: index.php');
    exit;
}

// Redirect if not POST
header('Location: index.php');
?>