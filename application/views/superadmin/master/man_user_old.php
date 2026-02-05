<!-- Content Wrapper. Contains page content -->
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $title; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('superadmin'); ?>">Home</a></li>
            <li class="breadcrumb-item active"><strong><?php echo $title; ?></strong></li>
        </ol>
    </div>
</div>
<div class="wrapper wrapper-content animated fadeInRight">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox">
				<div class="ibox-title">
					<h5>Data User</h5>
					<div class="ibox-tools">
						<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-user">
							<i class="fa fa-plus"></i> Tambah User
						</button>
					</div>
				</div>
				
				<div class="ibox-content">
					<div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
					<?php if (validation_errors()) { ?>
						<div class="alert alert-danger">
							<a class="close" data-dismiss="alert">x</a>
							<strong><?php echo strip_tags(validation_errors()); ?></strong>
						</div>
					<?php } ?>

					<div class="table-responsive">
						<table class="table table-striped table-bordered table-hover dataTables-example">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama</th>
									<th>Email</th>
									<th>Level</th>
									<th>Status</th>
									<th>Register</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 1; ?>
								<?php foreach ($list_user as $lu) : ?>
									<tr>
										<td><?php echo $i++; ?></td>
										<td><?php echo $lu['nama']; ?></td>
										<td><?php echo $lu['email']; ?></td>
										<td><?php echo $lu['level'] ?></td>
										<td>
											<?php if ($lu['is_active'] == 1) : ?>
												<span class="badge badge-primary">Aktif</span>
											<?php else : ?>
												<span class="badge badge-danger">Tidak Aktif</span>
											<?php endif; ?>
										</td>
										<td><?php echo format_indo($lu['date_created']); ?></td>
										<td>
											<?php if ($lu['is_active'] == 1) : ?>
						<a href="<?php echo base_url('superadmin/toggle_user_status/' . $lu['id_user']); ?>" class="btn btn-success btn-xs mb-1 toggle-status" data-id="<?php echo $lu['id_user']; ?>" data-status="<?php echo $lu['is_active']; ?>" title="Status: Aktif">
							<i class="fa fa-power-off"></i>
						</a>
					<?php else : ?>
						<a href="<?php echo base_url('superadmin/toggle_user_status/' . $lu['id_user']); ?>" class="btn btn-danger btn-xs mb-1 toggle-status" data-id="<?php echo $lu['id_user']; ?>" data-status="<?php echo $lu['is_active']; ?>" title="Status: Tidak Aktif">
							<i class="fa fa-power-off"></i>
						</a>
					<?php endif; ?>
											<button type="button" class="tombol-edit btn btn-info btn-xs" data-id="<?php echo $lu['id_user']; ?>" data-toggle="modal" data-target="#edit-user">
												<i class="fa fa-edit"></i> Edit
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Tambah User -->
<div class="modal fade" id="add-user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah User</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('superadmin/man_user'); ?>" method="post" id="form_id">
						<div class="form-group">
							<label>Level</label>
							<select class="form-control form-control-sm" name="level">
								<option value="">- Pilih Level -</option>
								<?php foreach ($roles as $role) : ?>
									<option value="<?php echo $role->role_name; ?>"><?php echo $role->role_name; ?></option>
								<?php endforeach; ?>
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
			</div>
		</div>
	</div>
</div>

<!-- Modal Edit User -->
<div class="modal fade" id="edit-user">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Edit User</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('superadmin/edit_user'); ?>" method="post" id="form_id">
						<input type="hidden" name="id_user" id="id_user">
						
						<div class="form-group">
							<label>Level</label>
							<select class="form-control form-control-sm" name="level" id="edit_level" required>
								<option value="">- Pilih Level -</option>
								<?php foreach ($roles as $role) : ?>
									<option value="<?php echo $role->role_name; ?>"><?php echo $role->role_name; ?></option>
								<?php endforeach; ?>
							</select>
						</div>
						
						<div class="form-group">
							<label>Nama Lengkap</label>
							<input type="text" class="form-control form-control-sm" name="nama" id="edit_nama" required>
						</div>
						
						<div class="form-group">
							<label>NIK</label>
							<input type="text" class="form-control form-control-sm" name="nik" id="edit_nik" required>
						</div>
						
						<div class="form-group">
							<div class="form-check">
								<input class="form-check-input" type="radio" name="is_active" value="1" required checked>
								<label class="form-check-label">Aktif</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="is_active" value="0">
								<label class="form-check-label">Tidak Aktif</label>
							</div>
						</div>
						
						<button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Struktur -->
<div class="modal fade" id="struktur">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Tambah Struktur</h4>
			</div>
			<div class="modal-body">
				<div class="box-body">
					<form action="<?php echo base_url('superadmin/add_struktur'); ?>" method="post">
						<input type="hidden" name="user_id" id="id_user_struktur">
						
						<div class="form-group">
							<label>Kode Karyawan</label>
							<input type="text" class="form-control form-control-sm" name="kode_karyawan" value="<?php echo $kode; ?>" readonly>
						</div>
						
						<div class="form-group">
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
						
						<button type="submit" class="btn btn-primary mr-2">Simpan Perubahan</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
	$(document).ready(function() {
	// Edit user - populate form
	$('.tombol-edit').on('click', function() {
		const id_user = $(this).data('id');
		
		$.ajax({
			url: '<?php echo base_url("superadmin/get_user"); ?>',
			data: { id_user: id_user },
			method: 'post',
			dataType: 'json',
			success: function(data) {
				$('#edit_nik').val(data.nik);
				$('#edit_nama').val(data.nama);
				$('#edit_level').val(data.level);
				$('#id_user').val(data.id_user);
				
				// Set radio button for is_active
				if (data.is_active == 1) {
					$('input[name="is_active"][value="1"]').prop('checked', true);
				} else {
					$('input[name="is_active"][value="0"]').prop('checked', true);
				}
				
				// Populate role checkboxes when editing user
				if (data.roles && data.roles.length > 0) {
					$('.role-checkbox').prop('checked', false);
					data.roles.forEach(function(roleId) {
						$('.role-checkbox[value="' + roleId + '"]').prop('checked', true);
					});
				}
			}
		});
	});
});
</script>
