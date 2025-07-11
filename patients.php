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
                <div class='patient-card'>
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
                        
                        if (!preg_match('/^https?:\/\//i', $url)) {
                            echo '<div class="error">Only HTTP/HTTPS URLs are allowed</div>';
                        } else {
                            // Start SSRF demonstration
                            echo '<div class="ssrf-demo">';
                            echo '<h4>Server-Side Request Demonstration</h4>';
                            
                            try {
                                $context = stream_context_create([
                                    'http' => [
                                        'timeout' => 3,
                                        'ignore_errors' => true
                                    ]
                                ]);
                                
                                $content = @file_get_contents($url, false, $context);
                                $status = $http_response_header[0] ?? 'Unknown';
                                
                                echo '<div class="request-info">';
                                echo '<p><strong>URL Requested:</strong> <code>'.htmlspecialchars($url).'</code></p>';
                                echo '<p><strong>HTTP Status:</strong> <code>'.htmlspecialchars($status).'</code></p>';
                                
                                if ($content === false) {
                                    echo '<p class="error"><strong>Request Failed:</strong> Could not retrieve content</p>';
                                } else {
                                    echo '<p><strong>Content Length:</strong> '.strlen($content).' bytes</p>';
                                    
                                    // Show preview for text responses
                                    if (preg_match('/text|html|xml/i', $status)) {
                                        echo '<div class="content-preview">';
                                        echo '<h5>Content Preview:</h5>';
                                        echo '<pre>'.htmlspecialchars(substr($content, 0, 500)).'</pre>';
                                        echo '</div>';
                                    }
                                }
                                
                                echo '</div>';
                                
                            } catch (Exception $e) {
                                echo '<p class="error"><strong>Error:</strong> '.htmlspecialchars($e->getMessage()).'</p>';
                            }
                            
                            echo '</div>'; // Close ssrf-demo
                            
                            // Still attempt to show as image
                            echo '<img src="'.htmlspecialchars($url).'" style="max-width:200px;" onerror="this.style.display=\'none\'">';
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
            <div class='error-message'>
                <p>No records found for: <strong>$searchTerm</strong></p>
            </div>
HTML;
        }
        
        echo '</div>'; // Close results div
    }

    echo <<<HTML
        </div> <!-- Close search panel -->
        
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
        <div class="access-denied">
            <h3>Access Denied</h3>
            <p>Patient search is confidential see HIPAA.</p>
            <p>Only employees can access this feature.</p>
        </div>
HTML;
}

echo get_footer();
?>