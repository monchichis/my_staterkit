<!DOCTYPE html>
<html>
<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title><?php echo $identitas->nama_aplikasi; ?> | Reset Password</title>

    <?php 
        $favicon = isset($identitas->title_icon) && !empty($identitas->title_icon) ? base_url('assets/images/' . $identitas->title_icon) : base_url('assets/images/default-icon.png');
    ?>
    <link rel="shortcut icon" href="<?php echo $favicon; ?>">

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">

    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">



</head>
<style>
    /* Responsive Reset Layout */
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
        float: none !important;
    }

    .ibox-content {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: none;
        background: #fff;
        padding: 30px !important;
    }

    /* Logo scaling */
    .img-circle {
        width: 120px !important;
        height: 120px !important;
    }
</style>


<body class="gray-bg">
	<audio hidden autoplay>
        <source src="<?= base_url('assets/'); ?>sound/lupa.mp3" type="audio/mpeg">
    </audio>

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
            <div class="ibox-content">
                <h3>Reset Password</h3>
                 <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
                 <?php echo $this->session->flashdata('msg'); ?>
                 <?php echo form_open('auth/do_reset', ['class' => 'm-t', 'role' => 'form']); ?>
                    <div class="form-group">
                    	<?php echo form_error('email', '<small class="text-danger pl-1">', '</small>'); ?>
                        <input type="email" class="form-control" name="email" placeholder="Alamat Email" required="">
                    </div>
                    <div class="form-group">
                    	<?php echo form_error('nik', '<small class="text-danger pl-1">', '</small>'); ?>
            			<input type="number" class="form-control" name="nik" placeholder="Nomor Induk Kepegawaian">
                    </div>
                    <button type="submit" class="btn btn-primary block full-width m-b"><i class="fa fa-check"></i> Reset</button>
                    <!-- <button type="submit" class="btn btn-primary block full-width m-b">Login</button> -->

                    <a  class="btn btn-sm btn-white btn-block" href="<?php echo base_url('auth')?>"><small><i class="fa fa-arrow-left"></i>   Back</small></a>
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



