<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ContractLabour - Environment Verification</title>
    <style>
        :root {
            --bg-color: #0b0f19;
            --card-bg: #151c2c;
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
            --accent-blue: #3b82f6;
            --accent-green: #10b981;
            --accent-red: #ef4444;
            --border-color: #1f2937;
        }
        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            font-family: 'Outfit', 'Inter', -apple-system, sans-serif;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            box-sizing: border-box;
        }
        .container {
            max-width: 800px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 40px;
        }
        .header h1 {
            font-size: 2.5rem;
            margin: 0;
            background: linear-gradient(135deg, #60a5fa, #3b82f6, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            letter-spacing: -0.05em;
        }
        .header p {
            color: var(--text-secondary);
            font-size: 1.1rem;
            margin-top: 10px;
        }
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 30px -10px rgba(0, 0, 0, 0.4);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-top: 0;
            margin-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
        }
        .metric {
            background-color: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px 20px;
        }
        .metric-label {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }
        .metric-value {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
        }
        .metric-value.success {
            color: var(--accent-green);
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }
        .badge.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--accent-green);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }
        .badge.error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--accent-red);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            font-size: 0.85rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        tr:hover td {
            background-color: rgba(255, 255, 255, 0.01);
        }
        .footer {
            text-align: center;
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-top: 40px;
        }
        .footer a {
            color: var(--accent-blue);
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ContractLabour Docker Environment</h1>
            <p>High-Performance PHP 8.2.31 & MySQL 5.7.44-48 (Percona) Stack</p>
        </div>

        <!-- System Information -->
        <div class="card">
            <div class="card-title">
                <span>System Specifications</span>
                <span class="badge success">Active</span>
            </div>
            <div class="grid">
                <div class="metric">
                    <div class="metric-label">PHP Version</div>
                    <div class="metric-value"><?php echo phpversion(); ?></div>
                </div>
                <div class="metric">
                    <div class="metric-label">Web Server</div>
                    <div class="metric-value">Apache/2.4 (Debian)</div>
                </div>
                <div class="metric">
                    <div class="metric-label">Server Port</div>
                    <div class="metric-value"><?php echo $_SERVER['SERVER_PORT']; ?> (<?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'HTTPS' : 'HTTP'; ?>)</div>
                </div>
            </div>
        </div>

        <!-- Database Verification -->
        <div class="card">
            <div class="card-title">
                <span>Database Connectivity</span>
                <?php
                $db_host = 'mysql';
                $db_user = 'anahaw';
                $db_pass = 'anahaw';
                $db_name = 'anahaw';
                
                try {
                    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_TIMEOUT => 5
                    ]);
                    $mysql_version = $pdo->query('select version()')->fetchColumn();
                    echo '<span class="badge success">Connected</span>';
                    $db_connected = true;
                } catch (PDOException $e) {
                    echo '<span class="badge error">Disconnected</span>';
                    $db_connected = false;
                    $db_error = $e->getMessage();
                }
                ?>
            </div>
            
            <?php if ($db_connected): ?>
                <div class="grid" style="margin-bottom: 25px;">
                    <div class="metric">
                        <div class="metric-label">MySQL Version (Percona)</div>
                        <div class="metric-value success"><?php echo htmlspecialchars($mysql_version); ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Database Host</div>
                        <div class="metric-value"><?php echo $db_host; ?></div>
                    </div>
                    <div class="metric">
                        <div class="metric-label">Active Schema</div>
                        <div class="metric-value"><?php echo $db_name; ?></div>
                    </div>
                </div>

                <!-- Sample Chart Data Table -->
                <div style="font-weight: 700; margin-bottom: 10px;">Verification Data (`my_chart_data`):</div>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Category Date</th>
                                <th>Value 1</th>
                                <th>Value 2</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM my_chart_data ORDER BY category LIMIT 5");
                            while ($row = $stmt->fetch()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($row['category']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['value1']) . "</td>";
                                echo "<td>" . htmlspecialchars($row['value2']) . "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="color: var(--accent-red); background-color: rgba(239, 68, 68, 0.05); padding: 15px; border-radius: 8px; border: 1px solid rgba(239, 68, 68, 0.1); font-family: monospace;">
                    <strong>Connection Failed:</strong> <?php echo htmlspecialchars($db_error); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <p>Antigravity Stack Engine | <a href="https://localhost:5002" target="_blank">SSL Secure URL</a></p>
        </div>
    </div>
</body>
</html>
