<script>
$(document).ready(function() {
    // Handle widget detail modal
    $('.view-widget-detail').on('click', function(e) {
        e.preventDefault();
        var widgetId = $(this).data('widget-id');
        var url = '<?= site_url('summary_widget/detail/') ?>' + widgetId;
        
        // Show modal with loading state
        $('#widgetDetailModal').modal('show');
        $('#widgetDetailContent').html('<div class="text-center"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading...</p></div>');
        
        // Fetch widget detail data
        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.error) {
                    $('#widgetDetailContent').html('<div class="alert alert-danger">' + response.error + '</div>');
                    return;
                }
                
                var widget = response.widget;
                var data = response.data;
                
                // Build modal content based on aggregate type
                var html = buildDetailContent(widget, data);
                $('#widgetDetailContent').html(html);
                $('#widgetDetailModalLabel').text(widget.title + ' - Detail');
                
                // Initialize DataTable if table exists
                if ($('#detailDataTable').length) {
                    $('#detailDataTable').DataTable({
                        pageLength: 25,
                        order: []
                    });
                }
            },
            error: function() {
                $('#widgetDetailContent').html('<div class="alert alert-danger">Failed to load widget details.</div>');
            }
        });
    });
    
    function buildDetailContent(widget, data) {
        var html = '';
        
        // Widget Info Section
        html += '<div class="row mb-3">';
        html += '<div class="col-md-6">';
        html += '<strong>Table:</strong> ' + data.table_name + '<br>';
        html += '<strong>Column:</strong> ' + data.column_name + '<br>';
        html += '<strong>Aggregate:</strong> ' + data.aggregate_type;
        html += '</div>';
        html += '<div class="col-md-6 text-right">';
        html += '<h3 class="text-primary">' + data.formatted_value + '</h3>';
        html += '</div>';
        html += '</div>';
        
        html += '<hr>';
        
        // Data Section based on aggregate type
        switch(data.aggregate_type) {
            case 'COUNT':
                html += '<div class="alert alert-info">';
                html += 'Showing ' + data.showing_count + ' of ' + data.total_count + ' total records';
                html += '</div>';
                break;
                
            case 'SUM':
            case 'AVG':
                html += '<div class="alert alert-info">';
                html += 'Showing up to 500 rows ordered by ' + data.column_name + ' (descending)';
                if (data.total_rows) {
                    html += '<br>Total rows with non-null values: ' + data.total_rows;
                }
                html += '</div>';
                break;
                
            case 'MIN':
                html += '<div class="alert alert-success">';
                html += 'Showing row(s) with minimum value: ' + data.formatted_value;
                html += '</div>';
                break;
                
            case 'MAX':
                html += '<div class="alert alert-success">';
                html += 'Showing row(s) with maximum value: ' + data.formatted_value;
                html += '</div>';
                break;
        }
        
        // Data Table
        if (data.rows && data.rows.length > 0) {
            html += '<div class="table-responsive">';
            html += '<table class="table table-striped table-bordered table-hover" id="detailDataTable">';
            html += '<thead><tr>';
            
            // Table headers
            var firstRow = data.rows[0];
            for (var key in firstRow) {
                if (firstRow.hasOwnProperty(key)) {
                    var headerClass = (key === data.column_name) ? 'bg-primary text-white' : '';
                    html += '<th class="' + headerClass + '">' + key + '</th>';
                }
            }
            html += '</tr></thead>';
            
            // Table body
            html += '<tbody>';
            data.rows.forEach(function(row) {
                html += '<tr>';
                for (var key in row) {
                    if (row.hasOwnProperty(key)) {
                        var cellClass = (key === data.column_name) ? 'font-weight-bold' : '';
                        var value = row[key] === null ? '<em class="text-muted">NULL</em>' : escapeHtml(row[key]);
                        html += '<td class="' + cellClass + '">' + value + '</td>';
                    }
                }
                html += '</tr>';
            });
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
        } else {
            html += '<div class="alert alert-warning">No data available</div>';
        }
        
        return html;
    }
    
    function escapeHtml(text) {
        var map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }
});
</script>
