<script>
$(document).ready(function() {
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
    var isLoading = false;

    function updateCsrf(newHash) {
        csrfHash = newHash;
        $('#csrf_token_input').val(csrfHash);
    }
    
    // Load Columns
    function loadColumns(table, keepSelected = false) {
        var selectedCol = '<?= isset($widget) ? $widget->column_name : "" ?>';
        // If not keeping selected (e.g. manual change), clear selection? 
        // Actually the PHP value is only relevant on initial load.
        if (!keepSelected) selectedCol = $('#columnSelect').val();

        if(table && !isLoading) {
            isLoading = true;
            $.ajax({
                url: '<?= site_url('summary_widget/get_table_columns') ?>',
                type: 'POST',
                data: {table: table, [csrfName]: csrfHash},
                dataType: 'json',
                success: function(res) {
                    updateCsrf(res.csrfHash);
                    
                    var cols = res.columns;
                    var $el = $('#columnSelect');
                    $el.empty();
                    $el.append('<option value="">-- Select Column --</option>');
                    $el.append('<option value="*">* (All/Count)</option>');
                    
                    $.each(cols, function(i, col){
                        var selected = (col == selectedCol) ? 'selected' : '';
                        $el.append('<option value="'+col+'" '+selected+'>'+col+'</option>');
                    });
                    
                    isLoading = false;
                    updatePreview();
                },
                error: function() {
                    isLoading = false;
                    console.error('Failed to load columns');
                }
            });
        }
    }

    $('#tableSelect').change(function() {
        loadColumns($(this).val());
    });
    
    // Preview Logic
    function updatePreview() {
        var title = $('#inputTitle').val() || 'Widget Title';
        var table = $('#tableSelect').val();
        var col = $('#columnSelect').val();
        var agg = $('#aggSelect').val();
        var bgClass = $('input[name="bg_color_class"]:checked').val();
        var formatting = $('#formatSelect').val(); // Add formatting
        
        // Update UI
        $('#previewTitle').text(title);
        $('#previewWidget').removeClass().addClass('widget style1 ' + bgClass);
        
        if(table && col) {
            if(isLoading) return; // Prevent race
            
             $.ajax({
                url: '<?= site_url('summary_widget/preview_value') ?>',
                type: 'POST',
                data: {table: table, column: col, agg: agg, formatting: formatting, [csrfName]: csrfHash},
                dataType: 'json',
                success: function(res) {
                    updateCsrf(res.csrfHash);
                    $('#previewValue').text(res.value);
                },
                error: function(xhr) {
                    // Handle CSRF mismatch generic error
                    if(xhr.status == 403) {
                        alert('Session token expired or invalid. Please reload the page.');
                    }
                }
            });
        }
    }
    
    $('#inputTitle, #aggSelect, #columnSelect, #formatSelect').change(updatePreview);
    $('#btnPreview').click(updatePreview);
    $('input[name="bg_color_class"]').change(updatePreview);
    
    // Initial Load
    var initialTable = $('#tableSelect').val();
    if (initialTable) {
        // If editing, we need to load columns first. 
        // Pass true to keep the PHP-rendered selected value if it matches.
        loadColumns(initialTable, true);
    } else {
        // Just show the default preview
        updatePreview();
    }
});
</script>
