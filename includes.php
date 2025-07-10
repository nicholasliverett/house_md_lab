<?php
session_start();

// Check if client is on LAN
function is_lan_client() {
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // LAN IP ranges
    $lan_ranges = [
        '10.0.0.0/8',        // Class A private network
        '172.16.0.0/12',     // Class B private networks
        '192.168.0.0/16',    // Class C private networks
        '127.0.0.0/8',       // Loopback addresses
        '::1'                // IPv6 loopback
    ];
    
    foreach ($lan_ranges as $range) {
        if (ip_in_range($ip, $range)) {
            return true;
        }
    }
    return false;
}

// Check if IP is in specified range
function ip_in_range($ip, $range) {
    if ($range === '::1') {
        return $ip === '::1';
    }
    
    list($subnet, $bits) = explode('/', $range);
    $ip = ip2long($ip);
    $subnet = ip2long($subnet);
    $mask = -1 << (32 - $bits);
    $subnet &= $mask;
    return ($ip & $mask) === $subnet;
}

function get_header($title, $quote) {
    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>$title - Princeton-Plainsboro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --accent: #e74c3c;
            --light: #ecf0f1;
            --dark: #2c3e50;
            --success: #2ecc71;
            --warning: #f39c12;
            --danger: #e74c3c;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #2c3e50, #4a6491);
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        header {
            background: linear-gradient(to right, var(--primary), #4a6491);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        nav {
            background: var(--secondary);
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin: 5px 15px;
            font-weight: bold;
            font-size: 18px;
            padding: 8px 16px;
            border-radius: 4px;
            transition: background 0.3s;
            display: flex;
            align-items: center;
        }
        
        nav a i {
            margin-right: 8px;
        }
        
        nav a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .house-quote {
            font-style: italic;
            color: var(--accent);
            text-align: center;
            margin: 20px 0;
            font-size: 1.3em;
            padding: 15px;
            border-left: 4px solid var(--accent);
            background: rgba(231, 76, 60, 0.1);
            border-radius: 0 8px 8px 0;
        }
        
        .panel {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin: 20px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }
        
        h1, h2, h3 {
            color: var(--primary);
            margin-bottom: 15px;
        }
        
        h1 {
            border-bottom: 3px solid var(--secondary);
            padding-bottom: 10px;
            font-size: 2.2rem;
        }
        
        h2 {
            border-bottom: 2px solid var(--secondary);
            padding-bottom: 8px;
            font-size: 1.8rem;
        }
        
        h3 {
            font-size: 1.4rem;
            color: var(--secondary);
        }
        
        .form-group {
            margin: 20px 0;
        }
        
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
        }
        
        button {
            background: var(--secondary);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: background 0.3s;
            margin-top: 10px;
            box-shadow: 0 3px 10px rgba(52, 152, 219, 0.3);
        }
        
        button:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
        
        .vuln-section {
            border: 2px dashed var(--accent);
            padding: 20px;
            margin: 25px 0;
            border-radius: 8px;
            background: rgba(231, 76, 60, 0.05);
        }
        
        .access-status {
            display: flex;
            align-items: center;
            margin: 15px 0;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .indicator {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        .granted {
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
        }
        
        .denied {
            background: var(--danger);
            box-shadow: 0 0 10px var(--danger);
        }
        
        .admin-content, .access-denied {
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 20px;
            border: 1px solid #e0e0e0;
        }
        
        .access-denied {
            text-align: center;
            background: #ffebee;
            border-color: #ffcdd2;
        }
        
        .warning {
            margin-top: 15px;
            padding: 10px;
            background: #fff8e1;
            border-radius: 8px;
            color: #ff8f00;
            font-weight: bold;
        }
        
        .results {
            margin-top: 25px;
            padding: 20px;
            background: #f0f8ff;
            border-radius: 8px;
            border: 1px dashed var(--secondary);
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
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .content-preview {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            max-height: 300px;
            overflow: auto;
            font-family: monospace;
            white-space: pre-wrap;
        }
        
        footer {
            text-align: center;
            margin-top: 30px;
            color: #ecf0f1;
            padding: 15px;
            font-size: 0.9rem;
        }
        
        .login-container {
            max-width: 500px;
            margin: 50px auto;
        }
        
        .instructions {
            background: rgba(236, 240, 241, 0.9);
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
        }
        
        .instructions h3 {
            color: var(--accent);
        }
        
        .instructions ul {
            padding-left: 20px;
            margin: 10px 0;
        }
        
        .instructions li {
            margin-bottom: 8px;
        }
        
        code {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: monospace;
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
            <a href="index.php"><i class="fas fa-home"></i> Home</a>
            <a href="patient_search.php"><i class="fas fa-search"></i> Patient Search</a>
            <a href="diagnostic_tool.php"><i class="fas fa-x-ray"></i> Diagnostic Tool</a>
            <a href="admin_panel.php"><i class="fas fa-lock"></i> Admin Panel</a>
        </nav>
HTML;
}

function get_footer() {
    return <<<HTML
        <footer>
            <p>For educational purposes only | House MD Theme | Penetration Testing Lab</p>
            <p>Remember: "It's never lupus... except when it is." - Dr. Gregory House</p>
        </footer>
    </div>
</body>
</html>
HTML;
}

// Simulated admin credentials
$ADMIN_USER = 'house';
$ADMIN_PASS = 'vicodin';
?>