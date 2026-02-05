<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Permissions Management</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('admin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Permissions</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Permissions List (Grouped by Module)</h5>
                    <?php if (can('rbac.permissions.manage')): ?>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addPermissionModal">
                            <i class="fa fa-plus"></i> Add Permission
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="ibox-content">
                    <?php echo $this->session->flashdata('msg'); ?>
                    
                    <?php foreach ($permissions as $module_name => $perms): ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fa fa-cube"></i> <?php echo $module_name; ?></h4>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Permission Name</th>
                                            <th>Permission Key</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <?php if (can('rbac.permissions.manage')): ?>
                                            <th>Actions</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($perms as $perm): ?>
                                        <tr>
                                            <td><strong><?php echo $perm->permission_name; ?></strong></td>
                                            <td><code><?php echo $perm->permission_key; ?></code></td>
                                            <td><?php echo $perm->permission_description; ?></td>
                                            <td>
                                                <?php if ($perm->is_active): ?>
                                                    <span class="badge badge-primary">Active</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <?php if (can('rbac.permissions.manage')): ?>
                                            <td>
                                                <button class="btn btn-info btn-xs" onclick="editPermission(<?php echo htmlspecialchars(json_encode($perm)); ?>)">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="btn btn-danger btn-xs" 
                                                   onclick="deletePermission(<?php echo $perm->id_permission; ?>, '<?php echo addslashes($perm->permission_name); ?>')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </button>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Permission Modal -->
<div class="modal fade" id="addPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Permission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('rbac/add_permission'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Module <span class="text-danger">*</span></label>
                        <select class="form-control" name="module_id" id="add_module_id" required>
                            <option value="">-- Select Module --</option>
                            <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module->id_module; ?>" data-controller="<?php echo strtolower($module->controller_name); ?>"><?php echo $module->module_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Action Type <span class="text-danger">*</span></label>
                        <select class="form-control" id="add_action_type" required>
                            <option value="">-- Select Action --</option>
                            <option value="view">View (Lihat data)</option>
                            <option value="create">Create (Tambah data)</option>
                            <option value="update">Update (Edit data)</option>
                            <option value="delete">Delete (Hapus data)</option>
                            <option value="manage">Manage (Full akses modul)</option>
                            <option value="custom">Custom (Tentukan sendiri)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="permission_name" id="add_permission_name" required>
                        <small class="form-text text-muted">e.g., "View Users", "Create Report"</small>
                    </div>
                    <div class="form-group">
                        <label>Permission Key <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control" name="permission_key" id="add_permission_key" required readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="toggleKeyEdit()">
                                    <i class="fa fa-edit"></i>
                                </button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Format: controller.action (e.g., "kategoriproduk.view")</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="permission_description" id="add_permission_description" rows="2"></textarea>
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
                    <button type="submit" class="btn btn-primary">Save Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Permission Modal -->
<div class="modal fade" id="editPermissionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Permission</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editPermissionForm" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Permission Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="permission_name" id="edit_permission_name" required>
                    </div>
                    <div class="form-group">
                        <label>Permission Key <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="permission_key" id="edit_permission_key" required>
                        <small class="form-text text-muted">e.g., "user.view"</small>
                    </div>
                    <div class="form-group">
                        <label>Module <span class="text-danger">*</span></label>
                        <select class="form-control" name="module_id" id="edit_module_id" required>
                            <option value="">-- Select Module --</option>
                            <?php foreach ($modules as $module): ?>
                            <option value="<?php echo $module->id_module; ?>"><?php echo $module->module_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="permission_description" id="edit_permission_description" rows="2"></textarea>
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
                    <button type="submit" class="btn btn-primary">Update Permission</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Action descriptions for auto-generation
var actionDescriptions = {
    'view': 'Akses melihat data',
    'create': 'Akses menambah data baru',
    'update': 'Akses mengubah/edit data',
    'delete': 'Akses menghapus data',
    'manage': 'Akses penuh (tambah, edit, hapus)'
};

// Generate permission key and name when module or action changes
function generatePermissionKey() {
    var moduleSelect = document.getElementById('add_module_id');
    var actionSelect = document.getElementById('add_action_type');
    var keyInput = document.getElementById('add_permission_key');
    var nameInput = document.getElementById('add_permission_name');
    var descInput = document.getElementById('add_permission_description');
    
    var selectedOption = moduleSelect.options[moduleSelect.selectedIndex];
    var controller = selectedOption.getAttribute('data-controller') || '';
    var moduleName = selectedOption.text || '';
    var action = actionSelect.value;
    
    if (controller && action && action !== 'custom') {
        // Generate permission key: controller.action
        keyInput.value = controller + '.' + action;
        
        // Generate permission name
        var actionNames = {
            'view': 'View',
            'create': 'Create',
            'update': 'Update',
            'delete': 'Delete',
            'manage': 'Manage'
        };
        nameInput.value = actionNames[action] + ' ' + moduleName;
        
        // Generate description
        descInput.value = actionDescriptions[action] + ' ' + moduleName;
    } else if (action === 'custom') {
        // Clear for custom input
        keyInput.value = controller ? controller + '.' : '';
        keyInput.removeAttribute('readonly');
        nameInput.value = '';
        descInput.value = '';
    }
}

// Toggle readonly on permission key
function toggleKeyEdit() {
    var keyInput = document.getElementById('add_permission_key');
    if (keyInput.hasAttribute('readonly')) {
        keyInput.removeAttribute('readonly');
        keyInput.focus();
    } else {
        keyInput.setAttribute('readonly', true);
    }
}

// Edit Permission function (using vanilla JS)
function editPermission(perm) {
    document.getElementById('edit_permission_name').value = perm.permission_name;
    document.getElementById('edit_permission_key').value = perm.permission_key;
    document.getElementById('edit_module_id').value = perm.module_id;
    document.getElementById('edit_permission_description').value = perm.permission_description;
    document.getElementById('edit_is_active').checked = perm.is_active == 1;
    
    document.getElementById('editPermissionForm').action = '<?php echo base_url('rbac/edit_permission/'); ?>' + perm.id_permission;
    
    // Show modal using Bootstrap's native method
    var editModal = new bootstrap.Modal(document.getElementById('editPermissionModal'));
    editModal.show();
}

// Event listeners using vanilla JS
document.addEventListener('DOMContentLoaded', function() {
    // Module and action change listeners
    var moduleSelect = document.getElementById('add_module_id');
    var actionSelect = document.getElementById('add_action_type');
    
    if (moduleSelect) {
        moduleSelect.addEventListener('change', generatePermissionKey);
    }
    if (actionSelect) {
        actionSelect.addEventListener('change', generatePermissionKey);
    }
    
    // Reset form when Add modal closes
    var addModal = document.getElementById('addPermissionModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function() {
            var form = this.querySelector('form');
            if (form) form.reset();
            var keyInput = document.getElementById('add_permission_key');
            if (keyInput) keyInput.setAttribute('readonly', true);
        });
    }
});

// Delete Permission with SweetAlert2 confirmation
function deletePermission(id, permissionName) {
    Swal.fire({
        title: 'Hapus Permission?',
        html: 'Apakah Anda yakin ingin menghapus <strong>"' + permissionName + '"</strong>?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo base_url('rbac/delete_permission/'); ?>' + id;
        }
    });
}
</script>

