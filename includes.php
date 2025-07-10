<?php
// includes.php - Simplified Design
session_start();

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

function generate_session_id() {
    if (!isset($_SESSION['vuln_session'])) {
        $_SESSION['vuln_session'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['vuln_session'];
}

function get_header($title, $quote = '') {
    $session_id = generate_session_id();
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title - Princeton-Plainsboro</title>
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
            background: linear-gradient(to right, #2c3e50, #4a6491);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        nav {
            background: #3498db;
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
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Princeton-Plainsboro Teaching Hospital</h1>
            <p>Department of Diagnostic Medicine</p>
        </header>
        
        <div class="house-quote">
            "$quote"
        </div>
        
        <nav>
            <a href="index.php">Home</a>
            <a href="patient_search.php">Patient Search</a>
            <a href="diagnostic_tool.php">Diagnostic Tool</a>
            <a href="admin_panel.php">Admin Panel</a>
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
2. index.php (Home Page)
php
<?php
require_once 'includes.php';

echo get_header("Diagnostic Medicine", "It's never lupus... except when it is.");

echo <<<HTML
    <h2>Welcome to the Diagnostic Department</h2>
    <p>Under the direction of Dr. Gregory House, we specialize in complex medical cases that defy conventional diagnosis.</p>
    
    <div class="vuln-section">
        <h3>Penetration Testing Laboratory</h3>
        <p>This educational platform contains intentional vulnerabilities for security training:</p>
        <ul>
            <li><strong>XSS Vulnerability</strong>: Patient search functionality</li>
            <li><strong>CSRF Vulnerability</strong>: Admin privilege modification</li>
            <li><strong>SSRF Vulnerability</strong>: Diagnostic image viewer</li>
        </ul>
        <p>Use responsibly in a controlled environment.</p>
    </div>
HTML;

echo get_footer();
?>
3. patient_search.php (XSS Vulnerability)
php
<?php
require_once 'includes.php';

// Simulated patient database
$patients = [
    ["id" => 1, "name" => "Rachel Dunne", "diagnosis" => "Sarcoidosis"],
    ["id" => 2, "name" => "Harvey Park", "diagnosis" => "Amyloidosis"],
    ["id" => 3, "name" => "Victoria Madsen", "diagnosis" => "Lupus"],
    ["id" => 4, "name" => "Ethan Hodges", "diagnosis" => "Vasculitis"]
];

echo get_header("Patient Search", "Everybody lies. The question is, about what?");

$searchTerm = $_GET['search'] ?? '';

echo <<<HTML
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
    echo "<h3>Search Results for: $searchTerm</h3>";
    
    $found = false;
    foreach ($patients as $patient) {
        // Case-insensitive search (vulnerable to XSS)
        if (stripos($patient['name'], $searchTerm) !== false) {
            echo "<p><strong>{$patient['name']}</strong>: {$patient['diagnosis']}</p>";
            $found = true;
        }
    }
    
    if (!$found) {
        // Intentionally vulnerable to XSS (no output encoding)
        echo "<p>No records found for: <strong>$searchTerm</strong></p>";
    }
    
    echo '</div>';
}

echo get_footer();
?>