<?php
require_once 'includes.php';

// Simulated patient database
$patients = [
    ["id" => 1, "name" => "Rachel Dunne", "diagnosis" => "Sarcoidosis"],
    ["id" => 2, "name" => "Harvey Park", "diagnosis" => "Amyloidosis"],
    ["id" => 3, "name" => "Victoria Madsen", "diagnosis" => "Lupus"],
    ["id" => 4, "name" => "Ethan Hodges", "diagnosis" => "Vasculitis"]
];

echo get_header("Patient Search", "Everybody lies. The question is, about what?");

$searchTerm = $_GET['search'] ?? '';

echo <<<HTML
    <h2>Patient Records Search</h2>
    <form method="GET">
        <div class="form-group">
            <input type="text" name="search" placeholder="Enter patient name" value="$searchTerm">
            <button type="submit">Search</button>
        </div>
    </form>
HTML;

if ($searchTerm) {
    echo '<div class="results">';
    echo "<h3>Search Results for: $searchTerm</h3>";
    
    $found = false;
    foreach ($patients as $patient) {
        // Case-insensitive search (vulnerable to XSS)
        if (stripos($patient['name'], $searchTerm) !== false) {
            echo "<p><strong>{$patient['name']}</strong>: {$patient['diagnosis']}</p>";
            $found = true;
        }
    }
    
    if (!$found) {
        // Intentionally vulnerable to XSS (no output encoding)
        echo "<p>No records found for: <strong>$searchTerm</strong></p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>