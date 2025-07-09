<?php
// diagnostic_tool.php - Simplest Possible SSRF
require_once 'includes.php';

$url = $_GET['url'] ?? '';

echo get_header("Diagnostic Tool", "Simple SSRF Demo");

echo <<<HTML
    <form method="GET">
        URL: <input type="text" name="url" value="$url" size="50">
        <input type="submit" value="Fetch">
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