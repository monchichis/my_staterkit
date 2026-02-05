<!-- Chart Renderer Partial -->
<?php if (!empty($charts)): ?>
<div class="row">
    <?php foreach ($charts as $chart): 
        $config = json_decode($chart->data_config, true);
        $has_filters = false;
        if (isset($config['filters']) && !empty($config['filters'])) {
            $has_filters = true;
        } elseif (is_array($config) && isset($config[0]['filters'])) {
            // Legacy/Edge case handling if structure varies, though 'filters' is usually root key in object now
            // My previous implementation put it as root key in object {series:..., filters:...}
            // So checking $config['filters'] is correct.
        }
    ?>
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title" data-chart-id="<?= $chart->id ?>" data-chart-type="<?= $chart->chart_type ?>" data-chart-title="<?= htmlspecialchars($chart->chart_title, ENT_QUOTES, 'UTF-8') ?>">
                    <h5><?= $chart->chart_title ?></h5>
                    <div class="ibox-tools">
                        <?php if ($has_filters): ?>
                            <a class="open-chart-filter" data-id="<?= $chart->id ?>" data-filters="<?= htmlspecialchars(json_encode($config['filters']), ENT_QUOTES, 'UTF-8') ?>">
                                <i class="fa fa-filter"></i> Advanced
                            </a>
                        <?php endif; ?>
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div id="dynamic-chart-<?= $chart->id ?>" style="height: 300px;">
                        <div class="sk-spinner sk-spinner-double-bounce">
                            <div class="sk-double-bounce1"></div>
                            <div class="sk-double-bounce2"></div>
                        </div>
                        <div class="text-center m-t-lg">Loading Chart Data...</div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Shared Filter Modal -->
<div class="modal fade" id="publicChartFilterModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title">Advanced Data Filters</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="publicFilterChartId">
                <div class="form-group">
                    <label>Filter By Column</label>
                    <select class="form-control" id="publicFilterColumn">
                        <option value="">-- Select Column --</option>
                    </select>
                </div>
                
                <div id="publicFilterInputContainer">
                    <div class="text-center text-muted">Select a column first</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="applyPublicFilterBtn">Apply Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Load Highcharts if not already loaded -->
<script>
if (typeof Highcharts === 'undefined') {
    document.write('<script src="https://code.highcharts.com/highcharts.js"><\/script>');
    document.write('<script src="https://code.highcharts.com/highcharts-more.js"><\/script>');
    document.write('<script src="https://code.highcharts.com/modules/exporting.js"><\/script>');
    document.write('<script src="https://code.highcharts.com/modules/accessibility.js"><\/script>');
} else if (typeof Highcharts.Series.types.bubble === 'undefined') {
     document.write('<script src="https://code.highcharts.com/highcharts-more.js"><\/script>');
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Global State for CSRF and Filters
    var rendererCsrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var rendererCsrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    
    // Store active filters per chart
    var activeChartFilters = {};

    <?php foreach ($charts as $chart): ?>
    loadChart(<?= $chart->id ?>, '<?= $chart->chart_type ?>', '<?= htmlspecialchars($chart->chart_title, ENT_QUOTES, 'UTF-8') ?>');
    <?php endforeach; ?>
    
    function loadChart(id, type, title, filters = []) {
        var postData = {filters: filters};
        postData[rendererCsrfName] = rendererCsrfHash;

        $.ajax({
            url: '<?= site_url('chart_api/get_data/') ?>' + id,
            type: 'POST',
            data: postData,
            dataType: 'json',
            success: function(data) {
                if (data.csrfHash) {
                    rendererCsrfHash = data.csrfHash;
                }

                var chartOptions = {
                    chart: { 
                        type: type === 'polar' ? 'line' : type,
                        polar: type === 'polar'
                    },
                    title: { text: title }, 
                    xAxis: data.xAxis,
                    yAxis: { title: { text: 'Value' } },
                    series: data.series,
                    credits: { enabled: false }
                };

                if (type === 'polar') {
                    chartOptions.pane = { size: '80%' };
                    chartOptions.xAxis.tickmarkPlacement = 'on';
                    chartOptions.xAxis.lineWidth = 0;
                    chartOptions.yAxis.gridLineInterpolation = 'polygon';
                    chartOptions.yAxis.lineWidth = 0;
                    chartOptions.yAxis.min = 0;
                }

                Highcharts.chart('dynamic-chart-' + id, chartOptions);
            },
            error: function() {
                $('#dynamic-chart-' + id).html('<div class="alert alert-danger">Failed to load chart data</div>');
            }
        });
    }

    // Modal Interaction
    $(document).on('click', '.open-chart-filter', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var filters = $(this).data('filters'); // jQuery auto-parses JSON in data attribs if consistent? No, string.
        
        // Safer parsing
        if (typeof filters === 'string') {
             try { filters = JSON.parse(filters); } catch(e) {}
        }
        
        $('#publicFilterChartId').val(id);
        
        var $sel = $('#publicFilterColumn');
        $sel.empty().append('<option value="">-- Select Column --</option>');
        
        if (Array.isArray(filters)) {
            filters.forEach(function(f) {
                 var label = f.label || f.column;
                 $sel.append($('<option>', {
                     value: f.column,
                     text: label + ' (' + f.type + ')',
                     'data-type': f.type
                 }));
            });
        }
        
        $('#publicFilterInputContainer').html('<div class="text-center text-muted">Select a column first</div>');
        $('#publicChartFilterModal').modal('show');
    });

    $('#publicFilterColumn').change(function() {
        var opt = $(this).find(':selected');
        var type = opt.data('type');
        var col = $(this).val();
        var container = $('#publicFilterInputContainer');
        var id = $('#publicFilterChartId').val();
        
        if (!col) {
            container.html('<div class="text-center text-muted">Select a column first</div>');
            return;
        }

        let inputHtml = '';
        if (type == 'month_year') {
             inputHtml = '<input type="month" class="form-control" id="publicFilterValue">';
        } else if (type == 'year') {
             inputHtml = '<input type="number" class="form-control" id="publicFilterValue" placeholder="YYYY">';
        } else if (type == 'date') {
             inputHtml = '<input type="date" class="form-control" id="publicFilterValue">';
        } else {
             inputHtml = '<input type="text" class="form-control" id="publicFilterValue" placeholder="Enter value...">';
        }
        
        container.html('<div class="form-group"><label>Value</label>' + inputHtml + '</div>');
        
        if (activeChartFilters[id]) {
             var existing = activeChartFilters[id].find(f => f.column == col);
             if (existing) {
                 $('#publicFilterValue').val(existing.value);
             }
         }
    });

    $('#applyPublicFilterBtn').click(function() {
         var id = $('#publicFilterChartId').val();
         var col = $('#publicFilterColumn').val();
         var val = $('#publicFilterValue').val();
         var type = $('#publicFilterColumn option:selected').data('type');
         
         if (col && id) {
             if (!activeChartFilters[id]) activeChartFilters[id] = [];
             
             activeChartFilters[id] = activeChartFilters[id].filter(f => f.column !== col);
             
             if (val) {
                 activeChartFilters[id].push({column: col, value: val, type: type});
             }
             
             // Retrieve original Type and Title from DOM
             var $header = $('[data-chart-id="' + id + '"]');
             var originalType = $header.data('chart-type') || 'column';
             var originalTitle = $header.data('chart-title') || '';
             
             loadChart(id, originalType, originalTitle, activeChartFilters[id]);
             
             $('#publicChartFilterModal').modal('hide');
             
             if (typeof Swal !== 'undefined') {
                 Swal.fire({
                     icon: 'success',
                     title: 'Filter Applied',
                     timer: 1000,
                     showConfirmButton: false
                 });
             }
         }
    });
});
</script>
<?php endif; ?>
