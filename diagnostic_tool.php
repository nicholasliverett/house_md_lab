// ... [previous code]

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

// ... [footer]