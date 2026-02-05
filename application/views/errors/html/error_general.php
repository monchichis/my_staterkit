<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$is_csrf_error = (strpos($message, 'The action you have requested is not allowed') !== false);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error Encountered</title>
    
    <!-- Bootstrap 3 CDN -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <!-- Animate CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    
    <style>
        body {
            background-color: #f3f3f4;
            font-family: "open sans", "Helvetica Neue", Helvetica, Arial, sans-serif;
            color: #676a6c;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }
        
        .error-container {
            max-width: 600px;
            width: 90%;
            text-align: center;
        }
        
        .error-card {
            background: #fff;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-top: 4px solid #e7eaec;
        }
        
        .error-card.security-error {
            border-top: 4px solid #ed5565;
        }
        
        .error-icon {
            font-size: 84px;
            margin-bottom: 20px;
            color: #d1d1d1;
        }
        
        .security-error .error-icon {
            color: #ed5565;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 300;
            margin-top: 0;
            margin-bottom: 10px;
        }
        
        p {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .btn {
            border-radius: 3px;
        }
        
        .btn-primary {
            background-color: #1ab394;
            border-color: #1ab394;
        }
        
        .btn-primary:hover {
            background-color: #18a689;
            border-color: #18a689;
        }
        
        .btn-danger {
            background-color: #ed5565;
            border-color: #ed5565;
        }
        
        .btn-danger:hover {
            background-color: #d43f3a;
            border-color: #d43f3a;
        }
        
        .technical-details {
            margin-top: 20px;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #eee;
            border-radius: 4px;
            text-align: left;
            font-family: monospace;
            font-size: 12px;
            color: #333;
            display: none;
        }
        
        .toggle-details {
            color: #999;
            font-size: 12px;
            text-decoration: underline;
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="error-container animated fadeInDown">
        <?php if ($is_csrf_error): ?>
            <!-- CSRF / Security Error Design -->
            <div class="error-card security-error">
                <i class="fa fa-shield error-icon animated pulse infinite"></i>
                <h1>Security Token Expired</h1>
                <p>
                    Maaf, aksi anda tidak dapat diproses karena token keamanan (CSRF) telah kadaluarsa atau tidak valid. 
                    Hal ini biasanya terjadi jika halaman didiamkan terlalu lama.
                </p>
                <p>Silakan muat ulang halaman dan coba lagi.</p>
                
                <div style="margin-top: 30px;">
                    <button onclick="window.location.reload();" class="btn btn-danger m-r-sm">
                        <i class="fa fa-refresh"></i> Reload Halaman
                    </button>
                    <button onclick="history.back();" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </button>
                </div>
            </div>
        <?php else: ?>
            <!-- General Error Design -->
            <div class="error-card">
                <i class="fa fa-exclamation-triangle error-icon"></i>
                <h1><?php echo $heading; ?></h1>
                
                <div class="alert alert-warning" style="text-align: left; border-left: 4px solid #f8ac59;">
                    <?php echo $message; ?>
                </div>
                
                <p>Silakan kembali atau hubungi administrator jika masalah berlanjut.</p>
                
                <div style="margin-top: 30px;">
                    <a href="javascript:history.back()" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <a href="/" class="btn btn-default">
                        <i class="fa fa-home"></i> Beranda
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="text-center" style="margin-top: 20px; color: #999; font-size: 11px;">
            &copy; <?php echo date('Y'); ?> Application Error Handler
        </div>
    </div>

</body>
</html>