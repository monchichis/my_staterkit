<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied</title>
    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f3f4;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .error-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .error-icon {
            font-size: 80px;
            color: #ed5565;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="error-container animated fadeInDown">
        <i class="fa fa-lock error-icon"></i>
        <h2 class="font-bold">ACCESS DENIED</h2>
        <div class="alert alert-danger mt-4">
            <h4><i class="fa fa-shield"></i> Security Alert</h4>
            <p>
                This feature (CRUD Generator) is <strong>disabled</strong> in Production environment for security reasons.
            </p>
        </div>
        <p class="text-muted">
            Code generation tools should only be accessed in Development environment to prevent accidental system modifications.
        </p>
        <div class="mt-4">
            <a href="<?php echo base_url('superadmin'); ?>" class="btn btn-primary btn-outline">
                <i class="fa fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>
