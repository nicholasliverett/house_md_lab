<?php
require_once 'includes.php';

echo get_header("Diagnostic Tool", "Treating illness is why we became doctors. Treating patients is what makes most doctors miserable.");

$url = $_GET['image'] ?? '';

echo <<<HTML
    <h2>Medical Imaging Viewer</h2>
    <p>View diagnostic scans and test results. Enter the image URL to load:</p>
    
    <form method="GET">
        <div class="form-group">
            <input type="text" name="image" placeholder="Enter image URL" value="$url">
            <button type="submit">View Image</button>
        </div>
    </form>
HTML;

if (!empty($url)) {
    echo '<div class="vuln-section">';
    echo "<h3>Scan Results for: " . htmlspecialchars($url) . "</h3>";
    
    $content = @file_get_contents($url);
    
    if ($content !== false) {
        // Try to detect if it's an image
        $imageInfo = @getimagesizefromstring($content);
        
        if ($imageInfo !== false) {
            // It's an image - display it
            $mimeType = $imageInfo['mime'];
            $base64 = base64_encode($content);
            echo "<img src='data:$mimeType;base64,$base64' style='max-width:600px;'><br>";
            echo "<p>Image metadata:</p>";
            echo "<pre>Type: " . $imageInfo['mime'] . "\n";
            echo "Dimensions: " . $imageInfo[0] . "x" . $imageInfo[1] . " pixels</pre>";
        } else {
            // Not an image - show raw content
            echo "<p>Non-image content:</p>";
            echo "<pre>" . htmlspecialchars($content) . "</pre>";
        }
    } else {
        echo "<p>ERROR: Could not fetch content from URL</p>";
        echo "<p>Error: " . error_get_last()['message'] . "</p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>