<?php
require_once 'includes.php';

echo get_header("Diagnostic Medicine", "It's never lupus... except when it is.");

echo <<<HTML
    <h2>Welcome to the Diagnostic Department</h2>
    <p>Under the direction of Dr. Gregory House, we specialize in complex medical cases that defy conventional diagnosis.</p>
    
    <div class="vuln-section">
        <h3>Penetration Testing Laboratory</h3>
        <p>This educational platform contains intentional vulnerabilities for security training:</p>
        <ul>
            <li><strong>XSS Vulnerability</strong>: Patient search functionality</li>
            <li><strong>CSRF Vulnerability</strong>: Admin privilege modification</li>
            <li><strong>SSRF Vulnerability</strong>: Diagnostic image viewer</li>
        </ul>
        <p>Use responsibly in a controlled environment.</p>
    </div>
HTML;

echo get_footer();
?>