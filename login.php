<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $users = get_users();
    
    // Check employee credentials
    if (isset($users['employees'][$username])) {
        if (password_verify($password, $users['employees'][$username])) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = 'employee';
            header('Location: index.php');
            exit;
        }
    } 
    // Check patient credentials
    elseif (isset($users['patients'][$username])) {
        if (password_verify($password, $users['patients'][$username])) {
            $_SESSION['user'] = $username;
            $_SESSION['role'] = 'patient';
            header('Location: index.php');
            exit;
        }
    }
    
    // Invalid credentials
    $_SESSION['error'] = 'Invalid username or password';
    header('Location: index.php');
    exit;
}

// Redirect if not POST
header('Location: index.php');
?>