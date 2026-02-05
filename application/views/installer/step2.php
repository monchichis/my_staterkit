<div class="step-content">
    <h3 class="mb-4"><i class="fa fa-download"></i> Import Database Schema</h3>
    <p class="text-muted mb-4">This step will import all necessary tables including RBAC system tables.</p>
    
    <div class="alert alert-info">
        <h5><i class="fa fa-info-circle"></i> Tables to be created:</h5>
        <ul class="mb-0">
            <li><strong>mst_user</strong> - User management table</li>
            <li><strong>tbl_aplikasi</strong> - Application settings</li>
            <li><strong>mst_roles</strong> - RBAC roles</li>
            <li><strong>mst_modules</strong> - Application modules</li>
            <li><strong>mst_permissions</strong> - Permission definitions</li>
            <li><strong>tbl_role_permissions</strong> - Role-permission relationships</li>
            <li><strong>tbl_user_roles</strong> - User-role relationships</li>
            <li><strong>tbl_rbac_audit_log</strong> - RBAC audit trail</li>
            <li><strong>tbl_crud_history</strong> - CRUD Generator history tracking</li>
        </ul>
    </div>
    
    <div id="importStatus" class="mb-3"></div>
    
    <div class="progress mb-3" style="display: none;" id="progressBar">
        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
             style="width: 0%" id="progressValue">0%</div>
    </div>
    
    <div class="form-group text-right">
        <button type="button" class="btn btn-primary" id="importBtn">
            <i class="fa fa-download"></i> Start Import
        </button>
        <button type="button" class="btn btn-success" id="nextBtn" style="display: none;" onclick="window.location.href='<?php echo base_url('install/step3'); ?>'">
            Next Step <i class="fa fa-arrow-right"></i>
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#importBtn').click(function() {
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Importing...');
        $('#progressBar').show();
        $('#importStatus').html('');
        
        // Simulate progress
        var progress = 0;
        var progressInterval = setInterval(function() {
            progress += 5;
            if (progress <= 90) {
                $('#progressValue').css('width', progress + '%').text(progress + '%');
            }
        }, 200);
        
        $.ajax({
            url: '<?php echo base_url('install/import_sql'); ?>',
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                clearInterval(progressInterval);
                $('#progressValue').css('width', '100%').text('100%');
                
                setTimeout(function() {
                    if (response.status) {
                        $('#importStatus').html(
                            '<div class="alert alert-success">' +
                            '<i class="fa fa-check-circle"></i> <strong>Success!</strong><br>' +
                            response.message + '<br>' +
                            '<small>Total statements executed: ' + response.success_count + '</small>' +
                            '</div>'
                        );
                        btn.hide();
                        $('#nextBtn').show();
                    } else {
                        $('#importStatus').html(
                            '<div class="alert alert-danger">' +
                            '<i class="fa fa-times-circle"></i> <strong>Error!</strong><br>' +
                            response.message +
                            (response.errors ? '<br><small>' + response.errors.join('<br>') + '</small>' : '') +
                            '</div>'
                        );
                        btn.prop('disabled', false).html('<i class="fa fa-download"></i> Retry Import');
                        $('#progressBar').hide();
                    }
                }, 500);
            },
            error: function(xhr, status, error) {
                clearInterval(progressInterval);
                $('#importStatus').html(
                    '<div class="alert alert-danger">' +
                    '<i class="fa fa-times-circle"></i> <strong>Error!</strong><br>' +
                    'Failed to import SQL file. Please check server logs.<br>' +
                    '<small>Error: ' + error + '</small>' +
                    '</div>'
                );
                btn.prop('disabled', false).html('<i class="fa fa-download"></i> Retry Import');
                $('#progressBar').hide();
            }
        });
    });
});
</script>
