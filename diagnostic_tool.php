<?php
require_once 'includes.php';

echo get_header("Diagnostic Tool", "Treating illness is why we became doctors. Treating patients is what makes most doctors miserable.");

$imageUrl = $_GET['image'] ?? '';

echo <<<HTML
    <h2>Medical Imaging Viewer</h2>
    <p>View diagnostic scans and test results. Enter the image URL to load:</p>
    
    <form method="GET">
        <div class="form-group">
            <input type="text" name="image" placeholder="Enter image URL" value="$imageUrl">
            <button type="submit">View Image</button>
        </div>
    </form>
HTML;

if ($imageUrl) {
    echo '<div class="vuln-section">';
    echo "<h3>Scan Results</h3>";
    
    // Intentionally vulnerable to SSRF
    if (@getimagesize($imageUrl)) {
        echo "<img src='$imageUrl' width='600' alt='Medical scan'><br>";
        echo "<p>Loaded from: $imageUrl</p>";
    } else {
        // Simulated internal system access
        if (strpos($imageUrl, 'internal') !== false) {
            echo "<p>Internal system accessed successfully!</p>";
            echo "<p>System status: <strong>Operational</strong></p>";
        } else {
            echo "<p>Error loading image from: $imageUrl</p>";
        }
    }
    
    echo '</div>';
}

echo get_footer();
?>