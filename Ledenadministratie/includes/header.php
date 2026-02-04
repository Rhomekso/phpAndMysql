<?php
require_once __DIR__ . '/Auth.php';
Auth::init();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? e($page_title) : 'Ledenadministratie'; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1, h2 {
            color: #333;
            margin-bottom: 20px;
        }
        
        nav {
            background: #333;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 3px;
            transition: background 0.3s;
        }
        
        nav a:hover {
            background: #555;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #333;
            color: white;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .btn {
            display: inline-block;
            padding: 8px 15px;
            margin: 5px;
            text-decoration: none;
            border-radius: 3px;
            cursor: pointer;
            border: none;
            font-size: 14px;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background: #218838;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #333;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        
        input[type="text"],
        input[type="date"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 3px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .stats-grid, .quick-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .stat-card {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .stat-card h3 {
            margin: 0;
            color: #1976d2;
            font-size: 32px;
        }
        
        .stat-card p {
            margin: 10px 0 0 0;
            color: #666;
        }
        
        .contributie-card {
            background: #e0f7fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        
        .contributie-card h3 {
            margin: 0 0 10px 0;
            color: #00695c;
        }
        
        .contributie-card .bedrag {
            margin: 0;
            font-size: 24px;
            font-weight: bold;
            color: #004d40;
        }
        
        .link-card {
            background: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            display: block;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        .link-card:hover {
            background: #0056b3;
        }
        
        .link-card small {
            color: #cce5ff;
        }
        
        input[type="password"], input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }
        
        .auth-form {
            max-width: 400px;
            margin: 50px auto;
        }
        
        .info-box {
            margin-top: 20px;
            padding: 15px;
            background: #e3f2fd;
            border-radius: 5px;
        }
        
        .filter-bar {
            margin-bottom: 20px;
        }
        
        .filter-bar form {
            display: inline-block;
        }
        
        .empty-message {
            text-align: center;
        }
    </style>
</head>
<body>
    <nav>
        <ul>
            <li><a href="/phpAndMysql/Ledenadministratie/index.php">Home</a></li>
            <?php if (Auth::check()): ?>
            <li><a href="/phpAndMysql/Ledenadministratie/families/index.php">Families</a></li>
            <li><a href="/phpAndMysql/Ledenadministratie/familieleden/index.php">Familieleden</a></li>
            <li><a href="/phpAndMysql/Ledenadministratie/soort_lid/index.php">Soort Leden</a></li>
            <li><a href="/phpAndMysql/Ledenadministratie/contributie/index.php">Contributies</a></li>
            <li><a href="/phpAndMysql/Ledenadministratie/boekjaar/index.php">Boekjaren</a></li>
            <?php endif; ?>
        </ul>
        <div style="float: right; color: white;">
            <?php if (Auth::check()): ?>
                Welkom, <strong><?php echo e(Auth::user()['username']); ?></strong> 
                <?php if (Auth::isAdmin()): ?><span style="color: #ffd700;">(Admin)</span><?php endif; ?>
                | <a href="/phpAndMysql/Ledenadministratie/auth/logout.php" style="color: #ffcccc;">Uitloggen</a>
            <?php else: ?>
                <a href="/phpAndMysql/Ledenadministratie/auth/login.php" style="color: white;">Inloggen</a>
            <?php endif; ?>
        </div>
        <div style="clear: both;"></div>
    </nav>
    <div class="container">
