<!DOCTYPE html>
<html>
<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $title; ?></title>

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">


    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">

</head>

<body class="md-skin">

<div id="wrapper">

