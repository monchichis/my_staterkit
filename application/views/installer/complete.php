<div class="step-content text-center">
    <div class="mb-4">
        <i class="fa fa-check-circle" style="font-size: 80px; color: #28a745;"></i>
    </div>
    
    <h3 class="mb-3 text-success">Installation Complete!</h3>
    <p class="lead text-muted mb-4">Your application has been successfully installed with RBAC system.</p>
    
    <div class="alert alert-success text-left">
        <h5><i class="fa fa-info-circle"></i> What's Next?</h5>
        <ul class="mb-0">
            <li>Login using your administrator credentials</li>
            <li>Explore the RBAC Management section to manage roles and permissions</li>
            <li>Create additional users and assign them roles</li>
            <li>Customize modules and permissions based on your needs</li>
        </ul>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title"><i class="fa fa-shield"></i> Default Roles Created</h5>
            <div class="row">
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fa fa-check text-success"></i> <strong>Super Admin</strong> - Full access</li>
                        <li><i class="fa fa-check text-success"></i> <strong>Admin</strong> - Administrative access</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul class="list-unstyled">
                        <li><i class="fa fa-check text-success"></i> <strong>Manager</strong> - Management level</li>
                        <li><i class="fa fa-check text-success"></i> <strong>User</strong> - Standard access</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="alert alert-warning text-left">
        <h5><i class="fa fa-exclamation-triangle"></i> Security Notice</h5>
        <p class="mb-0">
            A lock file has been created at <code>application/config/installed.lock</code>. 
            This prevents the installer from running again. If you need to reinstall, delete this file first.
        </p>
    </div>
    
    <div class="mt-4">
        <a href="<?php echo base_url('auth'); ?>" class="btn btn-primary btn-lg">
            <i class="fa fa-sign-in"></i> Go to Login Page
        </a>
    </div>
    
    <div class="mt-3">
        <small class="text-muted">
            Thank you for using our application installer!
        </small>
    </div>
</div>

<script>
$(document).ready(function() {
    // Confetti animation or celebration effect (optional)
    setTimeout(function() {
        Swal.fire({
            title: "Congratulations!",
            text: "Installation completed successfully. You can now login to your application.",
            icon: "success",
            confirmButtonText: "Awesome!"
        });
    }, 500);
});
</script>
