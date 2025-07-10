<?php
// patient_search.php - Enhanced XSS Vulnerability
require_once 'includes.php';

echo get_header("Patient Search", "Everybody lies. The question is, about what?");

// Simulated patient database
$patients = [
    ["id" => 1, "name" => "Rachel Dunne", "diagnosis" => "Sarcoidosis"],
    ["id" => 2, "name" => "Harvey Park", "diagnosis" => "Amyloidosis"],
    ["id" => 3, "name" => "Victoria Madsen", "diagnosis" => "Lupus"],
    ["id" => 4, "name" => "Ethan Hodges", "diagnosis" => "Vasculitis"]
];

$searchTerm = $_GET['search'] ?? '';


if(isset($_SESSION['user']) && $_SESSION['role'] === 'employee' && $_COOKIE['PHPSESSID'] === $specialEmployeeSession) {

    echo <<<HTML
        <div class="panel">
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
        echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
        
        $found = false;
        foreach ($patients as $patient) {
            // Case-insensitive search (vulnerable to XSS in diagnosis)
            if (stripos($patient['name'], $searchTerm) !== false) {
                // Intentionally vulnerable to XSS in diagnosis field
                echo "<div class='patient-card'>";
                echo "<h4>{$patient['name']}</h4>";
                echo "<p><strong>Diagnosis:</strong> {$patient['diagnosis']}</p>";
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

    echo <<<HTML
        <div class="vuln-section">
            <h3>Do not Disclose this DATA!</h3>
            <p>This data is HIPPA or something, NO SHARING: SHARING BAD</p>
        </div>
    HTML;
} else {
    echo <<<HTML
        <div class="access-denied">
            <h3>Access Denied</h3>
            <p>Patient search requires a specific employee session.</p>
            <p>Only employees with the special session ID can access this feature.</p>
        </div>
    HTML;
}


echo '</div>'; // Close panel
echo get_footer();
?>