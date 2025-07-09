<?php
require_once 'includes.php';

// Simulated admin session
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = false;
}

// Simulated user database
$users = [
    "house" => ["password" => "vicodin", "admin" => true],
    "wilson" => ["password" => "cancer", "admin" => false],
    "cuddy" => ["password" => "admin123", "admin" => true]
];

$message = '';

// Login handling
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (isset($users[$username]) && $users[$username]['password'] === $password) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['username'] = $username;
        $message = "Login successful! Welcome, $username.";
    } else {
        $message = "Invalid credentials!";
    }
}

// CSRF vulnerable action
if (isset($_POST['toggle_admin']) && $_SESSION['admin_logged_in']) {
    $targetUser = $_POST['username'];
    
    if (isset($users[$targetUser])) {
        // Simulate privilege change
        $users[$targetUser]['admin'] = !$users[$targetUser]['admin'];
        $status = $users[$targetUser]['admin'] ? 'ADMIN' : 'USER';
        $message = "Privileges updated for $targetUser. New status: $status";
    } else {
        $message = "User not found: $targetUser";
    }
}

echo get_header("Admin Panel", "You can have all the facts and still be wrong.");

if ($_SESSION['admin_logged_in']) {
    $username = $_SESSION['username'];
    echo "<div class='admin-panel'>";
    echo "<h2>Welcome, Dr. $username</h2>";
    
    if ($message) {
        echo "<p><strong>$message</strong></p>";
    }
    
    // CSRF vulnerable form
    echo <<<HTML
        <div class="vuln-section">
            <h3>User Privilege Management</h3>
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Enter username" required>
                </div>
                <button type="submit" name="toggle_admin">Toggle Admin Privileges</button>
            </form>
            <p>Current users: house, wilson, cuddy</p>
        </div>
    HTML;
    
    echo "<p><a href='?logout=1'>Logout</a></p>";
    echo "</div>";
} else {
    echo <<<HTML
        <div class="admin-panel">
            <h2>Administrator Login</h2>
            <p>Access restricted to authorized medical staff</p>
            
            <form method="POST">
                <div class="form-group">
                    <input type="text" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" name="login">Login</button>
            </form>
    HTML;
    
    if ($message) {
        echo "<p style='color:#e74c3c;'>$message</p>";
    }
    
    echo "</div>";
}

// Logout handling
if (isset($_GET['logout'])) {
    $_SESSION['admin_logged_in'] = false;
    session_destroy();
    header("Location: admin_panel.php");
    exit;
}

echo get_footer();
?>