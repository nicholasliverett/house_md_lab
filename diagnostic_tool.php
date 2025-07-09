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
    
    // Direct SSRF vulnerability using include
    try {
        // Intentionally vulnerable to SSRF/LFI/RFI
        include($imageUrl);
    } catch (Exception $e) {
        echo "<p>Error loading content: " . $e->getMessage() . "</p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>