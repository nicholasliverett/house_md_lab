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
    
    // Whitelist for educational purposes
    $allowed_hosts = ['169.254.169.254', 'metadata.google.internal'];
    $parsed = parse_url($imageUrl);
    
    if (in_array($parsed['host'] ?? '', $allowed_hosts) {
        // Intentionally vulnerable for specific targets
        include($imageUrl);
    } else {
        // Use safe method for other URLs
        $content = @file_get_contents($imageUrl);
        if ($content !== false) {
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        } else {
            echo "<p>Error loading content</p>";
        }
    }
    
    echo '</div>';
}

echo get_footer();
?>