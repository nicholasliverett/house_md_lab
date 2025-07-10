<?php
// staff_dir.php - Enhanced XSS Vulnerability
require_once 'includes.php';

echo get_header("Staff Directory", "The eyes can mislead, a smile can lie, but shoes always tell the truth");

// Simulated staff database
$staffs = [
    ["id" => 1, "name" => "Dr. Lisa Cuddy", "role" => "Dean of Medicine and Hospital Administrator"],
    ["id" => 2, "name" => "Dr. Gregory House", "role" => "Diagnostician"],
    ["id" => 3, "name" => "Dr. Eric Foreman", "role" => "Neurologist"],
    ["id" => 4, "name" => "Dr. James Wilson", "role" => "Head of Oncology and House's Boyfriend"],
    ["id" => 5, "name" => "Dr. Allison Cameron", "role" => "Immunologist"],
    ["id" => 6, "name" => "Dr. Robert Chase", "role" => "Head of Diagnostic Medicine"],
    ["id" => 7, "name" => "Dr. Chris Taub", "role" => "Plastic Surgeon"],
];

$searchTerm = $_GET['search'] ?? '';

echo <<<HTML
    <div class="panel">
        <h2>Staff Search</h2>
        <form method="GET">
            <div class="form-group">
                <input type="text" name="search" placeholder="Enter employee name" value="$searchTerm">
                <button type="submit">Search</button>
            </div>
        </form>
HTML;

if ($searchTerm) {
    echo '<div class="results">';
    echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
    
    $found = false;
    foreach ($staffs as $staff) {
        // Case-insensitive search (vulnerable to XSS in role)
        if (stripos($staff['name'], $searchTerm) !== false) {
            // Intentionally vulnerable to XSS in role field
            echo "<div class='staff-card'>";
            echo "<h4>{$staff['name']}</h4>";
            echo "<p><strong>Role:</strong> {$staff['role']}</p>";
            echo "</div>";
            $found = true;
        }
    }
    
    if (!$found) {
        // Intentionally vulnerable to XSS in error message
        echo "<div class='error-message'>";
        echo "<p>No records found for: <strong>$searchTerm</strong></p>";
        echo "</div>";
    }
    
    echo '</div>';
}

echo '</div>'; // Close panel
echo get_footer();
?>