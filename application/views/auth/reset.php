<!DOCTYPE html>
<html>
<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Payroll | Reset Password</title>

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">



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
	<audio hidden autoplay>
        <source src="<?= base_url('assets/'); ?>sound/lupa.mp3" type="audio/mpeg">
    </audio>

    <div class="middle-box text-center loginscreen animated fadeInDown">
        <div>
           <div>

                <h1 class="logo-name"><img src="<?php echo base_url('assets/dist/aplikasi/') . $identitas->logo; ?>" alt="AdoniaLogo" class="brand-image img-circle elevation-3" style="height:200px"></h1>

            </div>
            <div class="ibox-content">
                <h3>Reset Password</h3>
                 <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
                 <?php echo $this->session->flashdata('msg'); ?>
                <p>Login in. To see it in action.</p>
                <form class="m-t" role="form" action="<?php echo base_url('Auth/do_reset'); ?>" method="post">
                    <div class="form-group">
                    	<?php echo form_error('email', '<small class="text-danger pl-1">', '</small>'); ?>
                        <input type="email" class="form-control" name="email" placeholder="Alamat Email" required="">
                    </div>
                    <div class="form-group">
                    	<?php echo form_error('password', '<small class="text-danger pl-1">', '</small>'); ?>
            			<input type="number" class="form-control" name="nik" placeholder="Nomor Induk Kepegawaian">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b"><i class="fa fa-check"></i> Reset</button>
                    <!-- <button type="submit" class="btn btn-primary block full-width m-b">Login</button> -->

                    <a  class="btn btn-sm btn-white btn-block" href="<?php echo base_url('Auth')?>"><small><i class="fa fa-arrow-left"></i>   Back</small></a>
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

</body>

</html>



