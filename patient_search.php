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

if(isset($_SESSION['user']) && $_SESSION['role'] === 'employee') {
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
                echo <<<HTML
                <div class='patient-card' style="
                    padding: 15px;
                    margin-bottom: 15px;
                    border-bottom: 1px solid #ddd;
                ">
                    <h4>{$patient['name']}</h4>
                    <p><strong>Diagnosis:</strong> {$patient['diagnosis']}</p>
                </div>
                HTML;
                $found = true;
            }
        }
        
        if (!$found) {
            // Intentionally vulnerable to XSS in error message
            echo <<<HTML
            <div class='error-message' style="
                padding: 15px;
                background-color: #fdecea;
                border-left: 4px solid #e74c3c;
                margin: 20px 0;
            ">
                <p>No records found for: <strong>$searchTerm</strong></p>
            </div>
            HTML;
        }
        
        echo '</div>';
    }

    echo <<<HTML
        <div class="vuln-section">
            <h3>Do not Disclose this DATA!</h3>
            <p>This data is HIPAA or something, NO SHARING: SHARING BAD</p>
            <p>We have gone through great lengths to secure this part of the website, only staff have access to this page, and if you aren't staff you aren't reading this</p>
        </div>
    HTML;
} else {
    echo <<<HTML
        <div class="access-denied" style="
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin: 20px 0;
        ">
            <h3>Access Denied</h3>
            <p>Patient search is confidential see HIPAA.</p>
            <p>Only employees can access this feature.</p>
        </div>
    HTML;
}

echo '</div>'; // Close panel
echo get_footer();
?>