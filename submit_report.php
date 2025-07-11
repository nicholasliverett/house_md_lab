<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reports = get_reports();
    $report = [
        'patient_name' => $_POST['patient_name'],
        'diagnosis' => $_POST['diagnosis'],
        'image_url' => $_POST['image_url'] ?? '',
        'notes' => $_POST['notes'],
        'date' => date('Y-m-d H:i:s'),
        'doctor' => $_SESSION['user'] ?? 'Unknown'
    ];
    
    $reports[] = $report;
    save_patient_reports($reports);
    
    $_SESSION['success'] = 'Patient report submitted successfully';
    header('Location: patient_search.php');
    exit;
}

// Handle GET request for specific patient
if (isset($_GET['patient_id'])) {
    $patient_id = (int)$_GET['patient_id'];
    $patients = [
        1 => "Rachel Dunne",
        2 => "Harvey Park",
        3 => "Victoria Madsen",
        4 => "Ethan Hodges"
    ];
    
    if (isset($patients[$patient_id])) {
        $patient_name = $patients[$patient_id];
        echo get_header("Submit Report for $patient_name");
        
        echo <<<HTML
        <div class="panel">
            <h2>Submit Report for $patient_name</h2>
            <form action="submit_report.php" method="POST">
                <input type="hidden" name="patient_name" value="$patient_name">
                <div class="form-group">
                    <label for="diagnosis">Diagnosis:</label>
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

header('Location: patient_search.php');
exit;
?>