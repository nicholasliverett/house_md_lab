<?php
require_once 'includes.php';

echo get_header("Diagnostic Medicine");

echo <<<HTML
    <div class="panel">
        <h2>Penetration Testing Laboratory</h2>
        <p>This educational platform contains intentional vulnerabilities for security training purposes.</p>
        
        <div class="session-info">
        </div>
        
        <div class="vuln-section">
            <h3>Lab Modules</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
                <div class="panel" style="background-color: #f0f8ff;">
                    <h3>XSS Vulnerability</h3>
                    <p>Patient search functionality</p>
                    <a href="patient_search.php" style="display: inline-block; background: #3498db; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Access Module</a>
                </div>
                
                <div class="panel" style="background-color: #fff8e1;">
                    <h3>SSRF Vulnerability</h3>
                    <p>Diagnostic image viewer</p>
                    <a href="diagnostic_tool.php" style="display: inline-block; background: #f39c12; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Access Module</a>
                </div>
                
                <div class="panel" style="background-color: #ffebee;">
                    <h3>CSRF Vulnerability</h3>
                    <p>Admin privilege modification</p>
                    <a href="admin_panel.php" style="display: inline-block; background: #e74c3c; color: white; padding: 10px 15px; border-radius: 4px; text-decoration: none; margin-top: 10px;">Access Module</a>
                </div>
            </div>
        </div>
        
        <h3>Learning Objectives</h3>
        <ul>
            <li>Identify and exploit common web vulnerabilities</li>
            <li>Understand real-world attack scenarios</li>
            <li>Learn secure coding practices</li>
            <li>Practice ethical hacking techniques</li>
        </ul>
    </div>
HTML;

echo get_footer();
?>