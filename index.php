<?php
require_once 'includes.php';

echo get_header("Diagnostic Medicine", "It's never lupus... except when it is.");

echo <<<HTML
    <h2>Welcome to the Diagnostic Department</h2>
    <p>Under the direction of Dr. Gregory House, we specialize in complex medical cases that defy conventional diagnosis.</p>
    
HTML;

echo get_footer();
?>