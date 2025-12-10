<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2><?php echo $title; ?></h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a><?php echo $title; ?></a>
                </li>
                
            </ol>
        </div>
    </div>
    <br/>
	<!-- /.content-header -->

	<!-- Main content -->
	<section class="content">
		<div class="container-fluid">
			<div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
			<?php echo $this->session->flashdata('msg'); ?>
			<?php if (validation_errors()) { ?>
				<div class="alert alert-danger">
					<a class="close" data-dismiss="alert">x</a>
					<strong><?php echo strip_tags(validation_errors()); ?></strong>
				</div>
			<?php } ?>
			<div class="row">
				<div class="col-lg-12">
					  <div class="widget-head-color-box navy-bg p-lg text-center">
                            <div class="m-b-md">
                            <h2 class="font-bold no-margins">
                                Selamat Datang Di <?= $identitas->nama_aplikasi?>
                            </h2>
                                <strong><?php echo $user['nama']; ?></strong>
                            </div>
                            <img src="<?php echo base_url('assets/dist/aplikasi/') . $identitas->logo; ?>" alt="profile" class="brand-image img-circle elevation-3" style="height:150px">
                            <div>
                                <div class="float-right">
                                     <button type="button" class="btn btn-info btn-sm float-right ml-1" data-toggle="modal" data-target="#ubah-pass">Ubah Password</button> 
                                    <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#profile">Ubah Profil</button> 
                                </div>
                               
                            </div>
                        </div>
                        
				</div>
			</div>
			
			


		</div>
	</section>
</div>

<div class="modal fade" id="profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLabel">Ubah Profil</h5>
			</div>
			<div class="modal-body">
				<?php echo form_open_multipart('user/edit_profile'); ?>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Email</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" value="<?php echo $user['email']; ?>" readonly>
					</div>
				</div>
				<div class="form-group row">
					<label class="col-sm-2 col-form-label">Nama</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>">
					</div>
				</div>
				<div class="form-group row">
					<div class="col-sm-2">Photo</div>
					<div class="col-sm-10">
						<div class="row">
							<div class="col-sm-3">
								<img src="<?php echo base_url('assets/dist/img/profile/') . $user['image']; ?>" class="img-thumbnail">
							</div>
							<div class="col-sm-9">
								<div class="custom-file">
									<input type="file" class="custom-file-input" name="image">
									<label class="custom-file-label" for="image">Pilih File</label>
								</div>
							</div>
						</div>
					</div>
				</div>
				<button type="submit" class="btn btn-primary">Simpan Perubahan </button>
				<button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="ubah-pass">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ubah Password</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('user/ubah_password'); ?>" method="post">
						<div class="form-group">
							<label for="current_password">Password Lama</label>
							<input type="password" class="form-control" id="current_password" name="current_password" required>
						</div>
						<div class="form-group">
							<label for="new_password1">Password Baru</label>
							<input type="password" class="form-control" id="new_password1" name="new_password1" required>
						</div>
						<div class="form-group">
							<label for="new_password2">Ulang Password Baru</label>
							<input type="password" class="form-control" id="new_password2" name="new_password2" placeholder="Ketik ulang password baru" required>
						</div>
						<button type="submit" class="btn btn-primary mr-2">Simpan Perubahan </button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
