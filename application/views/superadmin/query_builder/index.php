<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Query Builder</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('superadmin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Query Builder</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<style>
    /* Query Builder Styles */
    .query-builder-container {
        padding: 20px;
        overflow-x: hidden;
    }
    
    .column-cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 15px;
        margin-bottom: 20px;
        width: 100%;
    }
    
    .column-card {
        background: #fff;
        border: 1px solid #e5e6e7;
        border-radius: 4px;
        padding: 15px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        position: relative;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        transition: box-shadow 0.3s ease, transform 0.2s ease;
    }
    
    .column-card:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.15);
        transform: translateY(-2px);
    }
    
    .column-card .remove-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #999;
        transition: color 0.2s;
    }
    
    .column-card .remove-btn:hover {
        color: #ed5565;
    }
    
    .column-card .selectors {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
        margin-right: 25px;
    }
    
    .column-card .selectors select {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #e5e6e7;
        border-radius: 3px;
        font-size: 13px;
        box-sizing: border-box;
        min-width: 0;
    }
    
    .column-card .show-checkbox {
        margin-bottom: 12px;
    }
    
    .column-card .show-checkbox label {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        cursor: pointer;
    }
    
    .column-card .show-checkbox input[type="checkbox"] {
        width: 16px;
        height: 16px;
        accent-color: #1ab394;
    }
    
    .column-card .alias-inputs {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-bottom: 12px;
    }
    
    .column-card .alias-inputs input {
        width: 100%;
        padding: 6px 10px;
        border: 1px solid #e5e6e7;
        border-radius: 3px;
        font-size: 12px;
        background: #f9f9f9;
        box-sizing: border-box;
        min-width: 0;
    }
    
    .column-card .alias-inputs input::placeholder {
        color: #aaa;
    }
    
    .column-card .criteria-section {
        border-top: 1px solid #eee;
        padding-top: 12px;
    }
    
    .column-card .criteria-toggle {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 10px;
    }
    
    .column-card .criteria-toggle label {
        font-size: 13px;
        cursor: pointer;
    }
    
    .column-card .criteria-options {
        display: none;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 3px;
    }
    
    .column-card .criteria-options.active {
        display: block;
    }
    
    .column-card .sort-options {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .column-card .sort-options label {
        display: flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        cursor: pointer;
    }
    
    .column-card .criteria-row {
        display: flex;
        gap: 6px;
        margin-bottom: 8px;
        align-items: center;
    }
    
    .column-card .criteria-row label {
        font-size: 12px;
        width: 50px;
        flex-shrink: 0;
    }
    
    .column-card .criteria-row select,
    .column-card .criteria-row input {
        flex: 1;
        padding: 5px 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 12px;
    }
    
    .column-card .criteria-value-input {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 3px;
        font-size: 12px;
        margin-top: 8px;
    }
    
    .column-card .criteria-value-input::placeholder {
        color: #bbb;
    }
    
    .add-column-btn {
        padding: 10px 20px;
        background: linear-gradient(135deg, #1ab394 0%, #23c6c8 100%);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .add-column-btn:hover {
        background: linear-gradient(135deg, #18a689 0%, #1fc0c2 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(26, 179, 148, 0.4);
    }
    
    .sql-preview {
        background: #2f3640;
        color: #f5f6fa;
        padding: 20px;
        border-radius: 6px;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 13px;
        line-height: 1.6;
        min-height: 120px;
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-x: auto;
        position: relative;
    }
    
    .sql-preview .line-number {
        color: #718093;
        margin-right: 15px;
        user-select: none;
    }
    
    .sql-preview .keyword {
        color: #74b9ff;
        font-weight: bold;
    }
    
    .sql-preview .string {
        color: #55efc4;
    }
    
    .sql-preview .table-name {
        color: #fdcb6e;
    }
    
    .sql-preview .method {
        color: #a29bfe;
        font-weight: bold;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
        margin: 20px 0;
    }
    
    .action-buttons .btn {
        padding: 10px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }
    
    .results-container {
        margin-top: 20px;
    }
    
    .results-info {
        padding: 10px 15px;
        background: #e8f4f8;
        border-radius: 4px;
        margin-bottom: 15px;
        font-size: 14px;
        color: #2c3e50;
    }
    
    .results-info strong {
        color: #1ab394;
    }
    
    /* Empty state styling */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }
    
    .empty-state i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #ccc;
    }
    
    /* Toggle mode tabs */
    .mode-toggle {
        display: flex;
        gap: 0;
        margin-bottom: 20px;
    }
    
    .mode-toggle .tab-btn {
        padding: 12px 25px;
        background: #f8f9fa;
        border: 1px solid #e5e6e7;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s;
        color: #666;
    }
    
    .mode-toggle .tab-btn:first-child {
        border-radius: 4px 0 0 4px;
    }
    
    .mode-toggle .tab-btn:last-child {
        border-radius: 0 4px 4px 0;
        border-left: none;
    }
    
    .mode-toggle .tab-btn.active {
        background: #1ab394;
        color: white;
        border-color: #1ab394;
    }
    
    .mode-toggle .tab-btn:hover:not(.active) {
        background: #eaeaea;
    }
    
    .builder-mode, .raw-mode {
        display: none;
    }
    
    .builder-mode.active, .raw-mode.active {
        display: block;
    }
    
    .raw-sql-input {
        width: 100%;
        min-height: 150px;
        padding: 15px;
        font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
        font-size: 13px;
        border: 1px solid #e5e6e7;
        border-radius: 4px;
        resize: vertical;
    }
    
    /* JOIN Section Styles */
    .join-section {
        background: #f8f9fa;
        border: 1px solid #e5e6e7;
        border-radius: 6px;
        padding: 20px;
        margin: 20px 0;
    }
    
    .join-section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e5e6e7;
    }
    
    .join-section-header h5 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #2c3e50;
    }
    
    .join-section-header h5 i {
        color: #3498db;
        margin-right: 8px;
    }
    
    .add-join-btn {
        padding: 8px 16px;
        background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .add-join-btn:hover {
        background: linear-gradient(135deg, #2980b9 0%, #1f6fa7 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.4);
    }
    
    .joins-container {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    
    .join-card {
        background: white;
        border: 1px solid #ddd;
        border-left: 4px solid #3498db;
        border-radius: 4px;
        padding: 15px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 12px;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .join-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-left-color: #2980b9;
    }
    
    .join-card .remove-join-btn {
        position: absolute;
        top: 8px;
        right: 8px;
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 16px;
        padding: 4px 8px;
        transition: color 0.2s;
    }
    
    .join-card .remove-join-btn:hover {
        color: #ed5565;
    }
    
    .join-card .join-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }
    
    .join-card .join-field label {
        font-size: 11px;
        font-weight: 600;
        color: #666;
        text-transform: uppercase;
    }
    
    .join-card .join-field select,
    .join-card .join-field input {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 13px;
        min-width: 140px;
    }
    
    .join-card .join-field select:focus,
    .join-card .join-field input:focus {
        border-color: #3498db;
        outline: none;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .join-card .join-on-separator {
        font-weight: bold;
        color: #666;
        font-size: 14px;
        padding: 0 5px;
    }
    
    .join-empty-state {
        text-align: center;
        padding: 30px;
        color: #999;
        font-size: 14px;
    }
    
    .join-empty-state i {
        font-size: 32px;
        margin-bottom: 10px;
        color: #ccc;
        display: block;
    }</style>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><i class="fa fa-search"></i> Query Builder</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                    </div>
                </div>
                <div class="ibox-content query-builder-container">
                    
                    <!-- Mode Toggle -->
                    <div class="mode-toggle">
                        <button type="button" class="tab-btn active" data-mode="builder">
                            <i class="fa fa-cubes"></i> Visual Builder
                        </button>
                        <button type="button" class="tab-btn" data-mode="raw">
                            <i class="fa fa-code"></i> Raw SQL
                        </button>
                    </div>
                    
                    <!-- Visual Builder Mode -->
                    <div class="builder-mode active">
                        <!-- Column Cards Container -->
                        <div class="column-cards-container" id="columnCardsContainer">
                            <!-- Column cards will be added here dynamically -->
                        </div>
                        
                        <!-- Add Column Button -->
                        <button type="button" class="add-column-btn" id="addColumnBtn">
                            <i class="fa fa-plus"></i> Add column
                        </button>
                        
                        <!-- JOIN Section -->
                        <div class="join-section">
                            <div class="join-section-header">
                                <h5><i class="fa fa-link"></i> JOIN Tables</h5>
                                <button type="button" class="add-join-btn" id="addJoinBtn">
                                    <i class="fa fa-plus"></i> Add JOIN
                                </button>
                            </div>
                            <div class="joins-container" id="joinsContainer">
                                <div class="join-empty-state" id="joinEmptyState">
                                    <i class="fa fa-chain-broken"></i>
                                    No table joins configured. Click "Add JOIN" to create a relationship between tables.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button type="button" class="btn btn-primary" id="previewQueryBtn">
                                <i class="fa fa-eye"></i> Preview SQL
                            </button>
                            <button type="button" class="btn btn-success" id="executeQueryBtn">
                                <i class="fa fa-play"></i> Execute Query
                            </button>
                            <button type="button" class="btn btn-warning" id="clearAllBtn">
                                <i class="fa fa-refresh"></i> Clear All
                            </button>
                        </div>
                    </div>

                    <!-- Raw SQL Mode -->
                    <div class="raw-mode">
                        <div class="form-group">
                            <label><strong>Enter SQL Query:</strong></label>
                            <textarea class="raw-sql-input" id="rawSqlInput" placeholder="SELECT * FROM your_table WHERE condition..."></textarea>
                            <small class="text-muted">Only SELECT queries are allowed for security.</small>
                        </div>
                        
                        <!-- Action Buttons for Raw Mode -->
                        <div class="action-buttons">
                            <button type="button" class="btn btn-success" id="executeRawQueryBtn">
                                <i class="fa fa-play"></i> Execute Raw Query
                            </button>
                            <button type="button" class="btn btn-default" id="clearRawBtn">
                                <i class="fa fa-eraser"></i> Clear
                            </button>
                        </div>
                    </div>
                    
                    <!-- SQL Preview Row -->
                    <div class="row" style="margin-top: 20px;">
                        <!-- SQL Preview -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-database"></i> SQL Query</h5>
                                </div>
                                <div class="ibox-content p-0">
                                    <div class="sql-preview" id="sqlPreview">
                                        <span class="text-muted">-- SQL query will appear here...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- CI3 Query Builder Syntax -->
                        <div class="col-lg-6">
                            <div class="ibox">
                                <div class="ibox-title">
                                    <h5><i class="fa fa-code"></i> CodeIgniter 3 Query Builder</h5>
                                </div>
                                <div class="ibox-content p-0">
                                    <div class="sql-preview ci3-preview" id="ci3Preview">
                                        <span class="text-muted">// CI3 Query Builder code will appear here...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Results Container -->
                    <div class="results-container" id="resultsContainer" style="display: none;">
                        <div class="ibox">
                            <div class="ibox-title">
                                <h5><i class="fa fa-table"></i> Query Results</h5>
                            </div>
                            <div class="ibox-content">
                                <div class="results-info" id="resultsInfo"></div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover" id="resultsTable">
                                        <thead id="resultsHead"></thead>
                                        <tbody id="resultsBody"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Column Card Template -->
<template id="columnCardTemplate">
    <div class="column-card" data-index="">
        <!-- Remove Button -->
        <button type="button" class="remove-btn" title="Remove column">&times;</button>
        
        <!-- Table and Column Selectors -->
        <div class="selectors">
            <select class="table-select" name="table">
                <option value="">select table</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?= $table ?>"><?= $table ?></option>
                <?php endforeach; ?>
            </select>
            <select class="column-select" name="column">
                <option value="">select column</option>
            </select>
        </div>
        
        <!-- Show Checkbox -->
        <div class="show-checkbox">
            <label>
                <input type="checkbox" name="show" checked> Show
            </label>
        </div>
        
        <!-- Alias Inputs -->
        <div class="alias-inputs">
            <input type="text" name="table_alias" placeholder="Table alias">
            <input type="text" name="column_alias" placeholder="Column alias">
        </div>
        
        <!-- Criteria Section -->
        <div class="criteria-section">
            <div class="criteria-toggle">
                <input type="checkbox" name="criteria_enabled" id="">
                <label>criteria</label>
            </div>
            
            <div class="criteria-options">
                <!-- Sort Options -->
                <div class="sort-options">
                    <strong style="font-size: 12px;">Sort:</strong>
                    <label>
                        <input type="radio" name="" value="asc"> Ascending
                    </label>
                    <label>
                        <input type="radio" name="" value="desc"> Descending
                    </label>
                </div>
                
                <!-- Criteria Column -->
                <div class="criteria-row">
                    <label>Column</label>
                    <select name="criteria_column" class="criteria-column-select">
                        <option value="">--</option>
                    </select>
                </div>
                
                <!-- Operator -->
                <div class="criteria-row">
                    <label>Op</label>
                    <select name="operator">
                        <option value="=">=</option>
                        <option value="!=">!=</option>
                        <option value="<"><</option>
                        <option value=">">></option>
                        <option value="<="><=</option>
                        <option value=">=">>=</option>
                        <option value="LIKE">LIKE</option>
                        <option value="NOT LIKE">NOT LIKE</option>
                        <option value="IN">IN</option>
                        <option value="NOT IN">NOT IN</option>
                        <option value="IS NULL">IS NULL</option>
                        <option value="IS NOT NULL">IS NOT NULL</option>
                    </select>
                    <select name="value_type">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                    </select>
                </div>
                
                <!-- Criteria Value -->
                <input type="text" class="criteria-value-input" name="criteria_value" placeholder="Enter criteria as free text">
            </div>
        </div>
    </div>
</template>

<!-- JOIN Card Template -->
<template id="joinCardTemplate">
    <div class="join-card" data-join-index="">
        <button type="button" class="remove-join-btn" title="Remove JOIN">&times;</button>
        
        <div class="join-field">
            <label>Join Type</label>
            <select name="join_type">
                <option value="INNER JOIN">INNER JOIN</option>
                <option value="LEFT JOIN">LEFT JOIN</option>
                <option value="RIGHT JOIN">RIGHT JOIN</option>
                <option value="LEFT OUTER JOIN">LEFT OUTER JOIN</option>
                <option value="RIGHT OUTER JOIN">RIGHT OUTER JOIN</option>
                <option value="CROSS JOIN">CROSS JOIN</option>
            </select>
        </div>
        
        <div class="join-field">
            <label>Table to Join</label>
            <select name="join_table" class="join-table-select">
                <option value="">select table</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?= $table ?>"><?= $table ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="join-field">
            <label>Alias (optional)</label>
            <input type="text" name="join_alias" placeholder="alias">
        </div>
        
        <span class="join-on-separator">ON</span>
        
        <div class="join-field">
            <label>Left Table.Column</label>
            <select name="on_left_table" class="on-left-table-select">
                <option value="">select table</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?= $table ?>"><?= $table ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="join-field">
            <label>&nbsp;</label>
            <select name="on_left_column" class="on-left-column-select">
                <option value="">select column</option>
            </select>
        </div>
        
        <span class="join-on-separator">=</span>
        
        <div class="join-field">
            <label>Right Table.Column</label>
            <select name="on_right_table" class="on-right-table-select">
                <option value="">select table</option>
                <?php foreach ($tables as $table): ?>
                    <option value="<?= $table ?>"><?= $table ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="join-field">
            <label>&nbsp;</label>
            <select name="on_right_column" class="on-right-column-select">
                <option value="">select column</option>
            </select>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const BASE_URL = '<?= base_url() ?>';
    let columnIndex = 0;
    let joinIndex = 0;
    
    // CSRF Protection
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    
    function updateCsrf(newHash) {
        if(!newHash) return;
        csrfHash = newHash;
    }
    
    // Mode Toggle
    document.querySelectorAll('.mode-toggle .tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.mode-toggle .tab-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const mode = this.dataset.mode;
            document.querySelector('.builder-mode').classList.toggle('active', mode === 'builder');
            document.querySelector('.raw-mode').classList.toggle('active', mode === 'raw');
        });
    });
    
    // Add initial column card
    addColumnCard();
    
    // Add Column Button
    document.getElementById('addColumnBtn').addEventListener('click', addColumnCard);
    
    // Add JOIN Button
    document.getElementById('addJoinBtn').addEventListener('click', addJoinCard);
    
    // Clear All Button
    document.getElementById('clearAllBtn').addEventListener('click', function() {
        Swal.fire({
            title: 'Clear All?',
            text: 'This will remove all column cards and joins.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, clear all'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('columnCardsContainer').innerHTML = '';
                columnIndex = 0;
                addColumnCard();
                
                // Clear joins
                document.getElementById('joinsContainer').innerHTML = '<div class="join-empty-state" id="joinEmptyState"><i class="fa fa-chain-broken"></i>No table joins configured. Click "Add JOIN" to create a relationship between tables.</div>';
                joinIndex = 0;
                
                document.getElementById('sqlPreview').innerHTML = '<span class="text-muted">-- SQL query will appear here...</span>';
                document.getElementById('resultsContainer').style.display = 'none';
            }
        });
    });
    
    // Clear Raw SQL
    document.getElementById('clearRawBtn').addEventListener('click', function() {
        document.getElementById('rawSqlInput').value = '';
        document.getElementById('sqlPreview').innerHTML = '<span class="text-muted">-- SQL query will appear here...</span>';
        document.getElementById('ci3Preview').innerHTML = '<span class="text-muted">// CI3 Query Builder code will appear here...</span>';
        document.getElementById('resultsContainer').style.display = 'none';
    });
    
    // Preview Query Button
    document.getElementById('previewQueryBtn').addEventListener('click', function() {
        const columns = getColumnsData();
        const joins = getJoinsData();
        previewQuery(columns, joins);
    });
    
    // Execute Query Button
    document.getElementById('executeQueryBtn').addEventListener('click', function() {
        const columns = getColumnsData();
        const joins = getJoinsData();
        executeQuery(columns, joins);
    });
    
    // Execute Raw Query Button
    document.getElementById('executeRawQueryBtn').addEventListener('click', function() {
        const sql = document.getElementById('rawSqlInput').value.trim();
        if (!sql) {
            Swal.fire('Error', 'Please enter a SQL query', 'error');
            return;
        }
        executeRawQuery(sql);
    });
    
    // Function to add a new column card
    function addColumnCard() {
        const template = document.getElementById('columnCardTemplate');
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.column-card');
        
        columnIndex++;
        card.dataset.index = columnIndex;
        
        // Set unique IDs for radio buttons
        const sortRadios = card.querySelectorAll('.sort-options input[type="radio"]');
        sortRadios.forEach(radio => {
            radio.name = 'sort_' + columnIndex;
        });
        
        // Set unique ID for criteria checkbox
        const criteriaCheckbox = card.querySelector('.criteria-toggle input');
        criteriaCheckbox.id = 'criteria_' + columnIndex;
        card.querySelector('.criteria-toggle label').setAttribute('for', 'criteria_' + columnIndex);
        
        // Event: Remove button
        card.querySelector('.remove-btn').addEventListener('click', function() {
            const container = document.getElementById('columnCardsContainer');
            if (container.children.length > 1) {
                card.remove();
            } else {
                Swal.fire('Info', 'At least one column card is required.', 'info');
            }
        });
        
        // Event: Table select change
        card.querySelector('.table-select').addEventListener('change', function() {
            const table = this.value;
            const columnSelect = card.querySelector('.column-select');
            const criteriaColumnSelect = card.querySelector('.criteria-column-select');
            
            if (table) {
                loadColumns(table, columnSelect, criteriaColumnSelect);
            } else {
                columnSelect.innerHTML = '<option value="">select column</option>';
                criteriaColumnSelect.innerHTML = '<option value="">--</option>';
            }
        });
        
        // Event: Criteria toggle
        criteriaCheckbox.addEventListener('change', function() {
            const options = card.querySelector('.criteria-options');
            options.classList.toggle('active', this.checked);
        });
        
        document.getElementById('columnCardsContainer').appendChild(card);
    }
    
    // Function to load columns for a table
    function loadColumns(table, columnSelect, criteriaColumnSelect) {
        fetch(BASE_URL + 'QueryBuilder/get_columns/' + encodeURIComponent(table))
            .then(response => response.json())
            .then(data => {
                if (data.csrfHash) updateCsrf(data.csrfHash);
                
                if (data.status) {
                    let optionsHtml = '<option value="">select column</option>';
                    optionsHtml += '<option value="*">* (All)</option>';
                    
                    let criteriaOptionsHtml = '<option value="">--</option>';
                    
                    data.data.forEach(col => {
                        optionsHtml += `<option value="${col.name}">${col.name} (${col.type})</option>`;
                        criteriaOptionsHtml += `<option value="${col.name}">${col.name}</option>`;
                    });
                    
                    columnSelect.innerHTML = optionsHtml;
                    criteriaColumnSelect.innerHTML = criteriaOptionsHtml;
                } else {
                    Swal.fire('Error', data.message || 'Failed to load columns', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading columns:', error);
                Swal.fire('Error', 'Failed to load columns', 'error');
            });
    }
    
    // Function to get all columns data
    function getColumnsData() {
        const cards = document.querySelectorAll('.column-card');
        const columns = [];
        
        cards.forEach(card => {
            const table = card.querySelector('.table-select').value;
            const column = card.querySelector('.column-select').value;
            
            if (!table) return;
            
            const sortRadio = card.querySelector('.sort-options input[type="radio"]:checked');
            
            columns.push({
                table: table,
                column: column || '*',
                table_alias: card.querySelector('input[name="table_alias"]').value,
                column_alias: card.querySelector('input[name="column_alias"]').value,
                show: card.querySelector('input[name="show"]').checked,
                criteria: {
                    enabled: card.querySelector('.criteria-toggle input').checked,
                    sort: sortRadio ? sortRadio.value : 'none',
                    column: card.querySelector('.criteria-column-select').value,
                    operator: card.querySelector('select[name="operator"]').value,
                    type: card.querySelector('select[name="value_type"]').value,
                    value: card.querySelector('.criteria-value-input').value
                }
            });
        });
        
        return columns;
    }
    
    // Function to add a new JOIN card
    function addJoinCard() {
        const template = document.getElementById('joinCardTemplate');
        const clone = template.content.cloneNode(true);
        const card = clone.querySelector('.join-card');
        
        joinIndex++;
        card.dataset.joinIndex = joinIndex;
        
        // Hide empty state
        const emptyState = document.getElementById('joinEmptyState');
        if (emptyState) {
            emptyState.style.display = 'none';
        }
        
        // Event: Remove JOIN button
        card.querySelector('.remove-join-btn').addEventListener('click', function() {
            card.remove();
            // Show empty state if no more joins
            const remainingJoins = document.querySelectorAll('.join-card');
            if (remainingJoins.length === 0) {
                const emptyState = document.getElementById('joinEmptyState');
                if (emptyState) {
                    emptyState.style.display = 'block';
                }
            }
        });
        
        // Event: Left table select change - load columns
        card.querySelector('.on-left-table-select').addEventListener('change', function() {
            const table = this.value;
            const columnSelect = card.querySelector('.on-left-column-select');
            if (table) {
                loadJoinColumns(table, columnSelect);
            } else {
                columnSelect.innerHTML = '<option value="">select column</option>';
            }
        });
        
        // Event: Right table select change - load columns
        card.querySelector('.on-right-table-select').addEventListener('change', function() {
            const table = this.value;
            const columnSelect = card.querySelector('.on-right-column-select');
            if (table) {
                loadJoinColumns(table, columnSelect);
            } else {
                columnSelect.innerHTML = '<option value="">select column</option>';
            }
        });
        
        // Event: Join table select change - auto-select in right table
        card.querySelector('.join-table-select').addEventListener('change', function() {
            const table = this.value;
            const rightTableSelect = card.querySelector('.on-right-table-select');
            if (table && rightTableSelect) {
                rightTableSelect.value = table;
                // Trigger change event to load columns
                rightTableSelect.dispatchEvent(new Event('change'));
            }
        });
        
        document.getElementById('joinsContainer').appendChild(card);
    }
    
    // Function to load columns for JOIN ON condition
    function loadJoinColumns(table, columnSelect) {
        fetch(BASE_URL + 'QueryBuilder/get_columns/' + encodeURIComponent(table))
            .then(response => response.json())
            .then(data => {
                if (data.csrfHash) updateCsrf(data.csrfHash);

                if (data.status) {
                    let optionsHtml = '<option value="">select column</option>';
                    
                    data.data.forEach(col => {
                        optionsHtml += `<option value="${col.name}">${col.name}</option>`;
                    });
                    
                    columnSelect.innerHTML = optionsHtml;
                }
            })
            .catch(error => {
                console.error('Error loading columns:', error);
            });
    }
    
    // Function to get all JOINs data
    function getJoinsData() {
        const cards = document.querySelectorAll('.join-card');
        const joins = [];
        
        cards.forEach(card => {
            const joinTable = card.querySelector('.join-table-select').value;
            const joinType = card.querySelector('select[name="join_type"]').value;
            
            if (!joinTable) return;
            
            const onLeftTable = card.querySelector('.on-left-table-select').value;
            const onLeftColumn = card.querySelector('.on-left-column-select').value;
            const onRightTable = card.querySelector('.on-right-table-select').value;
            const onRightColumn = card.querySelector('.on-right-column-select').value;
            
            // Build ON condition references
            let onLeft = '';
            let onRight = '';
            
            if (onLeftTable && onLeftColumn) {
                onLeft = '`' + onLeftTable + '`.`' + onLeftColumn + '`';
            }
            
            if (onRightTable && onRightColumn) {
                onRight = '`' + onRightTable + '`.`' + onRightColumn + '`';
            }
            
            joins.push({
                type: joinType,
                table: joinTable,
                alias: card.querySelector('input[name="join_alias"]').value,
                on_left: onLeft,
                on_right: onRight
            });
        });
        
        return joins;
    }
    
    // Function to preview query
    function previewQuery(columns, joins) {
        if (columns.length === 0) {
            Swal.fire('Warning', 'Please select at least one table', 'warning');
            return;
        }
        
        let bodyData = 'columns=' + encodeURIComponent(JSON.stringify(columns));
        if (joins && joins.length > 0) {
            bodyData += '&joins=' + encodeURIComponent(JSON.stringify(joins));
        }
        bodyData += '&' + csrfName + '=' + csrfHash;
        
        fetch(BASE_URL + 'QueryBuilder/preview_query', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: bodyData
        })
        .then(response => response.json())
        .then(data => {
            if (data.csrfHash) updateCsrf(data.csrfHash);
            
            if (data.status) {
                displaySQL(data.sql);
                displayCI3Syntax(data.ci_syntax);
            } else {
                Swal.fire('Error', data.message || 'Failed to generate preview', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Failed to generate preview', 'error');
        });
    }
    
    // Function to execute query
    function executeQuery(columns, joins) {
        if (columns.length === 0) {
            Swal.fire('Warning', 'Please select at least one table', 'warning');
            return;
        }
        
        // Show loading
        Swal.fire({
            title: 'Executing Query...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        let bodyData = 'columns=' + encodeURIComponent(JSON.stringify(columns));
        if (joins && joins.length > 0) {
            bodyData += '&joins=' + encodeURIComponent(JSON.stringify(joins));
        }
        bodyData += '&' + csrfName + '=' + csrfHash;

        fetch(BASE_URL + 'QueryBuilder/execute_query', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: bodyData
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.csrfHash) updateCsrf(data.csrfHash);
            
            if (data.status) {
                displaySQL(data.sql);
                displayResults(data);
            } else {
                displaySQL(data.sql || '');
                Swal.fire('Error', data.message || 'Query execution failed', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Query execution failed', 'error');
        });
    }
    
    // Function to execute raw query
    function executeRawQuery(sql) {
        // Show loading
        Swal.fire({
            title: 'Executing Query...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        fetch(BASE_URL + 'QueryBuilder/execute_raw_query', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'sql=' + encodeURIComponent(sql) + '&' + csrfName + '=' + csrfHash
        })
        .then(response => response.json())
        .then(data => {
            Swal.close();
            if (data.csrfHash) updateCsrf(data.csrfHash);
            
            if (data.status) {
                displaySQL(data.sql);
                displayCI3Syntax(data.ci_syntax);
                displayResults(data);
            } else {
                displaySQL(data.sql || '');
                displayCI3Syntax('');
                Swal.fire('Error', data.message || 'Query execution failed', 'error');
            }
        })
        .catch(error => {
            Swal.close();
            console.error('Error:', error);
            Swal.fire('Error', 'Query execution failed', 'error');
        });
    }
    
    // Function to display SQL with syntax highlighting
    function displaySQL(sql) {
        if (!sql) {
            document.getElementById('sqlPreview').innerHTML = '<span class="text-muted">-- SQL query will appear here...</span>';
            return;
        }
        
        // Simple syntax highlighting
        let highlighted = sql
            .replace(/\b(SELECT|FROM|WHERE|AND|OR|ORDER BY|GROUP BY|HAVING|JOIN|LEFT JOIN|RIGHT JOIN|INNER JOIN|ON|AS|LIMIT|OFFSET|ASC|DESC|IN|NOT IN|LIKE|NOT LIKE|IS NULL|IS NOT NULL|BETWEEN)\b/gi, '<span class="keyword">$1</span>')
            .replace(/`([^`]+)`/g, '<span class="table-name">`$1`</span>')
            .replace(/'([^']+)'/g, '<span class="string">\'$1\'</span>');
        
        // Add line numbers
        const lines = highlighted.split('\n');
        let numberedLines = lines.map((line, index) => {
            return `<span class="line-number">${String(index + 1).padStart(2, ' ')}</span>` + line;
        }).join('\n');
        
        document.getElementById('sqlPreview').innerHTML = numberedLines;
    }
    
    // Function to display CI3 Query Builder syntax with highlighting
    function displayCI3Syntax(code) {
        if (!code) {
            document.getElementById('ci3Preview').innerHTML = '<span class="text-muted">// CI3 Query Builder code will appear here...</span>';
            return;
        }
        
        // PHP syntax highlighting
        let highlighted = code
            .replace(/\/\/([^\n]*)/g, '<span class="string">// $1</span>')  // Comments
            .replace(/\$this->db/g, '<span class="keyword">$this</span>-><span class="table-name">db</span>')
            .replace(/\$query/g, '<span class="keyword">$query</span>')
            .replace(/\$result/g, '<span class="keyword">$result</span>')
            .replace(/->(select|from|join|where|where_in|where_not_in|like|not_like|or_like|or_where|order_by|group_by|having|limit|offset|get|result|row|result_array|row_array)\(/g, '-><span class="method">$1</span>(')
            .replace(/'([^']+)'/g, '<span class="string">\'$1\'</span>');
        
        // Add line numbers
        const lines = highlighted.split('\n');
        let numberedLines = lines.map((line, index) => {
            return `<span class="line-number">${String(index + 1).padStart(2, ' ')}</span>` + line;
        }).join('\n');
        
        document.getElementById('ci3Preview').innerHTML = numberedLines;
    }
    
    // Function to display results
    function displayResults(data) {
        const container = document.getElementById('resultsContainer');
        const info = document.getElementById('resultsInfo');
        const thead = document.getElementById('resultsHead');
        const tbody = document.getElementById('resultsBody');
        
        // Show results container
        container.style.display = 'block';
        
        // Update info
        info.innerHTML = `<strong>${data.total_rows}</strong> row(s) returned`;
        
        // Build table header
        let headerHtml = '<tr>';
        data.fields.forEach(field => {
            headerHtml += `<th>${escapeHtml(field)}</th>`;
        });
        headerHtml += '</tr>';
        thead.innerHTML = headerHtml;
        
        // Build table body
        let bodyHtml = '';
        if (data.data.length === 0) {
            bodyHtml = `<tr><td colspan="${data.fields.length}" class="text-center text-muted">No results found</td></tr>`;
        } else {
            data.data.forEach(row => {
                bodyHtml += '<tr>';
                data.fields.forEach(field => {
                    const value = row[field];
                    bodyHtml += `<td>${value === null ? '<span class="text-muted">NULL</span>' : escapeHtml(String(value))}</td>`;
                });
                bodyHtml += '</tr>';
            });
        }
        tbody.innerHTML = bodyHtml;
        
        // Scroll to results
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    
    // Helper function to escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});
</script>
