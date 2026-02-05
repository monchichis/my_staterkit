<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chart_model extends CI_Model
{
    public function get_charts()
    {
        return $this->db->get('tbl_chart_gen')->result();
    }
    
    public function get_chart($id)
    {
        return $this->db->get_where('tbl_chart_gen', ['id' => $id])->row();
    }
    
    public function create_chart($data)
    {
        return $this->db->insert('tbl_chart_gen', $data);
    }
    
    public function update_chart($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update('tbl_chart_gen', $data);
    }
    
    public function delete_chart($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete('tbl_chart_gen');
    }
    
    public function get_available_tables()
    {
        $tables = $this->db->list_tables();
        $ignore = ['mst_user', 'mst_roles', 'mst_modules', 'mst_permissions', 'tbl_role_permissions', 'tbl_user_roles', 'tbl_aplikasi', 'migrations', 'tbl_sessions', 'tbl_rbac_audit_log', 'tbl_crud_history'];
        
        $result = [];
        foreach ($tables as $t) {
            // Only exclude system tables if really needed, but for superadmin, maybe allow most?
            // Let's exclude core RBAC tables to avoid confusion, but keep business tables.
            if (!in_array($t, $ignore)) {
                $result[] = $t;
            }
        }
        return $result;
    }
    
    public function get_table_columns($table)
    {
        if (!$this->db->table_exists($table)) return [];
        return $this->db->list_fields($table);
    }
    
    /**
     * Fetch data based on config
     * Config structure expected:
     * [
     *    {
     *       "table": "tbl_sales",
     *       "type": "column",
     *       "name": "Total Sales",
     *       "x_axis": "transaction_date",  // Group By
     *       "y_axis": "amount",            // Value column
     *       "aggregate": "SUM",            // Aggregate function
     *       "color": "#ff0000"
     *    }
     * ]
     */
    public function fetch_chart_data($config, $active_filters = [])
    {
        $series_data = [];
        $categories = []; // For X-axis if needed (e.g. strict dates)
        
        // Handle if config is wrapped (future proofing)
        $series_list = isset($config['series']) ? $config['series'] : $config;

        foreach ($series_list as $series_idx => $s) {
            $table = $s['table'];
            $x_col = $s['x_axis'];
            $y_col = $s['y_axis']; // Can be '*' for COUNT
            $agg = strtoupper($s['aggregate']);
            
            // Build Query
            $this->db->from($table);
            
            // Apply Filters (Advanced Menu)
            if (!empty($active_filters)) {
                foreach ($active_filters as $filter) {
                    // Check if this filter applies to this table (or just apply blindly if user knows what they are doing)
                    // For now, assume filters are global if column exists. 
                    // To be safe, we should check if column exists in this table, but performance...
                    // Let's rely on the Admin picking valid columns.
                    
                    $f_col = $filter['column'];
                    $f_val = $filter['value'];
                    $f_type = $filter['type'] ?? 'text';
                    
                    if (empty($f_val)) continue; // Skip empty filters
                    
                    // Simple logic to check if column likely belongs to this table? 
                    // No, let's just create a robust query.
                    // Ideally we pass "table.column" but we might just have "column".
                    // Let's assume column names are unique or just try to match.
                    
                     if ($this->db->field_exists($f_col, $table)) {
                         if ($f_type == 'month_year') {
                             // Input: "YYYY-MM"
                             // Query: YEAR(col) = Y AND MONTH(col) = M
                             $parts = explode('-', $f_val);
                             if (count($parts) == 2) {
                                 $this->db->where("YEAR($f_col)", $parts[0]);
                                 $this->db->where("MONTH($f_col)", $parts[1]);
                             }
                         } elseif ($f_type == 'year') {
                             $this->db->where("YEAR($f_col)", $f_val);
                         } elseif ($f_type == 'date') {
                             $this->db->where("DATE($f_col)", $f_val);
                         } elseif ($f_type == 'date_range') {
                             // Value expected: "YYYY-MM-DD - YYYY-MM-DD" or JSON
                             $dates = explode(' - ', $f_val);
                             if (count($dates) == 2) {
                                  $this->db->where("$f_col >=", $dates[0]);
                                  $this->db->where("$f_col <=", $dates[1]);
                             }
                         } else {
                             // Default Exact or Like?
                             // Exact match is safer for categories.
                             $this->db->where($f_col, $f_val);
                         }
                     }
                }
            }
            
            // Select Group By (X Axis)
            $this->db->select($x_col . ' as x_val');
            $this->db->group_by($x_col);
            
            // Select Aggregate (Y Axis)
            if ($agg == 'COUNT') {
                $this->db->select('COUNT(*) as y_val');
            } else {
                // Ensure safe column
                // $this->db->select("$agg($y_col) as y_val");
                 // Handle potential issues if Y column is not numeric?
                 // But aggregate functions usually handle it.
                 $this->db->select("$agg($y_col) as y_val");
            }
            
            $this->db->order_by('x_val', 'ASC');
            
            $query = $this->db->get();
            $results = $query->result();
            
            $data_points = [];
            foreach ($results as $row) {
                // Formatting for Highcharts
                // If x_val is date, maybe format? For now raw.
                $data_points[] = [
                    'name' => $row->x_val, // Category name
                    'y' => (float) $row->y_val
                ];
                
                // Collect categories if this is the first series (or merge them)
                if ($series_idx == 0) {
                    $categories[] = $row->x_val;
                }
            }
            
            $series_type = $s['type'] ?? 'column';
            // 'polar' is not a series type, fallback to 'line' or 'area'
            if ($series_type == 'polar') {
                $series_type = 'line'; 
            }

            $series_data[] = [
                'name' => $s['name'] ?? 'Series ' . ($series_idx + 1),
                'data' => $data_points,
                'type' => $series_type,
                'color' => $s['color'] ?? null
            ];
        }
        
        return [
            'series' => $series_data,
            'xAxis' => [
                'categories' => $categories // Optional, depends on how we pass data
            ]
        ];
    }
    
    public function get_charts_for_view($placement, $requestor_roles = [])
    {
        $this->db->where('placement_identifier', $placement);
        $this->db->where('is_active', 1);
        $charts = $this->db->get('tbl_chart_gen')->result();
        
        // If requestor has multiple roles, check if ANY matches the allowed_roles for the chart
        if (!is_array($requestor_roles)) {
            $requestor_roles = [$requestor_roles];
        }
        
        $filtered = [];
        foreach ($charts as $c) {
            $allowed = json_decode($c->allowed_roles); // Array of allowed role IDs
            if (is_array($allowed)) {
                // Check intersection
                $intersection = array_intersect($allowed, $requestor_roles);
                if (!empty($intersection)) {
                    $filtered[] = $c;
                }
            }
        }
        return $filtered;
    }
}
