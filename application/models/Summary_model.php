<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Summary_model extends CI_Model
{
    private $table = 'tbl_summary_widgets';

    public function get_all_widgets()
    {
        return $this->db->get($this->table)->result();
    }

    public function get_widget($id)
    {
        return $this->db->get_where($this->table, ['id' => $id])->row();
    }

    public function save($data, $id = null)
    {
        if ($id) {
            $this->db->where('id', $id);
            return $this->db->update($this->table, $data);
        } else {
            return $this->db->insert($this->table, $data);
        }
    }

    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }

    // Reuse logic from Chart_model
    public function get_available_tables()
    {
        $tables = $this->db->list_tables();
        $ignore = ['mst_user', 'mst_roles', 'mst_modules', 'mst_permissions', 'tbl_role_permissions', 'tbl_user_roles', 'tbl_aplikasi', 'migrations', 'tbl_sessions', 'tbl_rbac_audit_log', 'tbl_crud_history'];
        
        $result = [];
        foreach ($tables as $t) {
            if (!in_array($t, $ignore)) {
                $result[] = $t;
            }
        }
        return $result;
    }

    public function get_table_columns($table_name)
    {
        if (!$this->db->table_exists($table_name)) return [];
        return $this->db->list_fields($table_name);
    }

    public function get_widgets_for_dashboard($requestor_roles, $placement = 'dashboard')
    {
        // specific role check
        if (!is_array($requestor_roles)) {
            $requestor_roles = [$requestor_roles];
        }

        $this->db->where('is_active', 1);
        $this->db->where('placement', $placement); // Add filter
        $widgets = $this->db->get($this->table)->result();
        
        $allowed_widgets = [];
        foreach ($widgets as $w) {
            $allowed_roles = json_decode($w->allowed_roles, true);
            if (is_array($allowed_roles)) {
                // Check if any of the requestor's roles are in the allowed roles
                if (array_intersect($requestor_roles, $allowed_roles)) {
                    $val = $this->calculate_summary($w);
                    
                    if (isset($w->formatting) && $w->formatting == 'rupiah') {
                        $val = rupiah($val);
                    }
                    
                    $w->value = $val;
                    $allowed_widgets[] = $w;
                }
            }
        }
        return $allowed_widgets;
    }

    public function get_aggregate_value($table, $col, $agg)
    {
        if (!$this->db->table_exists($table)) return 0;

        // Basic safety check for column existence if not *
        if ($col !== '*' && !$this->db->field_exists($col, $table)) {
            return 0; 
        }

        $agg = strtoupper($agg);
        
        // Prevent SQL injection by validating agg
        $allowed_aggs = ['SUM', 'AVG', 'MIN', 'MAX', 'COUNT'];
        if (!in_array($agg, $allowed_aggs)) return 0;

        if ($agg == 'COUNT') {
            return $this->db->count_all($table);
        } else {
            // Use Query Builder for safety where possible, or careful escaping
            $this->db->select("$agg($col) as val");
            $query = $this->db->get($table);
            $row = $query->row();
            return $row ? $row->val : 0;
        }
    }

    private function calculate_summary($widget)
    {
        return $this->get_aggregate_value($widget->table_name, $widget->column_name, $widget->aggregate_func);
    }

    public function get_detail_data($id)
    {
        $widget = $this->get_widget($id);
        if (!$widget) return [];

        if (!$this->db->table_exists($widget->table_name)) return [];
        
        // Return all columns or specific? 
        // User request: "popup whole data on column amount" -> likely implies showing the rows that contributed.
        // For simple aggregates without filters, it's just the whole table.
        // Limiting to 1000 for safety.
        return $this->db->limit(1000)->get($widget->table_name)->result();
    }

    /**
     * Get detail data formatted according to aggregate function
     * Returns different data structures based on COUNT, SUM, AVG, MIN, MAX
     */
    public function get_detail_data_by_aggregate($id)
    {
        $widget = $this->get_widget($id);
        if (!$widget) return ['error' => 'Widget not found'];

        if (!$this->db->table_exists($widget->table_name)) {
            return ['error' => 'Table not found'];
        }

        $table = $widget->table_name;
        $column = $widget->column_name;
        $agg = strtoupper($widget->aggregate_func);

        $result = [
            'aggregate_type' => $agg,
            'table_name' => $table,
            'column_name' => $column,
            'aggregate_value' => $this->get_aggregate_value($table, $column, $agg),
            'rows' => []
        ];

        // Format aggregate value if needed
        if ($widget->formatting == 'rupiah') {
            $result['formatted_value'] = rupiah($result['aggregate_value']);
        } else {
            $result['formatted_value'] = is_numeric($result['aggregate_value']) ? number_format($result['aggregate_value']) : $result['aggregate_value'];
        }

        switch ($agg) {
            case 'COUNT':
                // For COUNT, show sample of first 100 rows
                $result['rows'] = $this->db->limit(100)->get($table)->result_array();
                $result['total_count'] = $result['aggregate_value'];
                $result['showing_count'] = count($result['rows']);
                break;

            case 'SUM':
                // For SUM, show all rows with the summed column (limit 500)
                $this->db->select('*');
                $this->db->where("$column IS NOT NULL");
                $this->db->order_by($column, 'DESC');
                $result['rows'] = $this->db->limit(500)->get($table)->result_array();
                $result['total_rows'] = $this->db->where("$column IS NOT NULL")->count_all_results($table);
                break;

            case 'AVG':
                // For AVG, show all rows with the averaged column (limit 500)
                $this->db->select('*');
                $this->db->where("$column IS NOT NULL");
                $this->db->order_by($column, 'DESC');
                $result['rows'] = $this->db->limit(500)->get($table)->result_array();
                $result['total_rows'] = $this->db->where("$column IS NOT NULL")->count_all_results($table);
                break;

            case 'MIN':
                // For MIN, show only the row(s) with minimum value
                $min_val = $result['aggregate_value'];
                $this->db->where($column, $min_val);
                $result['rows'] = $this->db->get($table)->result_array();
                break;

            case 'MAX':
                // For MAX, show only the row(s) with maximum value
                $max_val = $result['aggregate_value'];
                $this->db->where($column, $max_val);
                $result['rows'] = $this->db->get($table)->result_array();
                break;

            default:
                // Default: show first 100 rows
                $result['rows'] = $this->db->limit(100)->get($table)->result_array();
                break;
        }

        return $result;
    }
}
