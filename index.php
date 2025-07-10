<?php
require_once 'includes.php';

echo get_header("Diagnostic Medicine", "It's never lupus... except when it is.");
?>

<div class="panel">
    <h2>Welcome to the Diagnostic Department</h2>
    <p>Under the direction of Dr. Gregory House, we specialize in complex medical cases that defy conventional diagnosis.</p>
    
    <?php if(!isset($_SESSION['user'])): ?>
        <div style="display: flex; gap: 20px; margin-top: 30px;">
            <div class="panel" style="flex: 1;">
                <h3>Login</h3>
                <form action="login.php" method="POST">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <button type="submit">Login</button>
                </form>
            </div>
            
            <div class="panel" style="flex: 1;">
                <h3>Patient Registration</h3>
                <form action="register.php" method="POST">
                    <div class="form-group">
                        <label for="reg_username">Username:</label>
                        <input type="text" name="username" id="reg_username" required>
                    </div>
                    <div class="form-group">
                        <label for="reg_password">Password:</label>
                        <input type="password" name="password" id="reg_password" required>
                    </div>
                    <button type="submit">Register</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php echo get_footer();
?>