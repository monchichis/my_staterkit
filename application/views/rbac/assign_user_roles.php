<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Assign Roles to Users</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo base_url('admin'); ?>">Home</a></li>
            <li class="breadcrumb-item">RBAC</li>
            <li class="breadcrumb-item active"><strong>Assign Roles</strong></li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>User Role Assignment</h5>
                    <small>Assign one or more roles to users</small>
                </div>
                <div class="ibox-content">
                    <div class="form-group">
                        <label>Select User:</label>
                        <select class="form-control select2" id="userSelector">
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $user): ?>
                            <option value="<?php echo $user->id_user; ?>">
                                <?php echo $user->nama; ?> (<?php echo $user->email; ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div id="roleAssignment" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Instructions:</strong> Select one or more roles for this user.
                        </div>
                        
                        <form id="roleForm">
                            <input type="hidden" id="selectedUserId" name="user_id">
                            
                            <div class="form-group">
                                <label>Assign Roles:</label>
                                <?php foreach ($roles as $role): ?>
                                <?php $has_permissions = $role->permission_count > 0; ?>
                                <div class="checkbox <?php echo $has_permissions ? '' : 'disabled text-muted'; ?>" style="<?php echo $has_permissions ? '' : 'opacity: 0.6;'; ?>">
                                    <label>
                                        <input type="checkbox" 
                                               name="roles[]" 
                                               value="<?php echo $role->id_role; ?>"
                                               class="role-checkbox"
                                               <?php echo $has_permissions ? '' : 'disabled'; ?>>
                                        <strong><?php echo $role->role_name; ?></strong>
                                        <br><small class="<?php echo $has_permissions ? 'text-muted' : 'text-danger'; ?>">
                                            <?php echo $role->role_description; ?>
                                            <?php if (!$has_permissions): ?>
                                            <br><i class="fa fa-exclamation-triangle"></i> No permissions assigned. 
                                            <a href="<?php echo base_url('rbac/assign_permissions'); ?>" target="_blank">Assign Permissions</a>
                                            <?php else: ?>
                                            <br><span class="badge badge-info"><?php echo $role->permission_count; ?> permissions</span>
                                            <?php endif; ?>
                                        </small>
                                    </label>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div class="form-group text-right">
                                <button type="button" class="btn btn-primary btn-lg" id="saveUserRoles">
                                    <i class="fa fa-save"></i> Save User Roles
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script>
var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

$('#userSelector').change(function() {
    var userId = $(this).val();
    
    if (!userId) {
        $('#roleAssignment').hide();
        return;
    }
    
    $('#selectedUserId').val(userId);
    $('.role-checkbox').prop('checked', false);
    
    var data = { user_id: userId };
    data[csrfName] = csrfHash;
    
    // Load user roles
    $.ajax({
        url: '<?php echo base_url('rbac/get_user_roles'); ?>',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
            // Update CSRF hash
            if (response.csrfHash) {
                csrfHash = response.csrfHash;
            }
            
            if (response.status) {
                response.roles.forEach(function(roleId) {
                    $('.role-checkbox[value="' + roleId + '"]').prop('checked', true);
                });
                $('#roleAssignment').show();
            }
        },
        error: function() {
            // On error we can't easily recover exact token without refresh, but usually 500 doesn't burn token if not processed. 
            // If it was 403 Forbidden (CSRF), we strictly need refresh.
            // For now just alert.
            alert('Error loading roles. Please refresh page.');
        }
    });
});

$('#saveUserRoles').click(function() {
    var btn = $(this);
    var originalText = btn.html();
    btn.html('<i class="fa fa-spinner fa-spin"></i> Saving...').prop('disabled', true);
    
    var formData = $('#roleForm').serializeArray();
    formData.push({name: csrfName, value: csrfHash});
    
    $.ajax({
        url: '<?php echo base_url('rbac/save_user_roles'); ?>',
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
            } else {
                Swal.fire("Error!", response.message, "error");
            }
        },
        error: function() {
            btn.html(originalText).prop('disabled', false);
            Swal.fire("Error!", "Failed to save user roles. Please refresh the page and try again.", "error");
        }
    });
});
</script>
