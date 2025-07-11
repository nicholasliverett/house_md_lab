<?php
require_once 'includes.php';

echo get_header("Patient Reports", "Everybody lies. The question is, about what?");

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

    echo '<div class="results">';
    echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
    $patients = get_patients();
    $found = false;

    if ($searchTerm !== '') {  // Only filter if search term is not empty
        $filteredPatients = array_filter($patients, function($patient) use ($searchTerm) {
            return stripos($patient['name'], $searchTerm) !== false;
        });
    } else {
        $filteredPatients = $patients;  // Show all patients when search is empty
    }

    // Display results
    echo '<div class="results">';
    if ($searchTerm !== '') {
        echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
    }
        
    foreach ($filteredPatients as $patient) {
        if (empty($searchTerm) || stripos($patient['name'], $searchTerm) !== false) {
            echo <<<HTML
            <div class='patient-card' style="
                padding: 15px;
                margin-bottom: 15px;
                border-bottom: 1px solid #ddd;
            ">
                <h4>{$patient['name']}</h4>
                <p><strong>Diagnosis:</strong> {$patient['diagnosis']}</p>
                <a href="submit_report.php?patient_id={$patient['id']}" class="report-link">Submit Report</a>
                
                <div class="patient-reports">
                    <h5>Previous Reports:</h5>
HTML;
            
            foreach ($patient['reports'] as $report) {
                echo <<<HTML
                    <div class="report">
                        <p><strong>Date:</strong> {$report['date']}</p>
                        <p><strong>Doctor:</strong> {$report['doctor']}</p>
                        <p><strong>Notes:</strong> {$report['notes']}</p>
                        <p><strong>Diagnosis Update:</strong> {$report['diagnosis']}</p>
HTML;
                if (!empty($report['image_url'])) {
                    $url = $report['image_url'];
                    
                    // Block file:// protocol
                    if (strpos($url, 'file://') === 0) {
                        echo '<div class="error">Local file access blocked</div>';
                    } else {
                        try {
                            // Get the content
                            $content = file_get_contents($url);
                            
                            // Check if it's actually an image
                            $imageInfo = @getimagesizefromstring($content);
                            if ($imageInfo !== false) {
                                // It's a real image - display it
                                header("Content-Type: ".$imageInfo['mime']);
                                echo $content;
                                exit;
                            } else {
                                // Not an image - show raw content
                                header("Content-Type: text/plain");
                                echo "URL Content:\n\n";
                                echo htmlspecialchars($content);
                                exit;
                            }
                        } catch (Exception $e) {
                            echo '<div class="error">Error: '.htmlspecialchars($e->getMessage()).'</div>';
                        }
                    }
                }
                echo '</div>';
            }
            
            echo <<<HTML
                </div>
            </div>
HTML;
            $found = true;
        }
        
        if (!$found) {
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
        <div class="panel">
            <h3>Add New Patient</h3>
            <form action="add_patient.php" method="POST">
                <div class="form-group">
                    <label for="name">Patient Name:</label>
                    <input type="text" name="name" id="name" required>
                </div>
                <div class="form-group">
                    <label for="diagnosis">Initial Diagnosis:</label>
                    <input type="text" name="diagnosis" id="diagnosis" required>
                </div>
                <button type="submit">Add Patient</button>
            </form>
        </div>

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

echo '</div>';
echo get_footer();
?>