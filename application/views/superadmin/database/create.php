<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Create New Table</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager'); ?>">Database Manager</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Create Table</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>New Table Configuration</h5>
                </div>
                <div class="ibox-content">
                    <?= $this->session->flashdata('message'); ?>
                    <form action="<?= base_url('DatabaseManager/store'); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Table Name</label>
                            <div class="col-sm-10">
                                <input type="text" name="table_name" class="form-control" required placeholder="e.g., mst_products">
                            </div>
                        </div>
                        <div class="hr-line-dashed"></div>
                        
                        <h4>Columns</h4>
                        <table class="table table-bordered" id="columns_table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length/Values</th>
                                    <th class="text-center">PK</th>
                                    <th class="text-center">AI</th>
                                    <th class="text-center">Null</th>
                                    <th class="text-center">Relation</th>
                                    <th>Ref Table</th>
                                    <th>Ref Column</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="column-row">
                                    <td><input type="text" name="columns[0][name]" class="form-control" required></td>
                                    <td>
                                        <select name="columns[0][type]" class="form-control">
                                            <option value="">Select Type</option>
                                            <option value="INT">INT</option>
                                            <option value="VARCHAR">VARCHAR</option>
                                            <option value="TEXT">TEXT</option>
                                            <option value="DATE">DATE</option>
                                            <option value="DATETIME">DATETIME</option>
                                            <option value="TIMESTAMP">TIMESTAMP</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="columns[0][length]" class="form-control"></td>
                                    <td class="text-center"><input type="checkbox" name="columns[0][primary_key]" value="1"></td>
                                    <td class="text-center"><input type="checkbox" name="columns[0][auto_increment]" value="1"></td>
                                    <td class="text-center"><input type="checkbox" name="columns[0][null]" value="1"></td>
                                    <td class="text-center"><input type="checkbox" name="columns[0][is_foreign_key]" value="1" class="is-foreign-key"></td>
                                    <td>
                                        <select name="columns[0][ref_table]" class="form-control ref-table" disabled>
                                            <option value="">Select Table</option>
                                            <?php foreach ($tables as $table) : ?>
                                                <option value="<?= $table; ?>"><?= $table; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select name="columns[0][ref_column]" class="form-control ref-column" disabled>
                                            <option value="">Select Column</option>
                                        </select>
                                    </td>
                                    <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-info btn-sm" id="add_column"><i class="fa fa-plus"></i> Add Column</button>
                        
                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a href="<?= base_url('DatabaseManager'); ?>" class="btn btn-white btn-sm">Cancel</a>
                                <button class="btn btn-primary btn-sm" type="submit">Save changes</button>
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
        let rowCount = 1;
        const tableBody = document.querySelector('#columns_table tbody');
        const addButton = document.getElementById('add_column');
        
        // Pass PHP tables array to JS 
        const tables = <?= json_encode($tables); ?>;
        
        function generateTableOptions() {
            let options = '<option value="">Select Table</option>';
            tables.forEach(table => {
                options += `<option value="${table}">${table}</option>`;
            });
            return options;
        }

        addButton.addEventListener('click', function() {
            const newRow = document.createElement('tr');
            newRow.classList.add('column-row');
            newRow.innerHTML = `
                <td><input type="text" name="columns[${rowCount}][name]" class="form-control" required></td>
                <td>
                    <select name="columns[${rowCount}][type]" class="form-control">
                        <option value="">Select Type</option>
                        <option value="INT">INT</option>
                        <option value="VARCHAR">VARCHAR</option>
                        <option value="TEXT">TEXT</option>
                        <option value="DATE">DATE</option>
                        <option value="DATETIME">DATETIME</option>
                        <option value="TIMESTAMP">TIMESTAMP</option>
                    </select>
                </td>
                <td><input type="text" name="columns[${rowCount}][length]" class="form-control"></td>
                <td class="text-center"><input type="checkbox" name="columns[${rowCount}][primary_key]" value="1"></td>
                <td class="text-center"><input type="checkbox" name="columns[${rowCount}][auto_increment]" value="1"></td>
                <td class="text-center"><input type="checkbox" name="columns[${rowCount}][null]" value="1"></td>
                <td class="text-center"><input type="checkbox" name="columns[${rowCount}][is_foreign_key]" value="1" class="is-foreign-key"></td>
                <td>
                    <select name="columns[${rowCount}][ref_table]" class="form-control ref-table" disabled>
                        ${generateTableOptions()}
                    </select>
                </td>
                <td>
                    <select name="columns[${rowCount}][ref_column]" class="form-control ref-column" disabled>
                        <option value="">Select Column</option>
                    </select>
                </td>
                <td class="text-center"><button type="button" class="btn btn-danger btn-xs remove-row"><i class="fa fa-trash"></i></button></td>
            `;
            tableBody.appendChild(newRow);
            rowCount++;
        });

        tableBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row')) {
                const row = e.target.closest('tr');
                if (document.querySelectorAll('.column-row').length > 1) {
                    row.remove();
                } else {
                    Swal.fire({
                        title: "Warning",
                        text: "You must have at least one column.",
                        icon: "warning"
                    });
                }
            }
        });

        // Event delegation for type change, auto increment, and foreign key
        tableBody.addEventListener('change', function(e) {
            
            // Handle Type Change for Default INT length
            if(e.target.matches('select[name*="[type]"]')) {
                const type = e.target.value;
                const row = e.target.closest('tr');
                const lengthInput = row.querySelector('input[name*="[length]"]');
                
                if (type === 'INT' && lengthInput.value === '') {
                    lengthInput.value = '11';
                }
            }
            
            // Handle Auto Increment Change
            if(e.target.matches('input[name*="[auto_increment]"]')) {
                 const row = e.target.closest('tr');
                 const pkCheckbox = row.querySelector('input[name*="[primary_key]"]');
                 
                 if (e.target.checked) {
                     pkCheckbox.checked = true;
                 }
            }

            // Handle Foreign Key Checkbox
            if(e.target.matches('.is-foreign-key')) {
                const row = e.target.closest('tr');
                const refTableSelect = row.querySelector('.ref-table');
                const refColumnSelect = row.querySelector('.ref-column');

                if (e.target.checked) {
                    refTableSelect.disabled = false;
                } else {
                    refTableSelect.disabled = true;
                    refColumnSelect.disabled = true;
                    refTableSelect.value = '';
                    refColumnSelect.innerHTML = '<option value="">Select Column</option>';
                }
            }

            // Handle Ref Table Change (Fetch Columns)
            if(e.target.matches('.ref-table')) {
                const row = e.target.closest('tr');
                const refColumnSelect = row.querySelector('.ref-column');
                const tableName = e.target.value;

                if (tableName) {
                    refColumnSelect.disabled = false;
                    
                    // Get CSRF token
                    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
                    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
                    
                    fetch('<?= base_url("DatabaseManager/get_table_columns"); ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'table=' + tableName + '&' + csrfName + '=' + csrfHash
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.csrfHash) {
                            csrfHash = data.csrfHash;
                            document.querySelector('input[name="'+csrfName+'"]').value = csrfHash;
                        }

                        if(data.error) {
                            console.error('Server Error:', data.error);
                            return;
                        }
                        
                        let options = '<option value="">Select Column</option>';
                        if (data.fields) {
                            data.fields.forEach(col => {
                                options += `<option value="${col}">${col}</option>`;
                            });
                        }
                        refColumnSelect.innerHTML = options;
                    })
                    .catch(error => console.error('Error:', error));
                } else {
                    refColumnSelect.disabled = true;
                    refColumnSelect.innerHTML = '<option value="">Select Column</option>';
                }
            }
        });
    });
</script>
