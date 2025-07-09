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

if (!empty($url)) {
    echo '<div class="vuln-section">';
    echo "<h3>Content from $url</h3>";
    
    $content = file_get_contents($url);
    
    if ($content !== false) {
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
    } else {
        echo "<p>ERROR: Could not fetch content</p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>