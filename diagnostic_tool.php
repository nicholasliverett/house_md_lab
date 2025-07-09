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
    
    // SSRF vulnerability implementation
    try {
        // Create a context that follows redirects
        $context = stream_context_create([
            'http' => ['follow_location' => 1],
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
        ]);
        
        // Get the content
        $content = @file_get_contents($imageUrl, false, $context);
        
        if ($content !== false) {
            // Try to display as image
            if (@imagecreatefromstring($content)) {
                $base64 = base64_encode($content);
                $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($content);
                echo "<img src='data:$mime;base64,$base64' width='600'><br>";
                echo "<p>Image loaded from: $imageUrl</p>";
            } 
            // Display as text if not an image
            else {
                echo "<h4>Non-image content from $imageUrl:</h4>";
                echo "<pre>" . htmlspecialchars($content) . "</pre>";
            }
        } else {
            echo "<p>Error loading content from: $imageUrl</p>";
        }
    } catch (Exception $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>