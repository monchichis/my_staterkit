<div class="step-content">
    <h3 class="mb-4"><i class="fa fa-cog"></i> Application Setup</h3>
    <p class="text-muted mb-4">Configure your application settings and create the administrator account.</p>
    
    <form action="<?php echo base_url('install/finalize'); ?>" method="post" enctype="multipart/form-data">
        <!-- Application Information -->
        <h5 class="mb-3 text-primary"><i class="fa fa-info-circle"></i> Application Information</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nama_aplikasi">Application Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_aplikasi" name="nama_aplikasi" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nama_developer">Developer Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="nama_developer" name="nama_developer" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="alamat">Address <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="alamat" name="alamat" rows="2" required></textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="telp">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="telp" name="telp" required>
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label for="logo">Application Logo</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="logo" name="logo" accept="image/*">
                <label class="custom-file-label" for="logo">Choose file...</label>
            </div>
            <small class="form-text text-muted">Supported formats: JPG, PNG, GIF (Max 2MB)</small>
            <div id="logoPreview" class="mt-2" style="display: none;">
                <img id="previewImage" src="" alt="Logo Preview" style="max-width: 200px; max-height: 100px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
            </div>
        </div>

        <div class="form-group">
            <label for="title_icon">Title Icon (Favicon)</label>
            <div class="custom-file">
                <input type="file" class="custom-file-input" id="title_icon" name="title_icon" accept="image/*">
                <label class="custom-file-label" for="title_icon">Choose file...</label>
            </div>
            <small class="form-text text-muted">Supported formats: ICO, PNG, JPG (Max 1MB). Displayed in browser tab.</small>
            <div id="iconPreview" class="mt-2" style="display: none;">
                <img id="previewIcon" src="" alt="Icon Preview" style="max-width: 50px; max-height: 50px; border: 1px solid #ddd; padding: 5px; border-radius: 5px;">
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Timezone Settings -->
        <h5 class="mb-3 text-primary"><i class="fa fa-globe"></i> Timezone Settings</h5>
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> Select your preferred timezone. This will be used as the default timezone for the application.
        </div>
        
        <div class="form-group">
            <label for="default_timezone">Default Timezone <span class="text-danger">*</span></label>
            <select class="form-control" id="default_timezone" name="default_timezone" required>
                <optgroup label="Indonesia">
                    <option value="Asia/Jakarta" selected>Asia/Jakarta (WIB - UTC+7)</option>
                    <option value="Asia/Makassar">Asia/Makassar (WITA - UTC+8)</option>
                    <option value="Asia/Jayapura">Asia/Jayapura (WIT - UTC+9)</option>
                </optgroup>
                <optgroup label="Asia">
                    <option value="Asia/Singapore">Asia/Singapore (UTC+8)</option>
                    <option value="Asia/Kuala_Lumpur">Asia/Kuala Lumpur (UTC+8)</option>
                    <option value="Asia/Bangkok">Asia/Bangkok (UTC+7)</option>
                    <option value="Asia/Ho_Chi_Minh">Asia/Ho Chi Minh (UTC+7)</option>
                    <option value="Asia/Manila">Asia/Manila (UTC+8)</option>
                    <option value="Asia/Tokyo">Asia/Tokyo (UTC+9)</option>
                    <option value="Asia/Seoul">Asia/Seoul (UTC+9)</option>
                    <option value="Asia/Shanghai">Asia/Shanghai (UTC+8)</option>
                    <option value="Asia/Hong_Kong">Asia/Hong Kong (UTC+8)</option>
                    <option value="Asia/Kolkata">Asia/Kolkata (UTC+5:30)</option>
                    <option value="Asia/Dubai">Asia/Dubai (UTC+4)</option>
                </optgroup>
                <optgroup label="Australia & Pacific">
                    <option value="Australia/Sydney">Australia/Sydney (UTC+10/+11)</option>
                    <option value="Australia/Melbourne">Australia/Melbourne (UTC+10/+11)</option>
                    <option value="Australia/Perth">Australia/Perth (UTC+8)</option>
                    <option value="Pacific/Auckland">Pacific/Auckland (UTC+12/+13)</option>
                </optgroup>
                <optgroup label="Europe">
                    <option value="Europe/London">Europe/London (UTC+0/+1)</option>
                    <option value="Europe/Paris">Europe/Paris (UTC+1/+2)</option>
                    <option value="Europe/Berlin">Europe/Berlin (UTC+1/+2)</option>
                    <option value="Europe/Amsterdam">Europe/Amsterdam (UTC+1/+2)</option>
                    <option value="Europe/Moscow">Europe/Moscow (UTC+3)</option>
                </optgroup>
                <optgroup label="Americas">
                    <option value="America/New_York">America/New York (UTC-5/-4)</option>
                    <option value="America/Los_Angeles">America/Los Angeles (UTC-8/-7)</option>
                    <option value="America/Chicago">America/Chicago (UTC-6/-5)</option>
                    <option value="America/Sao_Paulo">America/Sao Paulo (UTC-3)</option>
                </optgroup>
                <optgroup label="Other">
                    <option value="UTC">UTC (Coordinated Universal Time)</option>
                </optgroup>
            </select>
            <small class="form-text text-muted">Select the timezone that matches your location or server.</small>
        </div>

        <div class="form-group">
            <label for="session_timeout">Session Timeout <span class="text-danger">*</span></label>
            <select class="form-control" id="session_timeout" name="session_timeout" required>
                <option value="300">5 Minutes</option>
                <option value="600">10 Minutes</option>
                <option value="900">15 Minutes</option>
                <option value="1800">30 Minutes</option>
                <option value="3600" selected>60 Minutes (1 Hour)</option>
            </select>
            <small class="form-text text-muted">Select how long a user session can remain idle before expiring.</small>
        </div>
        
        <!-- Administrator Account -->
        <h5 class="mb-3 text-primary"><i class="fa fa-user-shield"></i> Administrator Account</h5>
        <div class="alert alert-warning">
            <i class="fa fa-exclamation-triangle"></i> <strong>Important:</strong> This will be your Super Admin account with full system access.
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="admin_name">Full Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="admin_name" name="admin_name" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="admin_nik">NIK/Employee ID <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="admin_nik" name="admin_nik" required>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="admin_email">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="admin_email" name="admin_email" required>
                    <small class="form-text text-muted">This will be your login username</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="admin_password">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="admin_password" name="admin_password" minlength="6" required>
                    <small class="form-text text-muted">Minimum 6 characters</small>
                </div>
            </div>
        </div>
        
        <hr class="my-4">
        
        <!-- Uninstall Secret Key -->
        <h5 class="mb-3 text-primary"><i class="fa fa-key"></i> Uninstall Secret Key</h5>
        <div class="alert alert-danger">
            <i class="fa fa-exclamation-triangle"></i> <strong>Important:</strong> This secret key will be required to uninstall the application. Keep it safe!
        </div>
        
        <div class="form-group">
            <label for="uninstall_secret_key">Secret Key <span class="text-danger">*</span></label>
            <input type="password" class="form-control" id="uninstall_secret_key" name="uninstall_secret_key" minlength="8" required>
            <small class="form-text text-muted">Minimum 8 characters. You will need this key to uninstall the application.</small>
        </div>
        
        <div class="form-group text-right mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-check"></i> Complete Installation
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    // File input label update and preview
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
        
        var inputId = $(this).attr('id');
        var previewContainerId = (inputId === 'logo') ? '#logoPreview' : '#iconPreview';
        var previewImageId = (inputId === 'logo') ? '#previewImage' : '#previewIcon';
        
        // Preview image
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $(previewImageId).attr('src', e.target.result);
                $(previewContainerId).show();
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
    
    // Form validation
    $('form').submit(function(e) {
        var isValid = true;
        var errorMsg = '';
        
        if ($('#admin_password').val().length < 6) {
            isValid = false;
            errorMsg = 'Password must be at least 6 characters long.';
        }
        
        if (!isValid) {
            e.preventDefault();
            Swal.fire("Validation Error", errorMsg, "error");
            return false;
        }
        
        // Show loading
        $(this).find('button[type="submit"]').html('<i class="fa fa-spinner fa-spin"></i> Processing...').prop('disabled', true);
    });
});
</script>
