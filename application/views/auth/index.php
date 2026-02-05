<!DOCTYPE html>
<html>
<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Login Form</title>

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">

    <?php 
        $favicon = isset($identitas->title_icon) && !empty($identitas->title_icon) ? base_url('assets/images/' . $identitas->title_icon) : base_url('assets/images/default-icon.png');
    ?>
    <link rel="shortcut icon" href="<?php echo $favicon; ?>">

    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">


</head>
<style>
    /* Responsive Login Layout */
    body.gray-bg {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 100vh;
        margin: 0;
        padding: 20px;
    }

    .middle-box {
        margin: 0 !important;
        padding: 0 !important;
        width: 100%;
        max-width: 400px;
        float: none !important; /* Override float if exists */
    }

    .ibox-content {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        background: #fff;
    }

    /* Logo scaling */
    .img-circle {
        width: 120px !important;
        height: 120px !important;
    }
    
    @media (max-width: 480px) {
        .logo-name {
            font-size: 60px;
        }
    }
</style>


<body class="gray-bg">
	<!-- <audio hidden autoplay>
        <source src="<?= base_url('assets/'); ?>sound/login.mp3" type="audio/mpeg">
    </audio> -->

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div class="text-center mb-4">
                <?php if (!empty($identitas->logo)): ?>
                    <img src="<?php echo base_url('assets/images/') . $identitas->logo; ?>" 
                         alt="<?php echo $identitas->nama_aplikasi; ?> Logo" 
                         class="img-circle elevation-3" 
                         style="height:150px; width:150px; object-fit:contain; background: white; padding: 10px; border: 3px solid #1ab394;">
                <?php else: ?>
                    <h1 class="logo-name" style="color:#1ab394;">IN+</h1>
                <?php endif; ?>
            </div>
            
            <div class="ibox-content" style="padding: 30px;">
                <h5>Welcome to</h5>
                <h3 style="color:#1ab394; margin-bottom:20px;"><?= $identitas->nama_aplikasi ?></h3>
                
                <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
                <?php echo $this->session->flashdata('msg'); ?>
                
                <?php echo form_open('auth/index', ['class' => 'm-t', 'role' => 'form']); ?>
                    <div class="form-group">
                        <?php echo form_error('email', '<small class="text-danger pl-1">', '</small>'); ?>
                        <input type="email" class="form-control" name="email" placeholder="Email Address" required="">
                    </div>
                    <div class="form-group">
                        <?php echo form_error('password', '<small class="text-danger pl-1">', '</small>'); ?>
                        <input type="password" class="form-control" name="password" placeholder="Password" required="">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b">
                        <i class="fa fa-sign-in"></i> Login
                    </button>

                    <a class="btn btn-sm btn-white btn-block" href="<?php echo base_url('Auth/reset')?>">
                        <small><i class="fa fa-lock"></i> Forgot password?</small>
                    </a>
                </form>
                
                <p class="text-muted text-center mt-3">
                    <small>Powered by RBAC System</small>
                </p>
            </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="<?php echo base_url('assets/'); ?>template/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/popper.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/bootstrap.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

    $(document).ready(function () {

        $('.logout').click(function () {
            Swal.fire({
                title: "Konfirmasi Logout",
                text: "Klik keluar untuk mengakhiri session!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Keluar",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = '<?=base_url('auth/logout'); ?>';
                }
            });
        });

        
        $('.backup').click(function () {
            Swal.fire({
                title: "Konfirmasi Backup",
                text: "Klik Backup Untuk Export Database!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Backup",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = '<?= base_url('admin/backup_database'); ?>';
                    Swal.fire("Success!", "Your Data has been Backup.", "success");
                }
            });
        });

        const flashData = $('.flash-data').data('flashdata');
        if (flashData) {
            Swal.fire({
                title: flashData + ' Sukses',
                text: "",
                icon: 'success'
            });
        }

    });

</script>

</body>

</html>



