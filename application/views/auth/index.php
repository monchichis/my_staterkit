<!DOCTYPE html>
<html>
<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Login Form</title>

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">


</head>
<!-- <style>
	.bg {
		background: url("<?php echo base_url('assets/dist/'); ?>img/payroll.png") no-repeat center center fixed;
		-webkit-background-size: cover;
		-moz-background-size: cover;
		-o-background-size: cover;
		background-size: cover;
	}
</style> -->


<body class="gray-bg">
	<!-- <audio hidden autoplay>
        <source src="<?= base_url('assets/'); ?>sound/login.mp3" type="audio/mpeg">
    </audio> -->

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
            <div>

                <h1 class="logo-name"><img src="<?php echo base_url('assets/dist/aplikasi/') . $identitas->logo; ?>" alt="AdoniaLogo" class="brand-image img-circle elevation-3" style="height:200px"></h1>

            </div>
            <div class="ibox-content">
            <h5>Welcome to</h5><br>
            <h3> <?= $identitas->nama_aplikasi ?></h3>
             <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
             <?php echo $this->session->flashdata('msg'); ?>
           <!--  <p>Login in. To see it in action.</p> -->
            <form class="m-t" role="form" action="<?php echo base_url('auth/index'); ?>" method="post">
                <div class="form-group">
                	<?php echo form_error('email', '<small class="text-danger pl-1">', '</small>'); ?>
                    <input type="email" class="form-control" name="email" placeholder="Alamat Email" required="">
                </div>
                <div class="form-group">
                	<?php echo form_error('password', '<small class="text-danger pl-1">', '</small>'); ?>
                    <input type="password" class="form-control" name="password" placeholder="Password" required="">
                </div>
                <button type="submit" class="btn btn-primary block full-width m-b"><i class="fa fa-sign-in"></i> Login</button>

                <a class="btn btn-sm btn-white btn-block" href="<?php echo base_url('Auth/reset')?>"><small><i class="fa fa-lock"></i> Forgot password?</small></a>
               <!--  <p class="text-muted text-center"><small>Do not have an account?</small></p>
                <a class="btn btn-sm btn-white btn-block" href="register.html">Create an account</a> -->
            </form>
            <!-- <p class="m-t"> <small>Inspinia we app framework base on Bootstrap 3 &copy; 2014</small> </p> -->
             </div>
        </div>
    </div>

    <!-- Mainly scripts -->
    <script src="<?php echo base_url('assets/'); ?>template/js/jquery-3.1.1.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/popper.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/bootstrap.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/plugins/sweetalert/sweetalert.min.js"></script>


<script>

    $(document).ready(function () {

        // $('#tombol-logout').click(function(){
        //     swal({
        //         title: "Welcome in Alerts",
        //         text: "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
        //     });
        // });

        // $('.demo2').click(function(){
        //     swal({
        //         title: "Good job!",
        //         text: "You clicked the button!",
        //         type: "success"
        //     });
        // });

        $('.logout').click(function () {
            swal({
                title: "Konfirmasi Logout",
                text: "Klik keluar untuk mengakhiri session!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Keluar",
                closeOnConfirm: false
            }, function () {
                document.location.href = '<?=base_url('auth/logout'); ?>';
            });
            
        });

        
        $('.backup').click(function () {
            swal({
                title: "Konfirmasi Backup",
                text: "Klik Backup Untuk Export Database!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Backup",
                closeOnConfirm: false
            }, function () {
                document.location.href = '<?= base_url('admin/backup_database'); ?>';
                swal("Success!", "Your Data has been Backup.", "success");
            });
            
        });

        const flashData = $('.flash-data').data('flashdata');
        if (flashData) {
            swal({
                title: flashData + ' Sukses',
                text: "",
                type: 'success'
            });
        }




 
 


    });

</script>

</body>

</html>



