<div class="step-content">
    <h3 class="mb-4"><i class="fa fa-database"></i> Database Configuration</h3>
    <p class="text-muted mb-4">Please provide your MySQL database credentials. We'll test the connection first, then create the database for you.</p>
    
    <form id="dbForm">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="hostname">
                        <i class="fa fa-server"></i> Database Host
                    </label>
                    <input type="text" class="form-control" id="hostname" name="hostname" value="localhost" required>
                    <small class="form-text text-muted">Usually "localhost"</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="database">
                        <i class="fa fa-database"></i> Database Name
                    </label>
                    <input type="text" class="form-control" id="database" name="database" placeholder="my_application_db" required>
                    <small class="form-text text-muted">Will be created if doesn't exist</small>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="username">
                        <i class="fa fa-user"></i> Database Username
                    </label>
                    <input type="text" class="form-control" id="username" name="username" value="root" required>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">
                        <i class="fa fa-lock"></i> Database Password
                    </label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank if no password">
                </div>
            </div>
        </div>
        
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> 
            <strong>Note:</strong> Make sure MySQL server is running and the credentials are correct.
        </div>
        
        <div class="form-group text-right">
            <button type="button" class="btn btn-secondary" id="testBtn">
                <i class="fa fa-plug"></i> Test Connection
            </button>
            <button type="button" class="btn btn-primary" id="createDbBtn" disabled>
                <i class="fa fa-database"></i> Create Database & Continue
            </button>
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    var connectionTested = false;
    
    // Function to check if database name is filled
    function checkDatabaseName() {
        var dbName = $('#database').val().trim();
        if (dbName === '') {
            $('#testBtn').prop('disabled', true);
            $('#createDbBtn').prop('disabled', true);
        } else {
            $('#testBtn').prop('disabled', false);
            // createDbBtn stays disabled until connection is tested
            if (!connectionTested) {
                $('#createDbBtn').prop('disabled', true);
            }
        }
    }
    
    // Check on page load
    checkDatabaseName();
    
    // Check when database name changes
    $('#database').on('input', function() {
        connectionTested = false; // Reset connection test when database name changes
        checkDatabaseName();
    });
    
    // Test MySQL Server Connection (without database)
    $('#testBtn').click(function() {
        var btn = $(this);
        var originalText = btn.html();
        
        // Validate inputs
        if (!$('#hostname').val() || !$('#username').val() || !$('#database').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Information',
                text: 'Please fill in hostname, username, and database name first.'
            });
            return;
        }
        
        btn.html('<i class="fa fa-spinner fa-spin"></i> Testing...').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo base_url('install/test_connection'); ?>',
            type: 'POST',
            data: {
                hostname: $('#hostname').val(),
                username: $('#username').val(),
                password: $('#password').val()
                // Note: NO database parameter - just test MySQL server connection
            },
            dataType: 'json',
            success: function(response) {
                btn.html(originalText).prop('disabled', false);
                
                if (response.status) {
                    connectionTested = true;
                    $('#createDbBtn').prop('disabled', false);
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Connection Successful!',
                        html: '<p>' + response.message + '</p>' +
                              '<p class="mb-0"><strong>MySQL Server:</strong> Connected ✓</p>' +
                              '<p class="text-muted">You can now create the database.</p>',
                        confirmButtonText: 'OK'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Connection Failed',
                        html: '<p>' + response.message + '</p>' +
                              '<p class="text-muted">Please check your MySQL credentials.</p>',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                btn.html(originalText).prop('disabled', false);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Failed to test connection. Please check your input and try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Create Database and Continue
    $('#createDbBtn').click(function() {
        if (!connectionTested) {
            Swal.fire({
                icon: 'warning',
                title: 'Test Connection First',
                text: 'Please test the MySQL connection before creating database.'
            });
            return;
        }
        
        // Validate database name
        if (!$('#database').val()) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Database Name',
                text: 'Please enter a database name.'
            });
            return;
        }
        
        var btn = $(this);
        var originalText = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i> Creating Database...').prop('disabled', true);
        $('#testBtn').prop('disabled', true);
        
        $.ajax({
            url: '<?php echo base_url('install/create_database'); ?>',
            type: 'POST',
            data: {
                hostname: $('#hostname').val(),
                username: $('#username').val(),
                password: $('#password').val(),
                database: $('#database').val()
            },
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Database Created!',
                        html: '<p>' + response.message + '</p>' +
                              '<p class="text-muted">Redirecting to next step...</p>',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    }).then(function() {
                        window.location.href = '<?php echo base_url('install/step2'); ?>';
                    });
                } else {
                    btn.html(originalText).prop('disabled', false);
                    $('#testBtn').prop('disabled', false);
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Database Creation Failed',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function() {
                btn.html(originalText).prop('disabled', false);
                $('#testBtn').prop('disabled', false);
                
                Swal.fire({
                    icon: 'error',
                    title: 'Request Failed',
                    text: 'Failed to create database. Please try again.',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Reset create button if user changes critical credentials (not password)
    $('#hostname, #username').on('input', function() {
        if (connectionTested) {
            connectionTested = false;
            $('#createDbBtn').prop('disabled', true);
        }
    });
});
</script>
