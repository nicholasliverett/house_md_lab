<?php
// diagnostic_tool.php - Super Simple SSRF Demo
require_once 'includes.php';

echo get_header("Diagnostic Tool", "Treating illness is why we became doctors. Treating patients is what makes most doctors miserable.");

$url = $_GET['url'] ?? '';

echo <<<HTML
    <h2>Medical Imaging Viewer</h2>
    <p>View diagnostic scans and test results. Enter the image URL to load:</p>
    
    <form method="GET">
        <input type="text" name="url" placeholder="Enter image URL" value="$url" size="50">
        <input type="submit" value="View Image">
    </form>
HTML;

if (!empty($url)) {
    echo '<div class="vuln-section">';
    echo "<h3>Scan Results</h3>";
    
    // Simplest possible SSRF vulnerability
    $content = file_get_contents($url);
    
    if ($content !== false) {
        // Try to display as image
        if (@imagecreatefromstring($content)) {
            $base64 = base64_encode($content);
            echo "<img src='data:image/png;base64,$base64' width='600'><br>";
            echo "<p>Image loaded from: " . htmlspecialchars($url) . "</p>";
        } 
        // Display as text if not an image
        else {
            echo "<h4>Non-image content from " . htmlspecialchars($url) . ":</h4>";
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } else {
        echo "<p>Error loading content from: " . htmlspecialchars($url) . "</p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>