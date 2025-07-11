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

    if ($searchTerm) {
        echo '<div class="results">';
        echo "<h3>Search Results for: " . htmlspecialchars($searchTerm) . "</h3>";
        
        $patients = get_patients();
        $found = false;
        
        foreach ($patients as $patient) {
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
                    // Inside the patient report display loop where image_url is processed:
                    if (!empty($report['image_url'])) {
                        $url = $report['image_url'];
                        
                        // Block file:// protocol and other non-HTTP protocols
                        if (!preg_match('/^https?:\/\//i', $url)) {
                            echo '<div class="error">Only HTTP/HTTPS URLs are allowed</div>';
                        } else {
                            try {
                                // First make a server-side request
                                $content = @file_get_contents($url);
                                
                                // Then display information about the request
                                echo '<div class="ssrf-info">';
                                echo '<h4>Server-Side Request Information:</h4>';
                                echo '<p><strong>URL Requested:</strong> ' . htmlspecialchars($url) . '</p>';
                                
                                if ($content === false) {
                                    echo '<p><strong>Status:</strong> Request failed</p>';
                                } else {
                                    echo '<p><strong>Status:</strong> Request succeeded</p>';
                                    echo '<p><strong>Content Length:</strong> ' . strlen($content) . ' bytes</p>';
                                    
                                    // Show preview if content is text-based
                                    if (preg_match('/text|html|xml/i', $http_response_header[0] ?? '')) {
                                        echo '<div class="content-preview">';
                                        echo '<h5>Content Preview (first 200 chars):</h5>';
                                        echo '<pre>' . htmlspecialchars(substr($content, 0, 200)) . '</pre>';
                                        echo '</div>';
                                    }
                                }
                                
                                echo '</div>';
                                
                                // Then try to display as regular image (will fail for non-images)
                                echo '<img src="' . htmlspecialchars($url) . '" style="max-width: 200px;" onerror="this.style.display=\'none\'">';
                                
                            } catch (Exception $e) {
                                echo '<div class="error">Error making server request: ' . 
                                    htmlspecialchars($e->getMessage()) . '</div>';
                            }
                        }
                    }
                    echo '</div>'; // Close report div
                }
                
                echo <<<HTML
                    </div>
                </div>
HTML;
                $found = true;
            }
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
        
        echo '</div>'; // Close results div
    }

    echo <<<HTML
        </div> <!-- Close the search panel div -->
        
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

echo get_footer();
?>