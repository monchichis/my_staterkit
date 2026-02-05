<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>CRUD Generator</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url('dashboard') ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>CRUD Generator</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Generate CRUD</h5>
                </div>
                <div class="ibox-content">
                    <?php echo $this->session->flashdata('message'); ?>
                    <form action="<?php echo base_url('CrudGenerator/generate') ?>" method="post" id="crudForm">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <?php if(isset($edit_data)): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data->id; ?>">
                        <?php endif; ?>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Select Table</label>
                            <div class="col-sm-10">
                                <select class="form-control" name="table_name" id="table_name" required>
                                    <option value="">-- Select Table --</option>
                                    <?php foreach ($tables as $table): ?>
                                        <option value="<?php echo $table ?>" <?php echo (isset($edit_data) && $edit_data->table_name == $table) ? 'selected' : ''; ?>><?php echo $table ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Controller Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="controller_name" id="controller_name" placeholder="E.g. Products" value="<?php echo isset($edit_data) ? $edit_data->controller_name : ''; ?>" required>
                                <span class="form-text m-b-none">Capitalized, no spaces.</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Model Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" name="model_name" id="model_name" placeholder="E.g. Products_model" value="<?php echo isset($edit_data) ? $edit_data->model_name : ''; ?>" required>
                                <span class="form-text m-b-none">Capitalized, no spaces.</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        


                        <!-- Notification Type Selection -->
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Notification Type</label>
                            <div class="col-sm-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="bootstrap" name="notification_type" <?php echo (!isset($edit_data) || $edit_data->notification_type == 'bootstrap') ? 'checked' : ''; ?>> 
                                        <i></i> Bootstrap Alert
                                        <button type="button" class="btn btn-xs btn-default ml-2" onclick="previewNotification('bootstrap')"><i class="fa fa-eye"></i> Preview</button>
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="sweetalert" name="notification_type" <?php echo (isset($edit_data) && $edit_data->notification_type == 'sweetalert') ? 'checked' : ''; ?>> 
                                        <i></i> SweetAlert2
                                        <button type="button" class="btn btn-xs btn-default ml-2" onclick="previewNotification('sweetalert')"><i class="fa fa-eye"></i> Preview</button>
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="izitoast" name="notification_type" <?php echo (isset($edit_data) && $edit_data->notification_type == 'izitoast') ? 'checked' : ''; ?>> 
                                        <i></i> IziToast
                                        <button type="button" class="btn btn-xs btn-default ml-2" onclick="previewNotification('izitoast')"><i class="fa fa-eye"></i> Preview</button>
                                    </label>
                                </div>
                                <span class="form-text m-b-none">Choose how success/error messages will be displayed.</span>
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <!-- Field Customization Section -->
                        <div id="fieldCustomization" style="display:none;">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Customize Fields</label>
                                <div class="col-sm-10">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Field Name</th>
                                                    <th width="10%">DB Type</th>
                                                    <th width="12%">HTML Input</th>
                                                    <th width="18%">Validation</th>
                                                    <th width="45%">Foreign Key</th>
                                                </tr>
                                            </thead>
                                            <tbody id="fieldsTableBody">
                                                <!-- Fields will be populated here via AJAX -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                        </div>
                        
                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <button class="btn btn-primary btn-sm" type="submit" id="generateBtn">
                                    <i class="fa fa-cogs"></i> Generate
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>

<!-- Page-Specific Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof jQuery === 'undefined') {
        console.error('jQuery is not loaded yet');
        return;
    }
    
    // Preview Notification Function
    window.previewNotification = function(type) {
        if (type === 'bootstrap') {
            // Check if alert area exists, if not create a temp one
            var alertHtml = `
                <div class="alert alert-success alert-dismissible fade show preview-alert" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 5px 15px rgba(0,0,0,0.2);">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <strong>Success!</strong> This is a standard Bootstrap alert.
                </div>
            `;
            $('body').append(alertHtml);
            setTimeout(function() {
                $('.preview-alert').alert('close');
            }, 3000);
        } else if (type === 'sweetalert') {
            Swal.fire({
                title: "Success!",
                text: "This is a SweetAlert2 notification.",
                icon: "success",
                confirmButtonColor: "#1ab394"
            });
        } else if (type === 'izitoast') {
            iziToast.success({
                title: 'OK',
                message: 'This is an IziToast notification!',
                position: 'topRight'
            });
        }
    };

    (function($) {
        var allTables = <?php echo json_encode(array_values($tables)); ?>;
        // Saved field configs from PHP if editing
        var savedFieldConfigs = <?php echo isset($field_configs) ? json_encode($field_configs) : 'null'; ?>;
        
        // CSRF Protection
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        // Trigger initial load if editing
        <?php if(isset($edit_data)): ?>
            fetchTableFields('<?php echo $edit_data->table_name; ?>');
        <?php endif; ?>

        // Auto-suggest names when table is selected
        $('#table_name').on('change', function(){
            var tableName = $(this).val();
            if(tableName) {
                // If not editing or if user manually changed the table
                if ($('#controller_name').val() === '' || (savedFieldConfigs && '<?php echo isset($edit_data) ? $edit_data->table_name : ''; ?>' !== tableName)) {
                    var parts = tableName.split('_');
                    var capitalizedParts = parts.map(function(part) {
                        return part.charAt(0).toUpperCase() + part.slice(1).toLowerCase();
                    });
                    var formatted = capitalizedParts.join('');
                    
                    $('#controller_name').val(formatted);
                    $('#model_name').val(formatted + '_model');
                }
                
                fetchTableFields(tableName);
            } else {
                $('#fieldCustomization').hide();
                $('#fieldsTableBody').empty();
            }
        });
        
        function fetchTableFields(tableName) {
            var data = {table_name: tableName};
            data[csrfName] = csrfHash;

            $.ajax({
                url: '<?php echo base_url('CrudGenerator/get_table_fields') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if(response.csrfHash) {
                        csrfHash = response.csrfHash;
                        $('input[name="'+csrfName+'"]').val(csrfHash);
                    }

                    if(response.success) {
                        displayFields(response.fields);
                        $('#fieldCustomization').show();
                    } else {
                        alert('Failed to fetch table fields: ' + response.message);
                    }
                },
                error: function() {
                    alert('Error fetching table fields');
                }
            });
        }
        
        function displayFields(fields) {
            var html = '';
            var inputTypes = ['text', 'number', 'email', 'password', 'tel', 'url', 'date', 'datetime-local', 'time', 'textarea', 'select', 'file'];
            
            fields.forEach(function(field, index) {
                var isPK = field.primary_key == 1;
                var fieldId = 'field_' + index;
                
                // Determine values (saved or default)
                var savedConfig = savedFieldConfigs && savedFieldConfigs[field.name];
                
                html += '<tr>';
                
                // Field Name
                html += '<td><strong>' + field.name + '</strong>' + (isPK ? ' <span class="badge badge-primary">PK</span>' : '') + '</td>';
                
                // DB Type
                html += '<td><small>' + field.type + (field.max_length > 0 ? '(' + field.max_length + ')' : '') + '</small></td>';
                
                // HTML Input Type
                html += '<td>';
                html += '<select class="form-control form-control-sm input-type-select" data-field="' + fieldId + '" name="field_configs[' + field.name + '][input_type]">';
                inputTypes.forEach(function(type) {
                    var selected = '';
                    if (savedConfig && savedConfig.input_type === type) {
                        selected = 'selected';
                    } else if (!savedConfig && type === field.suggested_input) {
                        selected = 'selected';
                    }
                    html += '<option value="' + type + '" ' + selected + '>' + type + '</option>';
                });
                html += '</select>';
                
                // File Options (shown only when file type is selected)
                var isFileType = (savedConfig && savedConfig.input_type === 'file') || (!savedConfig && field.suggested_input === 'file');
                var multipleChecked = savedConfig && savedConfig.file_multiple ? 'checked' : '';
                html += '<div id="' + fieldId + '_file_options" class="file-options mt-2" style="' + (isFileType ? '' : 'display:none;') + '">';
                html += '<label class="checkbox-inline" style="font-size:11px;"><input type="checkbox" name="field_configs[' + field.name + '][file_multiple]" value="1" ' + multipleChecked + '> <i class="fa fa-copy"></i> Multiple Files</label>';
                html += '</div>';
                html += '</td>';
                
                // Validation Options
                html += '<td style="font-size:12px;">';
                
                var reqChecked = (savedConfig && savedConfig.required) || (!savedConfig && !isPK) ? 'checked' : ''; 
                html += '<label class="checkbox-inline"><input type="checkbox" name="field_configs[' + field.name + '][required]" value="1" ' + reqChecked + '> Required</label><br>';
                
                var roChecked = (savedConfig && savedConfig.readonly) || isPK ? 'checked' : '';
                html += '<label class="checkbox-inline"><input type="checkbox" name="field_configs[' + field.name + '][readonly]" value="1" ' + roChecked + '> Readonly</label><br>';
                
                var emailChecked = (savedConfig && savedConfig.email) ? 'checked' : '';
                html += '<label class="checkbox-inline"><input type="checkbox" name="field_configs[' + field.name + '][email]" value="1" ' + emailChecked + '> Email</label><br>';
                
                var numChecked = (savedConfig && savedConfig.numeric) ? 'checked' : '';
                html += '<label class="checkbox-inline"><input type="checkbox" name="field_configs[' + field.name + '][numeric]" value="1" ' + numChecked + '> Numeric</label>';
                html += '</td>';
                
                // Foreign Key Configuration
                var hasFk = savedConfig && savedConfig.has_fk;
                html += '<td>';
                html += '<label class="checkbox-inline"><input type="checkbox" class="fk-checkbox" data-field="' + fieldId + '" name="field_configs[' + field.name + '][has_fk]" value="1" ' + (hasFk ? 'checked' : '') + '> Has Relationship</label>';
                html += '<div id="' + fieldId + '_fk" class="fk-config" style="' + (hasFk ? '' : 'display:none;') + ' margin-top:5px;">';
                html += '<div class="row">';
                
                var savedTable = hasFk ? savedConfig.fk_table : '';
                html += '<div class="col-md-4"><select class="form-control form-control-sm fk-table" data-field="' + fieldId + '" name="field_configs[' + field.name + '][fk_table]"><option value="">Ref Table</option>';
                allTables.forEach(function(table) {
                    html += '<option value="' + table + '" ' + (table === savedTable ? 'selected' : '') + '>' + table + '</option>';
                });
                html += '</select></div>';
                
                var savedKey = hasFk ? savedConfig.fk_key : '';
                var savedDisplay = hasFk ? savedConfig.fk_display : '';
                
                html += '<div class="col-md-4"><select class="form-control form-control-sm fk-key" id="' + fieldId + '_key" name="field_configs[' + field.name + '][fk_key]" data-saved="' + savedKey + '"><option value="">Key Column</option></select></div>';
                html += '<div class="col-md-4"><select class="form-control form-control-sm fk-display" id="' + fieldId + '_display" name="field_configs[' + field.name + '][fk_display]" data-saved="' + savedDisplay + '"><option value="">Display Column</option></select></div>';
                html += '</div>';
                html += '</div>';
                html += '</td>';
                
                html += '</tr>';
                
                // If there was a saved FK, trigger load for this row
                if (savedTable) {
                    loadTableColumns(savedTable, fieldId);
                }
            });
            
            $('#fieldsTableBody').html(html);
            
            // FK Checkbox handlers (standard checkbox events)
            $('.fk-checkbox').on('change', function() {
                var fieldId = $(this).data('field');
                if($(this).is(':checked')) {
                    $('#' + fieldId + '_fk').show();
                } else {
                    $('#' + fieldId + '_fk').hide();
                }
            });
            
            // FK Table selection handler
            $('.fk-table').on('change', function() {
                var fieldId = $(this).data('field');
                var refTable = $(this).val();
                if(refTable) {
                    loadTableColumns(refTable, fieldId);
                }
            });
            
            // Input Type change handler - show/hide file options
            $('.input-type-select').on('change', function() {
                var fieldId = $(this).data('field');
                var inputType = $(this).val();
                if(inputType === 'file') {
                    $('#' + fieldId + '_file_options').show();
                } else {
                    $('#' + fieldId + '_file_options').hide();
                }
            });
        }
        
        function loadTableColumns(tableName, fieldId) {
            var data = {table_name: tableName};
            data[csrfName] = csrfHash;

            $.ajax({
                url: '<?php echo base_url('CrudGenerator/get_table_columns') ?>',
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if(response.csrfHash) {
                        csrfHash = response.csrfHash;
                        $('input[name="'+csrfName+'"]').val(csrfHash);
                    }

                    if(response.success) {
                        var keySelect = $('#' + fieldId + '_key');
                        var displaySelect = $('#' + fieldId + '_display');
                        var savedKey = keySelect.data('saved');
                        var savedDisplay = displaySelect.data('saved');
                        
                        keySelect.empty().append('<option value="">Key Column</option>');
                        displaySelect.empty().append('<option value="">Display Column</option>');
                        
                        response.columns.forEach(function(col) {
                            keySelect.append('<option value="' + col + '" ' + (col === savedKey ? 'selected' : '') + '>' + col + '</option>');
                            displaySelect.append('<option value="' + col + '" ' + (col === savedDisplay ? 'selected' : '') + '>' + col + '</option>');
                        });
                    }
                },
                error: function() {
                    alert('Error loading table columns');
                }
            });
        }
    })(jQuery);
});
</script>
