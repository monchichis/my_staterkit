<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Mode</title>
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
        .maintenance-container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 90%;
        }
        .maintenance-icon {
            font-size: 80px;
            color: #f8ac59;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="maintenance-container animated fadeInDown">
        <i class="fa fa-cogs maintenance-icon"></i>
        <h2 class="font-bold">SYSTEM UNDER MAINTENANCE</h2>
        <div class="alert alert-warning mt-4">
            <h4><i class="fa fa-info-circle"></i> Mohon Maaf,</h4>
            <p>
                Sistem sedang dalam proses pemeliharaan rutin. Fitur aplikasi dinonaktifkan sementara waktu untuk peningkatan performa dan keamanan.
            </p>
        </div>
        <p class="text-muted">
            Silahkan hubungi Administrator jika anda membutuhkan akses mendesak atau cobalah beberapa saat lagi.
        </p>
        <div class="mt-4">
            <a href="<?php echo base_url('auth/logout'); ?>" class="btn btn-danger btn-outline">
                <i class="fa fa-sign-out"></i> Logout
            </a>
        </div>
    </div>
</body>
</html>
