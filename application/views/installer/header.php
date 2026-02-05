<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> - Installation Wizard</title>
    
    <!-- Bootstrap CSS -->
    <link href="<?php echo base_url('assets/template/css/bootstrap.min.css'); ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="<?php echo base_url('assets/template/font-awesome/css/font-awesome.css'); ?>" rel="stylesheet">
    
    <!-- jQuery (must be loaded before any inline scripts) -->
    <script src="<?php echo base_url('assets/template/js/jquery-3.1.1.min.js'); ?>"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .installer-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }
        
        .installer-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .installer-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .installer-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        
        .installer-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        
        .step-wizard {
            display: flex;
            justify-content: space-between;
            padding: 40px 50px 20px;
            position: relative;
        }
        
        .step-wizard::before {
            content: '';
            position: absolute;
            top: 60px;
            left: 50%;
            transform: translateX(-50%);
            width: 80%;
            height: 3px;
            background: #e0e0e0;
            z-index: 0;
        }
        
        .step-item {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        
        .step-number {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 10px;
            transition: all 0.3s;
        }
        
        .step-item.active .step-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .step-item.completed .step-number {
            background: #28a745;
            color: white;
        }
        
        .step-item.completed .step-number::before {
            content: '\f00c';
            font-family: FontAwesome;
        }
        
        .step-title {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }
        
        .step-item.active .step-title {
            color: #667eea;
            font-weight: 600;
        }
        
        .installer-body {
            padding: 40px 50px;
        }
        
        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }
        
        .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            padding: 12px 15px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 30px;
            font-weight: 600;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
        }
        
        .progress {
            height: 25px;
            border-radius: 8px;
        }
        
        .progress-bar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Custom Select Dropdown Styling */
        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23667eea' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            padding-right: 40px;
            cursor: pointer;
            height: auto;
            min-height: 48px;
            line-height: 1.5;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        select.form-control option {
            padding: 10px 15px;
            font-size: 14px;
        }
        
        select.form-control optgroup {
            font-weight: 600;
            color: #667eea;
            background: #f8f9fa;
        }
        
        select.form-control:focus {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23764ba2' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        }
        
        /* Timezone select specific styling */
        #default_timezone {
            font-size: 14px;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="installer-container">
        <div class="installer-card">
            <div class="installer-header">
                <h1><i class="fa fa-cogs"></i> Installation Wizard</h1>
                <p>Web-Based Application Installer with RBAC System</p>
            </div>
            
            <div class="step-wizard">
                <div class="step-item <?php echo ($step >= 1) ? 'active' : ''; ?> <?php echo ($step > 1) ? 'completed' : ''; ?>">
                    <div class="step-number"><?php echo ($step > 1) ? '' : '1'; ?></div>
                    <div class="step-title">Database</div>
                </div>
                <div class="step-item <?php echo ($step >= 2) ? 'active' : ''; ?> <?php echo ($step > 2) ? 'completed' : ''; ?>">
                    <div class="step-number"><?php echo ($step > 2) ? '' : '2'; ?></div>
                    <div class="step-title">Import SQL</div>
                </div>
                <div class="step-item <?php echo ($step >= 3) ? 'active' : ''; ?> <?php echo ($step > 3) ? 'completed' : ''; ?>">
                    <div class="step-number"><?php echo ($step > 3) ? '' : '3'; ?></div>
                    <div class="step-title">Setup</div>
                </div>
                <div class="step-item <?php echo ($step >= 4) ? 'active completed' : ''; ?>">
                    <div class="step-number"><?php echo ($step >= 4) ? '' : '4'; ?></div>
                    <div class="step-title">Complete</div>
                </div>
            </div>
            
            <div class="installer-body">
