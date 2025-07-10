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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            border-bottom: 4px solid #3498db;
        }
        
        nav {
            background-color: #34495e;
            padding: 15px 0;
            margin: 20px 0;
        }
        
        .nav-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        nav a:hover {
            background-color: #3498db;
        }
        
        .house-quote {
            font-style: italic;
            color: #e74c3c;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            border-left: 3px solid #e74c3c;
            background-color: #fdecea;
        }
        
        .panel {
            background-color: white;
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            border: 1px solid #e0e0e0;
        }
        
        h1, h2, h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        h1 {
            font-size: 2.2rem;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        
        h2 {
            font-size: 1.8rem;
            padding-bottom: 8px;
            border-bottom: 1px solid #3498db;
        }
        
        h3 {
            font-size: 1.4rem;
            color: #2980b9;
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        input:focus {
            border-color: #3498db;
            outline: none;
        }
        
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
        }
        
        button:hover {
            background-color: #2980b9;
        }
        
        .vuln-section {
            border: 1px solid #e74c3c;
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            background-color: #fdecea;
        }
        
        .access-status {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 8px;
        }
        
        .indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .granted {
            background-color: #2ecc71;
        }
        
        .denied {
            background-color: #e74c3c;
        }
        
        .admin-content, .access-denied {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .access-denied {
            text-align: center;
            background-color: #ffebee;
            border-color: #ffcdd2;
        }
        
        .results {
            margin-top: 25px;
            padding: 20px;
            background-color: #f0f8ff;
            border-radius: 8px;
            border: 1px solid #3498db;
        }
        
        .image-container {
            text-align: center;
            margin: 20px 0;
        }
        
        .image-container img {
            max-width: 100%;
            max-height: 500px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            color: #7f8c8d;
            padding: 15px;
            font-size: 0.9rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .patient-card {
            border: 1px solid #3498db;
            border-radius: 8px;
            padding: 15px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        
        .error-message {
            background-color: #ffebee;
            border: 1px solid #e74c3c;
            border-radius: 8px;
            padding: 15px;
            margin: 15px 0;
        }
        
        .vulnerable-output {
            display: inline-block;
            padding: 5px;
            margin-top: 5px;
        }
        
        .xss-payloads {
            margin: 20px 0;
        }
        
        .payload {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            border-radius: 8px;
            margin: 15px 0;
        }
        
        .payload code {
            background-color: rgba(255,255,255,0.1);
            display: block;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            overflow-x: auto;
            font-size: 14px;
        }
        
        .xss-tips {
            background-color: rgba(52, 152, 219, 0.1);
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        .session-info {
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Princeton-Plainsboro Teaching Hospital</h1>
            <p>Penetration Testing Laboratory</p>
        </header>
        
        <div class="house-quote">
            "It's never lupus... except when it is."
        </div>
        
        <nav>
            <div class="nav-container">
                <a href="index.php">Home</a>
                <a href="patient_search.php">Patient Search</a>
                <a href="diagnostic_tool.php">Diagnostic Tool</a>
                <a href="admin_panel.php">Admin Panel</a>
            </div>
        </nav>
HTML;
}

function get_footer() {
    return <<<HTML
        <footer>
            <p>For educational purposes only | House MD Theme | Penetration Testing Lab</p>
            <p>Remember: "Everybody lies." - Dr. Gregory House</p>
        </footer>
    </div>
</body>
</html>
HTML;
}
?>