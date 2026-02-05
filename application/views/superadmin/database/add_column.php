<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Add Column to <?= $table_name; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager'); ?>">Database Manager</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager/detail/' . $table_name); ?>">Table Detail</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Add Column</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>New Column Configuration</h5>
                </div>
                <div class="ibox-content">
                    <?= $this->session->flashdata('message'); ?>
                    <form action="<?= base_url('DatabaseManager/store_column/' . $table_name); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length/Values</th>
                                    <th>Default</th>
                                    <th class="text-center">PK</th>
                                    <th class="text-center">AI</th>
                                    <th class="text-center">Null</th>
                                    <th class="text-center">Relation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="text" name="column[name]" class="form-control" required></td>
                                    <td>
                                        <select name="column[type]" class="form-control" id="col_type">
                                            <option value="INT">INT</option>
                                            <option value="VARCHAR">VARCHAR</option>
                                            <option value="TEXT">TEXT</option>
                                            <option value="DATE">DATE</option>
                                            <option value="DATETIME">DATETIME</option>
                                            <option value="TIMESTAMP">TIMESTAMP</option>
                                            <option value="FLOAT">FLOAT</option>
                                            <option value="DOUBLE">DOUBLE</option>
                                            <option value="DECIMAL">DECIMAL</option>
                                            <option value="ENUM">ENUM</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="column[length]" class="form-control" id="col_length"></td>
                                    <td><input type="text" name="column[default]" class="form-control"></td>
                                    <td class="text-center"><input type="checkbox" name="column[primary_key]" value="1" id="col_pk"></td>
                                    <td class="text-center"><input type="checkbox" name="column[auto_increment]" value="1" id="col_ai"></td>
                                    <td class="text-center"><input type="checkbox" name="column[null]" value="1"></td>
                                    <td class="text-center"><input type="checkbox" name="column[is_foreign_key]" value="1" id="col_fk"></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Relation Configuration -->
                        <div id="relation_config" style="display: none;" class="alert alert-info mt-3">
                            <h4><i class="fa fa-link"></i> Foreign Key Configuration</h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reference Table</label>
                                        <select name="column[ref_table]" class="form-control" id="ref_table">
                                            <option value="">-- Select Table --</option>
                                            <?php foreach($tables as $tbl): ?>
                                                <option value="<?= $tbl; ?>"><?= $tbl; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Reference Column</label>
                                        <select name="column[ref_column]" class="form-control" id="ref_column">
                                            <option value="">-- Select Table First --</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a href="<?= base_url('DatabaseManager/detail/' . $table_name); ?>" class="btn btn-white btn-sm">Cancel</a>
                                <button class="btn btn-primary btn-sm" type="submit">Add Column</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('col_type');
        const lengthInput = document.getElementById('col_length');
        const aiCheckbox = document.getElementById('col_ai');
        const pkCheckbox = document.getElementById('col_pk');
        
        // Relation Elements
        const fkCheckbox = document.getElementById('col_fk');
        const relationConfig = document.getElementById('relation_config');
        const refTableSelect = document.getElementById('ref_table');
        const refColumnSelect = document.getElementById('ref_column');
        
        // Global CSRF vars
        var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
        var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

        function updateCsrf(newHash) {
            if(!newHash) return;
            csrfHash = newHash;
            document.querySelector('input[name="'+csrfName+'"]').value = newHash;
        }

        typeSelect.addEventListener('change', function() {
            if (this.value === 'INT' && lengthInput.value === '') {
                lengthInput.value = '11';
            }
        });

        aiCheckbox.addEventListener('change', function() {
            if (this.checked) {
                pkCheckbox.checked = true;
            }
        });
        
        // Show/Hide Relation Config
        fkCheckbox.addEventListener('change', function() {
            if (this.checked) {
                relationConfig.style.display = 'block';
                // Auto set type to INT 11 if not set, commonly used for FK
                if (typeSelect.value !== 'INT') {
                    typeSelect.value = 'INT';
                    lengthInput.value = '11';
                }
            } else {
                relationConfig.style.display = 'none';
                refTableSelect.value = '';
                refColumnSelect.innerHTML = '<option value="">-- Select Table First --</option>';
            }
        });
        
        // Fetch Reference Columns
        refTableSelect.addEventListener('change', function() {
            const table = this.value;
            if (table) {
                refColumnSelect.innerHTML = '<option value="">Loading...</option>';
                
                // Use AJAX to fetch columns
                const formData = new FormData();
                formData.append('table', table);
                formData.append(csrfName, csrfHash);
                
                fetch('<?= base_url("DatabaseManager/get_table_columns"); ?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.csrfHash) {
                        updateCsrf(data.csrfHash);
                    }
                    
                    if(data.error) {
                        console.error('Server Error:', data.error);
                        refColumnSelect.innerHTML = '<option value="">Error: ' + data.error + '</option>';
                        return;
                    }
                    
                    let options = '<option value="">-- Select Column --</option>';
                    if(data.fields) {
                        data.fields.forEach(col => {
                            options += `<option value="${col}">${col}</option>`;
                        });
                    }
                    refColumnSelect.innerHTML = options;
                    
                    // Try to auto-select 'id' or table_name_id if matches
                    if (data.fields && data.fields.includes('id')) {
                        refColumnSelect.value = 'id';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    refColumnSelect.innerHTML = '<option value="">Error fetching columns</option>';
                });
            } else {
                refColumnSelect.innerHTML = '<option value="">-- Select Table First --</option>';
            }
        });
    });
</script>
