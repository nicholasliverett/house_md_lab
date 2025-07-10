<?php
require_once 'includes.php';

// Check if user is admin
if (!is_admin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $action = $_POST['action'];
    $users = get_users();
    
    if ($action === 'make_admin' && isset($users['patients'][$username])) {
        if (!in_array($username, $users['admins'])) {
            $users['admins'][] = $username;
            save_users($users);
            $_SESSION['message'] = "Made $username an administrator";
        }
    }
}

header('Location: admin_panel.php');
exit;
?>