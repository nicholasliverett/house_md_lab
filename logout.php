<?php
require_once 'includes.php';

// Destroy session
session_destroy();

// Redirect to home
header('Location: index.php');
exit;
?>