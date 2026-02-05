<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Parent Menu Management</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('superadmin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Parent Menus</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Parent Menus</h5>
                    <div class="ibox-tools">
                        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addParentMenuModal">
                            <i class="fa fa-plus"></i> Add Parent Menu
                        </button>
                    </div>
                </div>
                <div class="ibox-content">
                    <?php echo $this->session->flashdata('msg'); ?>
                    
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Parent menus digunakan untuk mengelompokkan modules di sidebar. 
                        Module yang tidak memiliki parent menu akan ditampilkan sebagai menu standalone.
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th width="60">Icon</th>
                                    <th>Menu Name</th>
                                    <th width="100">Sort Order</th>
                                    <th width="100">Status</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1; foreach ($parent_menus as $menu): ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>
                                    <td><i class="<?php echo $menu->icon; ?>"></i></td>
                                    <td><strong><?php echo $menu->menu_name; ?></strong></td>
                                    <td><?php echo $menu->sort_order; ?></td>
                                    <td>
                                        <?php if ($menu->is_active): ?>
                                            <span class="badge badge-primary">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-info btn-xs" onclick="editParentMenu(<?php echo htmlspecialchars(json_encode($menu)); ?>)">
                                            <i class="fa fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-xs" onclick="deleteParentMenu(<?php echo $menu->id_parent_menu; ?>, '<?php echo addslashes($menu->menu_name); ?>')">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (empty($parent_menus)): ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No parent menus found</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Parent Menu Modal -->
<div class="modal fade" id="addParentMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Parent Menu</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="<?php echo base_url('rbac/add_parent_menu'); ?>" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="menu_name" required placeholder="e.g. Master Data">
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <input type="text" class="form-control" name="icon" value="fa fa-folder" placeholder="e.g. fa fa-folder">
                        <small class="form-text text-muted">Font Awesome class, e.g., "fa-folder", "fa-cogs"</small>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" value="0" placeholder="Otomatis jika kosong">
                        <small class="form-text text-muted">Kosongkan atau isi 0 untuk urutan otomatis</small>
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
                    <button type="submit" class="btn btn-primary">Save Parent Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Parent Menu Modal -->
<div class="modal fade" id="editParentMenuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Parent Menu</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editParentMenuForm" method="post">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Menu Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="menu_name" id="edit_menu_name" required>
                    </div>
                    <div class="form-group">
                        <label>Icon</label>
                        <input type="text" class="form-control" name="icon" id="edit_icon">
                        <small class="form-text text-muted">Font Awesome class, e.g., "fa-folder"</small>
                    </div>
                    <div class="form-group">
                        <label>Sort Order</label>
                        <input type="number" class="form-control" name="sort_order" id="edit_sort_order">
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
                    <button type="submit" class="btn btn-primary">Update Parent Menu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Edit Parent Menu (using vanilla JS)
function editParentMenu(menu) {
    document.getElementById('edit_menu_name').value = menu.menu_name;
    document.getElementById('edit_icon').value = menu.icon;
    document.getElementById('edit_sort_order').value = menu.sort_order;
    document.getElementById('edit_is_active').checked = menu.is_active == 1;
    
    document.getElementById('editParentMenuForm').action = '<?php echo base_url('rbac/edit_parent_menu/'); ?>' + menu.id_parent_menu;
    
    // Show modal using Bootstrap's native method
    var editModal = new bootstrap.Modal(document.getElementById('editParentMenuModal'));
    editModal.show();
}

// Delete Parent Menu with SweetAlert2 confirmation
function deleteParentMenu(id, menuName) {
    Swal.fire({
        title: 'Hapus Parent Menu?',
        html: 'Apakah Anda yakin ingin menghapus <strong>"' + menuName + '"</strong>?<br><small class="text-muted">Module yang terkait akan menjadi standalone.</small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?php echo base_url('rbac/delete_parent_menu/'); ?>' + id;
        }
    });
}
</script>

