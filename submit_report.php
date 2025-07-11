<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $patient = get_patient_by_id($patient_id);
    
    if ($patient) {
        $report = [
            'date' => date('Y-m-d H:i:s'),
            'doctor' => $_SESSION['user'] ?? 'Unknown',
            'diagnosis' => $_POST['diagnosis'],
            'image_url' => $_POST['image_url'] ?? '',
            'notes' => $_POST['notes']
        ];
        
        $patient['reports'][] = $report;
        
        // Update diagnosis if changed
        if ($_POST['update_diagnosis'] === 'yes') {
            $patient['diagnosis'] = $_POST['diagnosis'];
        }
        
        update_patient($patient);
        $_SESSION['success'] = 'Report submitted successfully';
    } else {
        $_SESSION['error'] = 'Patient not found';
    }
    
    header('Location: patients.php');
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
            <form action="submit_report.php" method="POST">
                <input type="hidden" name="patient_id" value="{$patient['id']}">
                
                <div class="form-group">
                    <label>Current Diagnosis: {$patient['diagnosis']}</label>
                </div>
                
                <div class="form-group">
                    <label for="update_diagnosis">Update Diagnosis?</label>
                    <select name="update_diagnosis" id="update_diagnosis" required>
                        <option value="no">Keep current diagnosis</option>
                        <option value="yes">Update to new diagnosis</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="diagnosis">New Diagnosis:</label>
                    <input type="text" name="diagnosis" id="diagnosis" required>
                </div>
                
                <div class="form-group">
                    <label for="image_url">Image URL:</label>
                    <input type="text" name="image_url" id="image_url">
                </div>
                
                <div class="form-group">
                    <label for="notes">Doctor's Notes:</label>
                    <textarea name="notes" id="notes" rows="4" required></textarea>
                </div>
                
                <button type="submit">Submit Report</button>
            </form>
        </div>
        HTML;
        
        echo get_footer();
        exit;
    }
}

header('Location: patients.php');
exit;
?>