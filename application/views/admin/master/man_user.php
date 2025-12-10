<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

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
			<!-- Default box -->
			<div class="row">
				<!-- left column -->
				<div class="col-md-12">
					<div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
					<?php if (validation_errors()) { ?>
						<div class="alert alert-danger">
							<a class="close" data-dismiss="alert">x</a>
							<strong><?php echo strip_tags(validation_errors()); ?></strong>
						</div>
					<?php } ?>
					<!-- general form elements -->
					<div class="card card-primary card-outline">
						<div class="card-header">
							<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#add-user">
								Tambah User
							</button>
						</div> <!-- /.card-body -->
						<div class="card-body">
							<div class="table-responsive">
								<table class="table table-striped table-bordered table-hover dataTables-example" id="">
									<thead>
										<th>No</th>
										<th>Nama</th>
										
										<th>Email</th>
										<th>Level</th>
										<th>Status</th>
										<th>Register</th>
										<th>Action</th>
										
									</thead>
									<tbody>
										<?php $i = 1; ?>
										<?php foreach ($list_user as $lu) : ?>
											<tr>
												<td><?php echo $i++; ?></td>
												<td><?php echo $lu['nama']; ?></td>
												
												<td><?php echo $lu['email']; ?></td>
												<td><?php echo $lu['level'] ?></td>
												<?php if ($lu['is_active'] == 1) : ?>
													<td>Aktif</td>
												<?php else : ?>
													<td><button class="badge badge-danger" style="font-size:14px;">Tidak Aktif</button></td>
												<?php endif; ?>
												<td><?php echo format_indo($lu['date_created']); ?></td>
												<td><button type="button" class="tombol-edit btn btn-info btn-block btn-sm" data-id="<?php echo $lu['id_user']; ?>" data-toggle="modal" data-target="#edit-user"><i class="fa fa-edit"></i> Edit</button></td>
												
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div><!-- /.container-fluid -->
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<!-- Modal -->
<div class="modal fade" id="add-user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah User</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('admin/man_user'); ?>" method="post" id="form_id">
						<div class="form-group">
							<label>Level</label>
							<select class="form-control form-control-sm" name="level">
								<option value="">- Pilih Level -</option>
								<option value="Admin">ADMINISTRATOR</option>
								<option value="User">Warga</option>
								<option value="Kadis">Kepala Dinas</option>
								
							</select>
						</div>
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control form-control-sm" name="nama" required>
						</div>
						<div class="form-group">
							<label>NIK</label>
							<input type="text" class="form-control form-control-sm" name="nik" required>
						</div>
					
						<div class="form-group">
							<label>Alamat Email</label>
							<input type="email" class="form-control form-control-sm" name="email" required>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label>Password</label>
									<input type="password" class="form-control form-control-sm" name="password1" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label>Repeat Password</label>
									<input type="password" class="form-control form-control-sm" name="password2" placeholder="Ketik ulang password" required>
								</div>
							</div>
						</div>
						<button type="submit" class="btn btn-primary mr-2">Simpan Data</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
				<!-- /.modal-content -->
			</div>
			<!-- /.modal-dialog -->
		</div>
	</div>
</div>

<div class="modal fade" id="edit-user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit User</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('admin/edit_user'); ?>" method="post" id="form_id">
						<input type="hidden" name="id_user" id="id_user">
						<div class="form-group">
							<label>Level</label>
							<select class="form-control form-control-sm" name="level" id="level" required>.
								<option value="">- Pilih Level -</option>
								<option value="Admin">ADMINISTRATOR</option>
								<option value="User">USER</option>
								<option value="Kepsek">Pimpinan</option>
							</select>
						</div>
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control form-control-sm" name="nama" id="nama" value="<?php set_value('nama'); ?>" required>
						</div>
						<div class="form-group">
							<label>NIK</label>
							<input type="text" class="form-control form-control-sm" name="nik" id="nik" required>
						</div>
						
						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="radio" name="is_active" value="1" required checked>
								<label class="form-check-label">
									Aktif
								</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="is_active" value="0">
								<label class="form-check-label">
									Tidak Aktif
								</label>
							</div>
						</div>
						<!-- /.box-body -->
						<button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="struktur">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah Struktur</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('admin/add_struktur'); ?>" method="post">
						<input type="hidden" name="user_id" id="id_user_struktur">
						<div class="form-group">
							<label>Kode Karyawan</label>
							<input type="text" class="form-control form-control-sm" name="kode_karyawan" value="<?php echo $kode; ?>" readonly>
						</div>
						<div class=" form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control form-control-sm" id="nama_struktur" readonly>
						</div>
						<div class="form-group">
							<label>NIK</label>
							<input type="text" class="form-control form-control-sm" id="nik_struktur" readonly>
						</div>
						<div class="form-group">
							<label>Bagian / Divisi</label>
							<input type="hidden" name="bagian_id_struktur" id="bagian_id_hidden">
							<select class="form-control form-control-sm" name="bagian_id_struktur" id="bagian_id_struktur" disabled>
								<option value="">- Pilih Bagian -</option>
								<?php foreach ($bagian as $b) : ?>
									<option value="<?php echo $b['id_bagian']; ?>"><?php echo $b['nama_bagian']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<div class="form-group">
							<label>Jabatan</label>
							<select class="form-control form-control-sm" name="jabatan_id" required>
								<option value="">- Pilih Jabatan -</option>
								<?php foreach ($jabatan as $b) : ?>
									<option value="<?php echo $b['id_jabatan']; ?>"><?php echo $b['nama_jabatan']; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						<!-- /.box-body -->
						<button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
<script>

	$('.tombol-edit').on('click', function() {
		const id_user = $(this).data('id');
		$.ajax({
			url: '<?php echo base_url('admin/get_user'); ?>',
			data: {
				id_user: id_user
			},
			method: 'post',
			dataType: 'json',
			success: function(data) {
				$('#nik').val(data.nik);
				$('#bagian_id').val(data.bagian_id);
				$('#nama').val(data.nama);
				$('#level').val(data.level);
				$('#id_user').val(data.id_user);
				$('#id_user_struktur').val(data.id_user);
				$('#bagian_id_hidden').val(data.bagian_id);
				$('#bagian_id_struktur').val(data.bagian_id);
				$('#nama_struktur').val(data.nama);
				$('#nik_struktur').val(data.nik);
			}
		});
	});
</script>
