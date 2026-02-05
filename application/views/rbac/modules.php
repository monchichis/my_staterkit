<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Modules Management</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('admin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Modules</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Application Modules</h5>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addModuleModal">
                            <i class="fa fa-plus"></i> Add Module
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <?php echo $this->session->flashdata('msg'); ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Icon</th>
                                    <th>Module Name</th>
                                    <th>Controller</th>
                                    <th>Parent Menu</th>
                                    <th>Description</th>
                                    <th>Sort Order</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($modules as $module): 
                                    // Find parent menu name
                                    $parent_menu_name = '-';
                                    foreach ($parent_menus as $pm) {
                                        if ($pm->id_parent_menu == $module->parent_menu_id) {
                                            $parent_menu_name = $pm->menu_name;
                                            break;
                                        }
                                    }
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><i class="<?php echo $module->icon; ?>"></i></td>
                                    <td><strong><?php echo $module->module_name; ?></strong></td>
                                    <td><code><?php echo $module->controller_name; ?></code></td>
                                    <td><?php echo $parent_menu_name; ?></td>
                                    <td><?php echo $module->module_description; ?></td>
                                    <td><?php echo $module->sort_order; ?></td>
                                    <td>
                                        <?php if ($module->is_active): ?>
                                            <span class="badge badge-primary">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-xs" onclick="editModule(<?php echo htmlspecialchars(json_encode($module)); ?>)">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-xs" 
                                           onclick="deleteModule(<?php echo $module->id_module; ?>, '<?php echo addslashes($module->module_name); ?>')">
                                            <i class="fa fa-trash"></i> Delete
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

<!-- Add Module Modal -->
<div class="modal fade" id="addModuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Module</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('rbac/add_module'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="module_name" required>
                    </div>
                    <div class="form-group">
                        <label>Controller Name <span class="text-danger">*</span></label>
                        <select class="form-control" name="controller_name" required>
                            <option value="">-- Select Generated Controller --</option>
                            <?php foreach ($generated_controllers as $gc): ?>
                                <option value="<?php echo $gc->controller_name; ?>"><?php echo $gc->controller_name; ?> (<?php echo $gc->table_name; ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Select from generated CRUD controllers</small>
                    </div>
                    <div class="form-group">
                        <label>Icon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="icon" value="fa fa-cube" required>
                        <small class="form-text text-muted">Font Awesome class, e.g., "fa fa-file"</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="module_description" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Parent Menu <span class="text-muted">(Optional)</span></label>
                        <select class="form-control" name="parent_menu_id">
                            <option value="">-- No Parent Menu (Standalone) --</option>
                            <?php foreach ($parent_menus as $pm): ?>
                            <option value="<?php echo $pm->id_parent_menu; ?>"><?php echo $pm->menu_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Module akan dikelompokkan dalam parent menu yang dipilih</small>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="10">
                    </div>
                    <div class="form-group">
                        <label>Parent Module</label>
                        <select class="form-control" name="parent_id">
                            <option value="">-- No Parent (Top Level) --</option>
                            <?php foreach ($modules as $m): ?>
                            <option value="<?php echo $m->id_module; ?>"><?php echo $m->module_name; ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-primary">Save Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Module Modal -->
<div class="modal fade" id="editModuleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Module</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editModuleForm" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Module Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="module_name" id="edit_module_name" required>
                    </div>
                    <div class="form-group">
                        <label>Controller Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="controller_name" id="edit_controller_name" required>
                        <small class="form-text text-muted">Lowercase, e.g., "reports"</small>
                    </div>
                    <div class="form-group">
                        <label>Icon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="icon" id="edit_icon" required>
                        <small class="form-text text-muted">Font Awesome class, e.g., "fa-file"</small>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="module_description" id="edit_module_description" rows="2"></textarea>
                    </div>
                    <div class="form-group">
                        <label>Parent Menu <span class="text-muted">(Optional)</span></label>
                        <select class="form-control" name="parent_menu_id" id="edit_parent_menu_id">
                            <option value="">-- No Parent Menu (Standalone) --</option>
                            <?php foreach ($parent_menus as $pm): ?>
                            <option value="<?php echo $pm->id_parent_menu; ?>"><?php echo $pm->menu_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="edit_sort_order">
                    </div>
                    <div class="form-group">
                        <label>Parent Module</label>
                        <select class="form-control" name="parent_id" id="edit_parent_id">
                            <option value="">-- No Parent (Top Level) --</option>
                            <?php foreach ($modules as $m): ?>
                            <option value="<?php echo $m->id_module; ?>"><?php echo $m->module_name; ?></option>
                            <?php endforeach; ?>
                        </select>
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
                    <button type="submit" class="btn btn-primary">Update Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editModule(module) {
    $('#edit_module_name').val(module.module_name);
    $('#edit_controller_name').val(module.controller_name);
    $('#edit_icon').val(module.icon);
    $('#edit_module_description').val(module.module_description);
    $('#edit_sort_order').val(module.sort_order);
    $('#edit_parent_id').val(module.parent_id);
    $('#edit_parent_menu_id').val(module.parent_menu_id);
    $('#edit_is_active').prop('checked', module.is_active == 1);
    
    $('#editModuleForm').attr('action', '<?php echo base_url('rbac/edit_module/'); ?>' + module.id_module);
    $('#editModuleModal').modal('show');
}

function deleteModule(id, moduleName) {
    Swal.fire({
        title: 'Hapus Module?',
        html: 'Apakah Anda yakin ingin menghapus <strong>"' + moduleName + '"</strong>?<br><small class="text-danger">Semua permissions terkait module ini juga akan dihapus!</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo base_url('rbac/delete_module/'); ?>' + id;
        }
    });
}
</script>

