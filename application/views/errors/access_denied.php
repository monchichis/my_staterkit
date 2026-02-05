<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Access Denied</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url('superadmin') ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>403 - Access Denied</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-content text-center" style="padding: 60px 20px;">
                    <div class="error-container">
                        <!-- Lock Icon -->
                        <div style="margin-bottom: 30px;">
                            <i class="fa fa-lock" style="font-size: 100px; color: #ed5565; opacity: 0.8;"></i>
                        </div>
                        
                        <!-- Error Code -->
                        <h1 style="font-size: 80px; font-weight: 700; color: #2f4050; margin: 0;">403</h1>
                        
                        <!-- Error Title -->
                        <h2 style="font-size: 28px; color: #676a6c; margin: 15px 0;">Access Denied</h2>
                        
                        <!-- Error Message -->
                        <p style="font-size: 16px; color: #888; max-width: 500px; margin: 0 auto 30px;">
                            <?php echo isset($message) ? $message : 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. Silakan hubungi administrator jika Anda yakin ini adalah kesalahan.'; ?>
                        </p>
                        
                        <!-- Permission Info (if available) -->
                        <?php if(isset($required_permission)): ?>
                        <div class="alert alert-warning" style="display: inline-block; margin-bottom: 30px;">
                            <i class="fa fa-info-circle"></i> 
                            Required permission: <code><?php echo $required_permission; ?></code>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div style="margin-top: 20px;">
                            <a href="javascript:history.back()" class="btn btn-white btn-lg" style="margin-right: 10px;">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <a href="<?php echo isset($dashboard_url) ? $dashboard_url : base_url(); ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-home"></i> Dashboard
                            </a>
                        </div>
                        
                        <!-- Decorative Elements -->
                        <div style="margin-top: 50px; padding-top: 30px; border-top: 1px solid #e7eaec;">
                            <p style="color: #aaaa; font-size: 14px;">
                                <i class="fa fa-shield"></i> 
                                Halaman ini dilindungi oleh sistem Role-Based Access Control (RBAC)
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-container {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-container .fa-lock {
    animation: shake 0.5s ease-in-out 0.5s;
}

@keyframes shake {
    0%, 100% { transform: rotate(0deg); }
    25% { transform: rotate(-10deg); }
    75% { transform: rotate(10deg); }
}
</style>
