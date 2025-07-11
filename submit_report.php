<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $patient = get_patient_by_id($patient_id);
    
    if ($patient) {
        $report = [
            'date' => date('Y-m-d H:i:s'),
            'doctor' => $_SESSION['user'] ?? 'Unknown',
            'diagnosis' => $_POST['update_diagnosis'] ? ($_POST['diagnosis'] ?? '') : '',
            'image_url' => $_POST['image_url'] ?? '',
            'notes' => $_POST['notes']
        ];
        
        $patient['reports'][] = $report;
        
        // Update diagnosis if checkbox was checked
        if (!empty($_POST['update_diagnosis']) && !empty($_POST['diagnosis'])) {
            $patient['diagnosis'] = $_POST['diagnosis'];
        }
        
        update_patient($patient);
        $_SESSION['success'] = 'Report submitted successfully';
    } else {
        $_SESSION['error'] = 'Patient not found';
    }
    
    header('Location: patient_search.php');
    exit;
}

// Handle GET request for specific patient
if (isset($_GET['patient_id'])) {
    $patient_id = (int)$_GET['patient_id'];
    $patient = get_patient_by_id($patient_id);
    
    if ($patient) {
        echo get_header("Submit Report for {$patient['name']}");
        
        echo <<<HTML
        <div class="panel">
            <h2>Submit Report for {$patient['name']}</h2>
            <form action="submit_report.php" method="POST" id="reportForm">
                <input type="hidden" name="patient_id" value="{$patient['id']}">
                
                <div class="form-group">
                    <label>Current Diagnosis: {$patient['diagnosis']}</label>
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; gap: 10px;">
                    <input type="checkbox" name="update_diagnosis" id="update_diagnosis" 
                        style="width: auto; margin: 0;">
                    <label for="update_diagnosis" style="margin: 0;">Update Diagnosis?</label>
                </div>
                
                <div class="form-group" id="diagnosisField">
                    <label for="diagnosis">New Diagnosis:</label>
                    <input type="text" name="diagnosis" id="diagnosis">
                </div>
                
                <div class="form-group">
                    <label for="image_url">Image URL:</label>
                    <input type="text" name="image_url" id="image_url" placeholder="Try http://localhost/admin_panel.php">
                    <small class="text-muted">For SSRF demo, try internal URLs like http://localhost/admin_panel.php</small>
                </div>
                
                <div class="form-group">
                    <label for="notes">Doctor's Notes:</label>
                    <textarea name="notes" id="notes" rows="4" required></textarea>
                </div>
                
                <button type="submit">Submit Report</button>
            </form>
        </div>

        <script>
        document.getElementById('update_diagnosis').addEventListener('change', function() {
            const diagnosisField = document.getElementById('diagnosis');
            if (this.checked) {
                diagnosisField.required = true;
                document.getElementById('diagnosisField').style.display = 'block';
            } else {
                diagnosisField.required = false;
                document.getElementById('diagnosisField').style.display = 'none';
            }
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('update_diagnosis').dispatchEvent(new Event('change'));
        });
        </script>
        HTML;
        
        echo get_footer();
        exit;
    }
}

header('Location: patients.php');
exit;
?>