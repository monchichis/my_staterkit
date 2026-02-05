<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Roles Management</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('superadmin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Roles</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Roles List</h5>
                    <?php if (can('rbac.roles.manage')): ?>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addRoleModal">
                            <i class="fa fa-plus"></i> Add Role
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="ibox-content">
                    <?php echo $this->session->flashdata('msg'); ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Role Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <?php if (can('rbac.roles.manage')): ?>
                                    <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($roles as $role): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><strong><?php echo $role->role_name; ?></strong></td>
                                    <td><?php echo $role->role_description; ?></td>
                                    <td>
                                        <?php if ($role->is_active): ?>
                                            <span class="badge badge-primary">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($role->created_at)); ?></td>
                                    <?php if (can('rbac.roles.manage')): ?>
                                    <td>
                                        <?php if ($role->role_name == 'Super Admin'): ?>
                                            <button class="btn btn-secondary btn-xs" disabled>
                                                <i class="fa fa-ban"></i> No Action
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-info btn-xs" onclick="editRole(<?php echo htmlspecialchars(json_encode($role)); ?>)">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-danger btn-xs" onclick="deleteRole('<?php echo base_url('rbac/delete_role/'.$role->id_role); ?>')">
                                                <i class="fa fa-trash"></i> Delete
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
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

<!-- Add Role Modal -->
<div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Role</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('rbac/add_role'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="role_name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="role_description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" value="1" checked> Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Role Modal -->
<div class="modal fade" id="editRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Role</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editRoleForm" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="role_name" id="edit_role_name" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="role_description" id="edit_role_description" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" id="edit_is_active" value="1"> Active
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Role</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editRole(role) {
    $('#edit_role_name').val(role.role_name);
    $('#edit_role_description').val(role.role_description);
    $('#edit_is_active').prop('checked', role.is_active == 1);
    $('#editRoleForm').attr('action', '<?php echo base_url('rbac/edit_role/'); ?>' + role.id_role);
    $('#editRoleModal').modal('show');
}

function deleteRole(url) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = url;
        }
    })
}
</script>
