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
    
    // Enhanced SSRF vulnerability with multiple methods
    $content = fetch_url_content($imageUrl);
    
    if ($content !== false) {
        // Try to display as image
        $imageInfo = @getimagesizefromstring($content);
        if ($imageInfo !== false) {
            $mime = $imageInfo['mime'];
            $base64 = base64_encode($content);
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
        echo "<p>Error details: " . error_get_last()['message'] . "</p>";
    }
    
    echo '</div>';
}

/**
 * Enhanced URL fetcher with multiple fallback methods
 */
function fetch_url_content($url) {
    // Method 1: curl (most reliable)
    if (function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    
    // Method 2: file_get_contents with stream context
    $context = stream_context_create([
        'http' => [
            'follow_location' => 1,
            'ignore_errors' => true,
            'timeout' => 5
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    if ($content !== false) return $content;
    
    // Method 3: sockets as last resort
    $parsed = parse_url($url);
    $host = $parsed['host'] ?? 'localhost';
    $port = $parsed['port'] ?? ($parsed['scheme'] === 'https' ? 443 : 80);
    $path = $parsed['path'] ?? '/';
    $query = $parsed['query'] ?? '';
    if ($query) $path .= "?$query";
    
    $fp = fsockopen($host, $port, $errno, $errstr, 5);
    if (!$fp) return false;
    
    $out = "GET $path HTTP/1.1\r\n";
    $out .= "Host: $host\r\n";
    $out .= "Connection: Close\r\n\r\n";
    fwrite($fp, $out);
    
    $content = '';
    while (!feof($fp)) {
        $content .= fgets($fp, 128);
    }
    fclose($fp);
    
    // Strip HTTP headers
    if (($pos = strpos($content, "\r\n\r\n")) {
        $content = substr($content, $pos + 4);
    }
    
    return $content;
}

echo get_footer();
?>