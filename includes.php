<?php

session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'secure' => false,     // Should be true in production
    'httponly' => false,   // Allows JavaScript access
    'samesite' => 'None'   // Allows cross-site usage
]);

// includes.php - Simplified Design
session_start();

// File-based database for simplicity
define('USERS_DB', 'users.json');
define('REVIEWS_DB', 'reviews.json');
define('PATIENTS_DB', 'patients.json');

// Initialize databases if they don't exist
if (!file_exists(USERS_DB)) {
    file_put_contents(USERS_DB, json_encode([
        'admins' => ['house'],
        'employees' => [
            'house' => password_hash('vicodin', PASSWORD_DEFAULT),
            'wilson' => password_hash('oncology', PASSWORD_DEFAULT)
        ],
        'patients' => []
    ]));
}

if (!file_exists(PATIENTS_DB)) {
    file_put_contents(PATIENTS_DB, json_encode([
        [
            "id" => 1,
            "name" => "Rachel Dunne",
            "diagnosis" => "Sarcoidosis",
            "reports" => []
        ],
        [
            "id" => 2,
            "name" => "Harvey Park",
            "diagnosis" => "Amyloidosis",
            "reports" => []
        ],
        [
            "id" => 3,
            "name" => "Victoria Madsen",
            "diagnosis" => "Lupus",
            "reports" => []
        ],
        [
            "id" => 4,
            "name" => "Ethan Hodges",
            "diagnosis" => "Vasculitis",
            "reports" => []
        ]
    ]));
}


// Load databases
function get_users() {
    return json_decode(file_get_contents(USERS_DB), true);
}

function get_reviews() {
    return json_decode(file_get_contents(REVIEWS_DB), true);
}

function get_patients() {
    return json_decode(file_get_contents(PATIENTS_DB), true);
}

function save_users($users) {
    file_put_contents(USERS_DB, json_encode($users));
}

function save_reviews($reviews) {
    file_put_contents(REVIEWS_DB, json_encode($reviews));
}

function save_patients($reports) {
    file_put_contents(PATIENTS_DB, json_encode($reports));
}

function get_patient_by_id($id) {
    $patients = get_patients();
    foreach ($patients as $patient) {
        if ($patient['id'] == $id) {
            return $patient;
        }
    }
    return null;
}

function update_patient($updatedPatient) {
    $patients = get_patients();
    foreach ($patients as &$patient) {
        if ($patient['id'] == $updatedPatient['id']) {
            $patient = $updatedPatient;
            break;
        }
    }
    save_patients($patients);
}

// Special employee session for patient search
$specialEmployeeSession = 'employee_special_session_12345';

// Check if user is admin
function is_admin() {
    if (!isset($_SESSION['user'])) return false;
    $users = get_users();
    return in_array($_SESSION['user'], $users['admins']);
}

// Check if client is on LAN
function is_lan_client() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $lan_ranges = [
        '10.0.0.0/8', '172.16.0.0/12', '192.168.0.0/16', '127.0.0.0/8', '::1'
    ];
    
    foreach ($lan_ranges as $range) {
        if (ip_in_range($ip, $range)) return true;
    }
    return false;
}

function ip_in_range($ip, $range) {
    if ($range === '::1') return $ip === '::1';
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    return ($ip & $mask) === $subnet;
}

function get_header($title, $quote = '') {
    $user_status = '';
    if(isset($_SESSION['user'])) {
        $username = htmlspecialchars($_SESSION['user']);
        
        // Determine role with admin priority
        $role = ucfirst($_SESSION['role'] ?? 'unknown');
        if (is_admin()) {
            $role = 'Admin';
            $icon = 'fa-user-shield';
            $color = 'rgba(46, 204, 113, 0.9)'; // Green for admin
        } else {
            $icon = ($_SESSION['role'] === 'employee') ? 'fa-user-md' : 'fa-user';
            $color = 'rgba(52, 152, 219, 0.9)'; // Blue for others
        }
        
        $user_status = <<<HTML
            <a href="logout.php" class="user-status-badge" title="Logout" style="background: {$color}">
                <i class="fas {$icon}"></i> {$role}: {$username}
            </a>
        HTML;
    } else {
        $user_status = <<<HTML
            <div class="user-status-badge">
                <i class="fas fa-user-times"></i> Not logged in
            </div>
        HTML;
    } if(isset($_SESSION['user']) && ($_SESSION['role'] === 'employee' || is_admin())) {
        $employee_nav = <<<HTML
            <a href="patients.php">Patient Reports</a>
            <a href="admin_panel.php">Admin Panel</a>
            <a href="terminal.php">Terminal</a>
        HTML;
    } else {
        $employee_nav = "";
    }
    
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title - Princeton-Plainsboro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        header {
            position: relative;
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px; 
            min-height: 120px;
        }
        nav {
            background:linear-gradient(to right, #2c3e50, #4a6491);
            padding: 12px;
            border-radius: 6px;
            margin: 20px 0;
            display: flex;
            justify-content: center;
        }
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 20px;
            font-weight: bold;
            font-size: 18px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        .house-quote {
            font-style: italic;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
            font-size: 1.3em;
            padding: 10px;
            border-left: 4px solid #e74c3c;
            background: #fdecea;
        }
        .vuln-section {
            border: 2px dashed #e74c3c;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            background: #fffaf0;
        }
        footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            padding: 15px;
            border-top: 1px solid #eee;
        }
        .form-group {
            margin: 15px 0;
        }
        input[type="text"], input[type="password"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        button {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #c0392b;
        }
        .results {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 6px;
            margin-top: 20px;
        }
        .admin-panel {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
        }
        .user-status strong { 
            color: var(--primary); 
        }
        .user-status-badge {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background 0.3s;
        }
        .user-status-badge:hover {
            text-decoration: none;
            background: rgba(169, 169, 169, 0.2);
        }
        .report {
            margin: 20px 0;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f9f9f9;
        }
        .report img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
            border-radius: 4px;
        }
        input[type="checkbox"] {
            width: auto;
            height: auto;
            margin-right: 8px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 15px 0;
        }
        .checkbox-group label {
            margin: 0;
            font-weight: normal;
        }
        #diagnosisField {
            transition: all 0.8s ease;
        }
        .report-link {
            display: inline-block;
            background: #e74c3c;
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 14px;
            transition: background 0.3s;
            margin: 10px 0;
            border: none;
            cursor: pointer;
        }
        .report-link:hover {
            background: #c0392b;
            text-decoration: none;
            color: white;
        }
        .patient-card {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        .patient-reports {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .review-card {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius 8px;
            border: 1px solid #ddd;
        }
        .staff-card {
            background: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-radius 8px;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Princeton-Plainsboro Teaching Hospital</h1>
            <p>Department of Diagnostic Medicine</p>
            {$user_status}
        </header>
        
        <div class="house-quote">
            "$quote"
        </div>
        
        <nav>
            <a href="index.php">Home</a>
            <a href="staff_dir.php">Staff</a>
            <a href="reviews.php">Reviews</a>
            {$employee_nav}
        </nav>
HTML;
}

function get_footer() {
    return <<<HTML
        <footer>
            <p>For educational purposes only | House MD Theme | Penetration Testing Lab</p>
            <p>Remember: "It's never lupus... except when it is."</p>
        </footer>
    </div>
</body>
</html>
HTML;
}
?>