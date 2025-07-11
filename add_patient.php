<?php
require_once 'includes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user']) && $_SESSION['role'] === 'employee') {
    $patients = get_patients();
    
    // Generate new ID
    $new_id = 1;
    if (!empty($patients)) {
        $ids = array_column($patients, 'id');
        $new_id = max($ids) + 1;
    }
    
    $new_patient = [
        'id' => $new_id,
        'name' => $_POST['name'],
        'diagnosis' => $_POST['diagnosis'],
        'reports' => []
    ];
    
    $patients[] = $new_patient;
    save_patients($patients);
    
    $_SESSION['success'] = 'Patient added successfully';
}

header('Location: patients.php');
exit;
?>