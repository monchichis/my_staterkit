<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?= $title ?></h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    
    <form id="chartForm" action="<?= site_url('superadmin/chart_generator/save') ?>" method="post">
        <input type="hidden" name="id" value="<?= isset($chart) ? $chart->id : '' ?>">
        <!-- CSRF Token -->
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" id="csrf_token_input" value="<?= $this->security->get_csrf_hash(); ?>">
        
        <div class="row">
            <!-- Left Column: Settings -->
            <div class="col-lg-5">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Configuration</h5>
                    </div>
                    <div class="ibox-content">
                        <div class="form-group">
                            <label>Chart Title</label>
                            <input type="text" name="chart_title" class="form-control" required value="<?= isset($chart) ? $chart->chart_title : '' ?>">
                        </div>
                        
                        <div class="form-group">
                            <label>Chart Type</label>
                            <select name="chart_type" class="form-control" id="chartType" required>
                                <?php 
                                $types = ['column', 'bar', 'line', 'area', 'pie', 'spline', 'polar'];
                                $current_type = isset($chart) ? $chart->chart_type : 'column';
                                foreach($types as $t): 
                                ?>
                                    <option value="<?= $t ?>" <?= $current_type == $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Placement</label>
                            <select name="placement_identifier" class="form-control">
                                <option value="dashboard" <?= (isset($chart) && $chart->placement_identifier == 'dashboard') ? 'selected' : '' ?>>Dashboard Main</option>
                                <option value="report_page" <?= (isset($chart) && $chart->placement_identifier == 'report_page') ? 'selected' : '' ?>>Report Page</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Visible to Roles</label>
                            <select name="allowed_roles[]" class="form-control" multiple required style="height: 100px">
                                <?php 
                                $selected_roles = isset($chart) ? json_decode($chart->allowed_roles) : [];
                                if(!is_array($selected_roles)) $selected_roles = [];
                                foreach($roles as $r): 
                                ?>
                                    <option value="<?= $r->id_role ?>" <?= in_array($r->id_role, $selected_roles) ? 'selected' : '' ?>>
                                        <?= $r->role_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="help-block m-b-none text-muted">Hold Ctrl to select multiple</span>
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="is_active" value="1" <?= (!isset($chart) || $chart->is_active) ? 'checked' : '' ?>> Active
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Series Data</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-primary btn-xs" id="addSeriesBtn"><i class="fa fa-plus"></i> Add Series</button>
                        </div>
                    </div>
                    <div class="ibox-content" id="seriesContainer">
                        <!-- Series items will be injected here -->
                    </div>
                </div>
                
                <button type="button" class="btn btn-info btn-block" id="previewBtn"><i class="fa fa-eye"></i> Generate Preview</button>
                <br>
                
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Advanced Filter Configuration</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-warning btn-xs" id="addFilterBtn"><i class="fa fa-plus"></i> Add Filter</button>
                        </div>
                    </div>
                    <div class="ibox-content" id="filterConfigContainer">
                         <div class="text-muted text-center" id="noFiltersMsg" style="padding: 10px;">
                            No filters configured. <br>Click "Add Filter" to allow users to filter data.
                         </div>
                        <!-- Filters injected here -->
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-save"></i> Save Chart</button>
            </div>
            
            <!-- Right Column: Preview -->
            <div class="col-lg-7">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Live Preview</h5>
                        <div class="ibox-tools">
                            <button type="button" class="btn btn-xs btn-primary" id="openAdvFilterBtn" style="display:none;"><i class="fa fa-filter"></i> Advanced Menu</button>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div id="chartPreview" style="height: 400px; border: 1px dashed #ccc; display: flex; align-items: center; justify-content: center; color: #999;">
                            Click "Generate Preview" to see the chart
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
<!-- Hidden input to store JSON -->
        <textarea name="data_config" id="dataConfigInput" style="display:none;"></textarea>
    </form>
</div>

<!-- Advanced Filter Modal -->
<div class="modal fade" id="advancedFilterModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Advanced Data Filters</h4>
                <small class="font-bold">Select a column and apply filters.</small>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Filter By Column</label>
                    <select class="form-control" id="advFilterColumn">
                        <option value="">-- Select Column --</option>
                    </select>
                </div>
                
                <div id="advFilterInputContainer">
                    <div class="text-center text-muted">Select a column first</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyAdvFilterBtn">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Highcharts -->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>

<script>
// Define CSRF Token variables correctly
var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

// Wait for jQuery to be loaded (it's in footer.php)
function initChartGenerator() {
    let seriesCount = 0;
    let filterCount = 0;
    const availableTables = <?= json_encode($tables) ?>;
    const rawConfig = <?= isset($chart) && $chart->data_config ? $chart->data_config : 'null' ?>;
    
    let existingSeries = [];
    let existingFilters = [];

    // Parse Config (Handle Legacy Array vs New Object)
    if (rawConfig) {
        if (Array.isArray(rawConfig)) {
            existingSeries = rawConfig;
        } else {
            existingSeries = rawConfig.series || [];
            existingFilters = rawConfig.filters || [];
        }
    }

    // Cache for columns to avoid re-fetching
    let tableColumnsCache = {};
    let runtimeFilters = []; // Active filters for preview

    // Template for Series Row
    function createSeriesRow(data = null) {
        seriesCount++;
        const id = seriesCount;
        
        let html = `
        <div class="series-item panel panel-default" id="series-${id}">
            <div class="panel-heading">
                Series #${id}
                <button type="button" class="close pull-right remove-series" data-id="${id}">&times;</button>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label>Data Source (Table)</label>
                    <select class="form-control table-select" data-id="${id}">
                        <option value="">-- Select Table --</option>
                        ${availableTables.map(t => `<option value="${t}" ${data && data.table == t ? 'selected' : ''}>${t}</option>`).join('')}
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Series Name</label>
                    <input type="text" class="form-control series-name" value="${data ? data.name : 'Series ' + id}">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>X-Axis (Group By)</label>
                            <select class="form-control x-axis-select">
                                ${data ? `<option value="${data.x_axis}" selected>${data.x_axis}</option>` : '<option value="">Select Table First</option>'}
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="form-group">
                            <label>Value Column</label>
                            <select class="form-control y-axis-select">
                                ${data ? `<option value="${data.y_axis}" selected>${data.y_axis}</option>` : '<option value="">Select Table First</option>'}
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                     <div class="col-md-6">
                        <div class="form-group">
                            <label>Aggregate Type</label>
                            <select class="form-control agg-select">
                                <option value="SUM" ${data && data.aggregate == 'SUM' ? 'selected' : ''}>Sum</option>
                                <option value="COUNT" ${data && data.aggregate == 'COUNT' ? 'selected' : ''}>Count</option>
                                <option value="AVG" ${data && data.aggregate == 'AVG' ? 'selected' : ''}>Average</option>
                                <option value="MAX" ${data && data.aggregate == 'MAX' ? 'selected' : ''}>Max</option>
                                <option value="MIN" ${data && data.aggregate == 'MIN' ? 'selected' : ''}>Min</option>
                            </select>
                        </div>
                    </div>
                     <div class="col-md-6">
                        <div class="form-group">
                            <label>Color (Hex)</label>
                            <input type="color" class="form-control color-select" value="${data ? (data.color || '#7cb5ec') : '#7cb5ec'}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        `;
        
        $('#seriesContainer').append(html);
        
        // If restoring data, trigger column load
        if (data) {
            loadColumns(id, data.table, data.x_axis, data.y_axis);
        }
    }

    // Template for Filter Row
    function createFilterRow(data = null) {
        filterCount++;
        const fid = filterCount;
        
        let html = `
        <div class="filter-item form-group row" id="filter-${fid}" style="margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <div class="col-sm-5">
                <label>Column</label>
                <select class="form-control filter-column-select" required data-selected="${data ? data.column : ''}">
                    <option value="">-- Select Column --</option>
                    ${data ? `<option value="${data.column}" selected>${data.column}</option>` : ''}
                </select>
            </div>
            <div class="col-sm-4">
                <label>Filter Type</label>
                 <select class="form-control filter-type-select">
                    <option value="text" ${data && data.type == 'text' ? 'selected' : ''}>Text / Exact</option>
                    <option value="date" ${data && data.type == 'date' ? 'selected' : ''}>Date (YYYY-MM-DD)</option>
                    <option value="month_year" ${data && data.type == 'month_year' ? 'selected' : ''}>Month & Year</option>
                    <option value="year" ${data && data.type == 'year' ? 'selected' : ''}>Year Only</option>
                 </select>
            </div>
            <div class="col-sm-2">
                <label>Label</label>
                <input type="text" class="form-control filter-label" placeholder="Label" value="${data ? (data.label||'') : ''}">
            </div>
            <div class="col-sm-1">
                <label>&nbsp;</label>
                <button type="button" class="btn btn-danger btn-block remove-filter" data-id="${fid}"><i class="fa fa-times"></i></button>
            </div>
        </div>`;
        
        $('#filterConfigContainer').append(html);
        $('#noFiltersMsg').hide();
        
        // Populate with currently available columns
        updateAllFilterColumnOptions();
        
        // Show Advanced Menu Button if filters exist
        checkAdvButtonVisibility();
    }
    
    // Load existing Series
    if (existingSeries.length > 0) {
        existingSeries.forEach(s => createSeriesRow(s));
    } else {
        createSeriesRow(); // One default
    }

    // Load existing Filters
    if (existingFilters.length > 0) {
        existingFilters.forEach(f => createFilterRow(f));
    }

    // Add Buttons
    $('#addSeriesBtn').click(function() {
        createSeriesRow();
    });
    
    $('#addFilterBtn').click(function() {
        createFilterRow();
    });

    // Remove buttons
    $(document).on('click', '.remove-series', function() {
        $(`#series-${$(this).data('id')}`).remove();
        updateAllFilterColumnOptions(); // Tables might have changed
    });
    
    $(document).on('click', '.remove-filter', function() {
        $(`#filter-${$(this).data('id')}`).remove();
        if ($('.filter-item').length === 0) {
            $('#noFiltersMsg').show();
        }
        checkAdvButtonVisibility();
    });

    // Load columns when table changes
    $(document).on('change', '.table-select', function() {
        const id = $(this).data('id');
        const table = $(this).val();
        loadColumns(id, table);
    });

    function loadColumns(id, table, selectedX = null, selectedY = null) {
        if (!table) return;
        
        // Check cache
        if (tableColumnsCache[table]) {
            const cols = tableColumnsCache[table];
            updateSeriesColumns(id, cols, selectedX, selectedY);
            updateAllFilterColumnOptions();
            return;
        }
        
        var data = {table: table};
        data[csrfName] = csrfHash; // Add CSRF token
        
        $.ajax({
            url: '<?= site_url('superadmin/chart_generator/get_table_columns') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.csrfHash) {
                    csrfHash = response.csrfHash;
                    $('#csrf_token_input').val(csrfHash);
                }
                
                const cols = response.columns || response;
                tableColumnsCache[table] = cols; // Cache
                
                updateSeriesColumns(id, cols, selectedX, selectedY);
                updateAllFilterColumnOptions();
            }
        });
    }

    function updateSeriesColumns(id, cols, selectedX, selectedY) {
        const xSelect = $(`#series-${id} .x-axis-select`);
        const ySelect = $(`#series-${id} .y-axis-select`);
        
        xSelect.empty();
        ySelect.empty();
        
        cols.forEach(c => {
            xSelect.append(new Option(c, c, false, c === selectedX));
            ySelect.append(new Option(c, c, false, c === selectedY));
        });
    }

    function updateAllFilterColumnOptions() {
        // Collect all columns from selected tables
        let allCols = new Set();
        $('.table-select').each(function() {
            let t = $(this).val();
            if (t && tableColumnsCache[t]) {
                tableColumnsCache[t].forEach(c => allCols.add(c));
            }
        });
        
        // Apply to all filter dropdowns
        $('.filter-column-select').each(function() {
            let currentVal = $(this).val();
            let intendedVal = $(this).data('selected');
            
            // Prefer intended val if current is empty (e.g. initialization)
            if (!currentVal && intendedVal) {
                currentVal = intendedVal;
            }

            $(this).empty().append(new Option('-- Select Column --', ''));
            
            allCols.forEach(c => {
                let isSelected = c === currentVal;
                $(this).append(new Option(c, c, false, isSelected));
            });
            
            // If we successfully set the value, we can maybe clear the data attribute?
            // Actually, keep it for safety, but updating .val() is key.
            if (currentVal && allCols.has(currentVal)) {
                 $(this).val(currentVal);
                 // Clear intended so we don't force it later if user changes it to empty? 
                 // Nah, 'currentVal' logic handles it.
                 $(this).data('selected', ''); // Clear it so it doesn't override user properties later if logic changes
            }
        });
    }
    
    function checkAdvButtonVisibility() {
        if ($('.filter-item').length > 0) {
            $('#openAdvFilterBtn').show();
        } else {
            $('#openAdvFilterBtn').hide();
        }
    }
    
    // Initial check
    checkAdvButtonVisibility();

    // Preview Logic
    $('#previewBtn').click(function() {
        const config = collectConfig();
        const type = $('#chartType').val();
        const title = $('input[name="chart_title"]').val();
        
        // Update hidden input
        $('#dataConfigInput').val(JSON.stringify(config));
        
        var postData = {
            config: JSON.stringify(config),
            filters: JSON.stringify(runtimeFilters) // Add runtime filters as JSON string
        };
        postData[csrfName] = csrfHash;

        $.ajax({
            url: '<?= site_url('superadmin/chart_generator/preview_chart') ?>',
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(response) {
                if (response.csrfHash) {
                    csrfHash = response.csrfHash;
                    $('#csrf_token_input').val(csrfHash);
                }
                renderChart(title, type, response.series, response.xAxis);
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error fetching chart data. Please check configuration.',
                    confirmButtonColor: '#d33'
                });
            }
        });
    });

    function collectConfig() {
        let series = [];
        $('.series-item').each(function() {
            const el = $(this);
            series.push({
                table: el.find('.table-select').val(),
                name: el.find('.series-name').val(),
                x_axis: el.find('.x-axis-select').val(),
                y_axis: el.find('.y-axis-select').val(),
                aggregate: el.find('.agg-select').val(),
                color: el.find('.color-select').val(),
                type: $('#chartType').val()
            });
        });
        
        let filters = [];
        $('.filter-item').each(function() {
            const el = $(this);
            const col = el.find('.filter-column-select').val();
            if (col) {
                filters.push({
                    column: col,
                    type: el.find('.filter-type-select').val(),
                    label: el.find('.filter-label').val()
                });
            }
        });
        
        return {
            series: series,
            filters: filters
        };
    }

    function renderChart(title, type, seriesData, xAxisData) {
        var chartOptions = {
            chart: {
                type: type === 'polar' ? 'line' : type,
                polar: type === 'polar'
            },
            title: {
                text: title
            },
            xAxis: xAxisData,
            yAxis: {
                title: {
                    text: 'Value'
                }
            },
            series: seriesData,
            credits: {
                enabled: false
            }
        };
        
        if (type === 'polar') {
            chartOptions.pane = {
                size: '80%'
            };
            chartOptions.xAxis.tickmarkPlacement = 'on';
            chartOptions.xAxis.lineWidth = 0;
            chartOptions.yAxis.gridLineInterpolation = 'polygon';
            chartOptions.yAxis.lineWidth = 0;
            chartOptions.yAxis.min = 0;
        }
        
        Highcharts.chart('chartPreview', chartOptions);
    }
    
    // Auto populate hidden input on submit
    $('#chartForm').submit(function() {
        $('#dataConfigInput').val(JSON.stringify(collectConfig()));
        return true;
    });

    // ---------------------------------------------------------
    // Advanced Menu Interaction
    // ---------------------------------------------------------

    $('#openAdvFilterBtn').click(function() {
         var config = collectConfig();
         var filters = config.filters || [];
         
         var $sel = $('#advFilterColumn');
         $sel.empty().append('<option value="">-- Select Column --</option>');
         
         filters.forEach(f => {
             // Basic escaping for label
             var label = f.label || f.column;
             $sel.append($('<option>', {
                 value: f.column,
                 text: label + ' (' + f.type + ')',
                 'data-type': f.type,
                 'data-label': label
             }));
         });
         
         $('#advancedFilterModal').modal('show');
    });

    $('#advFilterColumn').change(function() {
        var opt = $(this).find(':selected');
        var type = opt.data('type');
        var col = $(this).val();
        var container = $('#advFilterInputContainer');
        container.empty();
        
        if (!col) {
            container.html('<div class="text-center text-muted">Select a column first</div>');
            return;
        }

        let inputHtml = '';
        if (type == 'month_year') {
             inputHtml = '<input type="month" class="form-control" id="advFilterValue">';
        } else if (type == 'year') {
             inputHtml = '<input type="number" class="form-control" id="advFilterValue" placeholder="YYYY">';
        } else if (type == 'date') {
             inputHtml = '<input type="date" class="form-control" id="advFilterValue">';
        } else {
             inputHtml = '<input type="text" class="form-control" id="advFilterValue" placeholder="Enter value...">';
        }
        
        container.html('<div class="form-group"><label>Value</label>' + inputHtml + '</div>');
        
        // Pre-fill if exists
        var existing = runtimeFilters.find(f => f.column == col);
        if (existing) {
             $('#advFilterValue').val(existing.value);
        }
    });

    $('#applyAdvFilterBtn').click(function() {
         var col = $('#advFilterColumn').val();
         var val = $('#advFilterValue').val();
         var type = $('#advFilterColumn option:selected').data('type');
         
         if (col) {
             // Remove existing filter for this column
             runtimeFilters = runtimeFilters.filter(f => f.column !== col);
             
             if (val) {
                 runtimeFilters.push({column: col, value: val, type: type});
             }
             
             // Trigger preview
             $('#previewBtn').click();
             $('#advancedFilterModal').modal('hide');
             
             // Show notification?
             Swal.fire({
                 icon: 'success',
                 title: 'Filter Applied',
                 timer: 1500,
                 showConfirmButton: false
             });
         }
    });
}

// Wait for jQuery to load
(function checkJQuery() {
    if (typeof jQuery !== 'undefined' && typeof Swal !== 'undefined') {
        jQuery(document).ready(function() {
            initChartGenerator();
        });
    } else {
        setTimeout(checkJQuery, 50);
    }
    
})();
</script>
