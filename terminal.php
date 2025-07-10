<?php
require_once 'includes.php';

echo get_header("Admin Terminal");
display_user_status();
echo get_navigation();

// Check if user is admin
if (!is_admin()) {
    header('Location: index.php');
    exit;
}
?>

<div class="panel">
    <h2>Admin Terminal</h2>
    <div class="terminal">
        Princeton-Plainsboro Hospital System Terminal v2.4
        <br>Type 'help' for available commands
        <br><br>
        <?php 
        if (isset($_SESSION['terminal_output'])) {
            echo "> {$_SESSION['command']}\n";
            echo $_SESSION['terminal_output'];
            unset($_SESSION['terminal_output']);
        }
        ?>
    </div>
    
    <form action="execute_command.php" method="POST" class="terminal-input">
        <input type="text" name="command" placeholder="Enter command..." required>
        <button type="submit">Execute</button>
    </form>
    
    <div class="vuln-section">
        <h3>Command Injection Vulnerability</h3>
        <p>This terminal executes system commands without proper validation.</p>
        <p>Example commands:</p>
        <ul>
            <li><code>ls -la</code> - List directory contents</li>
            <li><code>cat /etc/passwd</code> - View system passwords</li>
            <li><code>rm -rf /</code> - Dangerous delete command</li>
        </ul>
    </div>
</div>

<?php echo get_footer(); ?>