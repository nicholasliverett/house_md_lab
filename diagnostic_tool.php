<?php
// diagnostic_tool.php - URL-only SSRF Vulnerability
require_once 'includes.php';

echo get_header("Diagnostic Tool", "Medical imaging requires network access...");

$url = $_GET['image'] ?? '';

echo <<<HTML
    <div class="panel">
        <h2>Medical Imaging Viewer</h2>
        <p>View diagnostic scans from networked PACS systems (HTTP/HTTPS only)</p>
        
        <form method="GET">
            <div class="form-group">
                <input type="text" name="image" placeholder="Enter image URL (http:// or https://)" value="$url">
                <button type="submit">Retrieve Image</button>
            </div>
        </form>
HTML;

if (!empty($url)) {
    echo '<div class="vuln-section">';
    echo "<h3>Scan Results</h3>";
    
    // Validate URL format
    if (!preg_match('/^https?:\/\//i', $url)) {
        echo "<div class='error-message'>";
        echo "<h4>Invalid URL Format</h4>";
        echo "<p>Only HTTP/HTTPS URLs are accepted by this system.</p>";
        echo "<p>Example: <code>https://avatars.githubusercontent.com/u/74279150?v=4</code></p>";
        echo "</div>";
    } else {

        // Intentionally vulnerable SSRF point (but only for URLs)
        $context = stream_context_create([
            'http' => [
                'ignore_errors' => true,
                'follow_location' => 0, // Prevent redirect attacks
                'timeout' => 5 // Short timeout
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);
        
        $content = @file_get_contents($url, false, $context);
        
        if ($content !== false) {
            $imageInfo = @getimagesizefromstring($content);
            
            if ($imageInfo !== false) {
                // Display actual images
                $mimeType = $imageInfo['mime'];
                $base64 = base64_encode($content);
                echo "<div class='image-container'>";
                echo "<img src='data:$mimeType;base64,$base64' style='max-width:600px;'>";
                echo "<div class='image-meta'>";
                echo "Source: " . htmlspecialchars($url);
                echo "</div></div>";
            } else {
                // Display non-image responses
                echo "<div class='content-preview'>";
                echo "<p><code>" . htmlspecialchars($url) . "</code></p>";
                echo "<pre>" . htmlspecialchars($content) . "</pre>";
                echo "</div>";
            }
        } else {
            echo "<div class='error-message'>";
            echo "<h4>Error Retrieving URL</h4>";
            echo "<p>Could not fetch content from: " . htmlspecialchars($url) . "</p>";
            echo "<p>Error: " . (error_get_last()['message'] ?? 'Unknown error') . "</p>";
            echo "</div>";
        }
    }
    
    echo '</div>';
}

echo get_footer();
?>