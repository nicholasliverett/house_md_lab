<?php
require_once 'includes.php';

// Check if user is admin
if (!is_admin()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $command = $_POST['command'];
    $output = [];
    
    // Vulnerable command execution
    exec($command, $output, $return_var);
    
    $_SESSION['command'] = $command;
    $_SESSION['terminal_output'] = implode("\n", $output);
}

header('Location: terminal.php');
exit;
?>