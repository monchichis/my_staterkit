<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Assign Permissions to Roles</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('admin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Assign Permissions</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Permission Assignment Matrix</h5>
                    <small>Assign permissions to roles by checking the boxes below</small>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label>Select Role:</label>
                        <select class="form-control" id="roleSelector">
                            <option value="">-- Select Role --</option>
                            <?php foreach ($matrix['roles'] as $role): ?>
                            <option value="<?php echo $role->id_role; ?>"><?php echo $role->role_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="permissionMatrix" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> Check the boxes to assign permissions to the selected role. Changes are saved automatically.
                        </div>
                        
                        <form id="permissionForm">
                            <input type="hidden" id="selectedRoleId" name="role_id">
                            
                            <?php foreach ($matrix['permissions'] as $module_name => $permissions): ?>
                            <div class="panel panel-primary">
                                <div class="panel-heading">
                                    <h4 class="panel-title">
                                        <i class="fa fa-cube"></i> <?php echo $module_name; ?>
                                    </h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php foreach ($permissions as $perm): ?>
                                        <div class="col-md-3 col-sm-4 col-xs-6">
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="permissions[]" 
                                                           value="<?php echo $perm->id_permission; ?>"
                                                           class="permission-checkbox"
                                                           data-permission-id="<?php echo $perm->id_permission; ?>">
                                                    <strong><?php echo $perm->permission_name; ?></strong>
                                                    <br><small class="text-muted"><?php echo $perm->permission_key; ?></small>
                                                </label>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                            
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-primary btn-lg" id="savePermissions">
                                    <i class="fa fa-save"></i> Save Permissions
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom script for assign_permissions - loaded AFTER footer so jQuery is available -->
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

$(document).ready(function() {
    var rolePermissions = <?php echo json_encode($matrix['matrix']); ?>;
    
    $('#roleSelector').change(function() {
        var roleId = $(this).val();
        
        if (!roleId) {
            $('#permissionMatrix').hide();
            return;
        }
        
        $('#selectedRoleId').val(roleId);
        $('#permissionMatrix').show();
        
        // Uncheck all
        $('.permission-checkbox').prop('checked', false);
        
        // Check permissions for selected role
        if (rolePermissions[roleId]) {
            var permissions = rolePermissions[roleId].permissions;
            permissions.forEach(function(permId) {
                $('input[data-permission-id="' + permId + '"]').prop('checked', true);
            });
        }
    });
    
    $('#savePermissions').click(function() {
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
        
        var formData = $('#permissionForm').serializeArray();
        formData.push({name: csrfName, value: csrfHash});
        
        $.ajax({
            url: '<?php echo base_url('rbac/save_permissions'); ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                btn.html(originalText).prop('disabled', false);
                
                // Update CSRF hash
                if (response.csrfHash) {
                    csrfHash = response.csrfHash;
                }
                
                if (response.status) {
                    Swal.fire("Success!", response.message, "success");
                    
                    // Update cache
                    var roleId = $('#selectedRoleId').val();
                    var permissions = [];
                    $('.permission-checkbox:checked').each(function() {
                        permissions.push(parseInt($(this).val()));
                    });
                    
                    if (!rolePermissions[roleId]) {
                        rolePermissions[roleId] = { role_id: roleId, permissions: [] };
                    }
                    rolePermissions[roleId].permissions = permissions;
                } else {
                    Swal.fire("Error!", response.message, "error");
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
                Swal.fire("Error!", "Failed to save permissions. Please refresh the page and try again.", "error");
            }
        });
    });
});
</script>
