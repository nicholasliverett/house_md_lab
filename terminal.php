<?php
require_once 'includes.php';

echo get_header("Admin Terminal");

echo <<<HTML 
<div class="panel">
    <h2>Admin Terminal</h2>
HTML;

// Check if user is admin
if (!is_admin()) {
    // Output HTML page with message and redirect logic
    echo <<<HTML
    <div class="message">You are not an administrator.</div>
    <div class="countdown">Redirecting to homepage in <span id="count">10</span> seconds...</div>

    <script>
        let seconds = 10;
        const countElement = document.getElementById('count');
        
        const timer = setInterval(() => {
            seconds--;
            countElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(timer);
                window.location.href = 'index.php';
            }
        }, 1000);
    </script>
HTML;
    exit;
}
?>
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
</div>

<?php echo get_footer(); ?>