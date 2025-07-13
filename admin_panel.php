<?php
require_once 'includes.php';

$lan_access = is_lan_client() ? 'GRANTED' : 'DENIED';
$ip = $_SERVER['REMOTE_ADDR'];

echo get_header("Admin Panel", "You can have all the facts and still be wrong.");

echo <<<HTML
    <div class="panel">
        <h2>Administrator Portal</h2>
        
        <div class="access-status">
            <div class="indicator {$lan_access}"></div>
            <span>LAN Access: {$lan_access}</span>
        </div>
        <p>Client IP: $ip</p>
HTML;

if ($lan_access == 'GRANTED') {
    // Simulated admin actions
    $message = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $action = $_POST['action'] ?? '';
        
        $message = "Action performed: $action for user $username";
    }
    
    if ($message) {
        echo "<div class='vuln-section'><strong>$message</strong></div>";
    }
    
    echo <<<HTML
        <div class="admin-content">
            <h3>Hospital Management System</h3>
            
            <div class="vuln-section">
                <h3>User Privilege Management (CSRF Vulnerable)</h3>
                <form method="POST" action="submit_review.php">
                    <div class="form-group">
                        <label>Username:</label>
                        <input type="text" name="username" placeholder="Enter username" required>
                    </div>
                    <div class="form-group">
                        <label>Action:</label>
                        <select name="action">
                            <option value="grant_admin">Grant Admin Privileges</option>
                            <option value="revoke_admin">Revoke Admin Privileges</option>
                            <option value="reset_password">Reset Password</option>
                        </select>
                    </div>
                    <button type="submit">Execute Action</button>
                </form>
                <p>This form is vulnerable to CSRF attacks as it lacks anti-CSRF tokens.</p>
            </div>
            
            <h3>System Status</h3>
            <ul>
                <li>Patient Records: 1,248</li>
                <li>Diagnostic Cases: 87</li>
                <li>Active Staff: 42</li>
            </ul>
        </div>
HTML;
} else {
    echo <<<HTML
        <div class="access-denied">
            <h3>Access Restricted</h3>
            <p>This admin panel is only accessible from the hospital's internal network.</p>
            <p>Please connect to the Princeton-Plainsboro LAN to access these controls.</p>
            <div class="warning">
                <i class="fas fa-exclamation-triangle"></i>
                External access prohibited
            </div>
        </div>
HTML;
}

echo '</div>'; // Close panel
echo get_footer();
?>