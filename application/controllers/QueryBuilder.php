<?php
defined('BASEPATH') or exit('No direct script access allowed');

class QueryBuilder extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load session
        $this->load->library('session');
        
        // Load database
        $this->load->database();
        
        is_logged_in();
        
        // Redirect if not Super Admin
        if ($this->session->userdata('level') != 'Super Admin') {
            $role_slug = strtolower(str_replace(' ', '_', $this->session->userdata('level')));
            redirect($role_slug);
        }
        
        $this->load->helper('ata');
        $this->load->helper('tglindo');
        $this->load->helper('rupiah');
    }
    
    /**
     * Main Query Builder page
     */
    public function index()
    {
        $data['title'] = 'Query Builder';
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['tables'] = $this->db->list_tables();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/query_builder/index', $data);
        $this->load->view('templates/footer');
    }
    
    /**
     * AJAX: Get all tables from database
     */
    public function get_tables()
    {
        $tables = $this->db->list_tables();
        
        echo json_encode([
            'status' => true,
            'data' => $tables,
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * AJAX: Get columns for a specific table
     */
    public function get_columns($table = null)
    {
        if (empty($table)) {
            $table = $this->input->post('table');
        }
        
        if (empty($table)) {
            echo json_encode([
                'status' => false,
                'message' => 'Table name is required',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        // Security: Check if table exists
        $tables = $this->db->list_tables();
        if (!in_array($table, $tables)) {
            echo json_encode([
                'status' => false,
                'message' => 'Table not found',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        $fields = $this->db->field_data($table);
        $columns = [];
        
        foreach ($fields as $field) {
            $columns[] = [
                'name' => $field->name,
                'type' => $field->type,
                'max_length' => $field->max_length,
                'primary_key' => $field->primary_key
            ];
        }
        
        echo json_encode([
            'status' => true,
            'data' => $columns,
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * AJAX: Preview generated SQL query
     */
    public function preview_query()
    {
        $columns_json = $this->input->post('columns');
        $columns = json_decode($columns_json, true);
        
        $joins_json = $this->input->post('joins');
        $joins = !empty($joins_json) ? json_decode($joins_json, true) : [];
        
        if (empty($columns)) {
            echo json_encode([
                'status' => false,
                'message' => 'No columns selected',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        $sql = $this->build_sql($columns, $joins);
        $ci_syntax = $this->build_ci3_syntax($columns, $joins);
        
        echo json_encode([
            'status' => true,
            'sql' => $sql,
            'ci_syntax' => $ci_syntax,
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * AJAX: Execute the generated query
     */
    public function execute_query()
    {
        $columns_json = $this->input->post('columns');
        $columns = json_decode($columns_json, true);
        
        $joins_json = $this->input->post('joins');
        $joins = !empty($joins_json) ? json_decode($joins_json, true) : [];
        
        if (empty($columns)) {
            echo json_encode([
                'status' => false,
                'message' => 'No columns selected',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        $sql = $this->build_sql($columns, $joins);
        
        try {
            $query = $this->db->query($sql);
            
            if ($query === false) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Query execution failed: ' . $this->db->error()['message'],
                    'sql' => $sql,
                    'csrfHash' => $this->security->get_csrf_hash()
                ]);
                return;
            }
            
            $result = $query->result_array();
            $fields = $query->list_fields();
            
            echo json_encode([
                'status' => true,
                'sql' => $sql,
                'fields' => $fields,
                'data' => $result,
                'total_rows' => count($result),
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Query execution error: ' . $e->getMessage(),
                'sql' => $sql,
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        }
    }
    
    /**
     * AJAX: Execute raw SQL query
     */
    public function execute_raw_query()
    {
        $sql = $this->input->post('sql');
        
        if (empty($sql)) {
            echo json_encode([
                'status' => false,
                'message' => 'SQL query is required',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        // Security: Only allow SELECT statements
        $sql_upper = strtoupper(trim($sql));
        if (strpos($sql_upper, 'SELECT') !== 0) {
            echo json_encode([
                'status' => false,
                'message' => 'Only SELECT queries are allowed for security reasons',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        // Block dangerous keywords
        $dangerous_keywords = ['DROP', 'DELETE', 'TRUNCATE', 'INSERT', 'UPDATE', 'ALTER', 'CREATE', 'GRANT', 'REVOKE'];
        foreach ($dangerous_keywords as $keyword) {
            if (strpos($sql_upper, $keyword) !== false) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Dangerous SQL keyword detected: ' . $keyword,
                    'csrfHash' => $this->security->get_csrf_hash()
                ]);
                return;
            }
        }
        
        try {
            $query = $this->db->query($sql);
            
            if ($query === false) {
                echo json_encode([
                    'status' => false,
                    'message' => 'Query execution failed: ' . $this->db->error()['message'],
                    'sql' => $sql,
                    'csrfHash' => $this->security->get_csrf_hash()
                ]);
                return;
            }
            
            $result = $query->result_array();
            $fields = $query->list_fields();
            
            // Generate CI3 syntax from raw SQL
            $ci_syntax = $this->parse_raw_sql_to_ci3($sql);
            
            echo json_encode([
                'status' => true,
                'sql' => $sql,
                'ci_syntax' => $ci_syntax,
                'fields' => $fields,
                'data' => $result,
                'total_rows' => count($result),
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Query execution error: ' . $e->getMessage(),
                'sql' => $sql,
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        }
    }
    
    /**
     * Build SQL query from column configurations
     */
    private function build_sql($columns, $joins = [])
    {
        $select_parts = [];
        $from_table = '';
        $join_parts = [];
        $where_parts = [];
        $order_parts = [];
        $used_tables = [];
        
        foreach ($columns as $col) {
            if (empty($col['table'])) {
                continue;
            }
            
            // Track first table as main FROM table
            if (empty($from_table)) {
                $from_table = '`' . $col['table'] . '`';
                if (!empty($col['table_alias'])) {
                    $from_table .= ' AS `' . $col['table_alias'] . '`';
                }
                $used_tables[$col['table']] = !empty($col['table_alias']) ? $col['table_alias'] : $col['table'];
            } else {
                // Additional tables - check if needs to be added as JOIN
                if (!isset($used_tables[$col['table']])) {
                    $used_tables[$col['table']] = !empty($col['table_alias']) ? $col['table_alias'] : $col['table'];
                }
            }
            
            // Determine table reference (use alias if provided)
            $table_ref = !empty($col['table_alias']) ? '`' . $col['table_alias'] . '`' : '`' . $col['table'] . '`';
            
            // Build SELECT part (only if show is true)
            if (isset($col['show']) && $col['show']) {
                if ($col['column'] === '*') {
                    $select_part = $table_ref . '.*';
                } else {
                    $select_part = $table_ref . '.`' . $col['column'] . '`';
                    if (!empty($col['column_alias'])) {
                        $select_part .= ' AS `' . $col['column_alias'] . '`';
                    }
                }
                $select_parts[] = $select_part;
            }
            
            // Build WHERE part
            if (isset($col['criteria']) && $col['criteria']['enabled'] && !empty($col['criteria']['value'])) {
                $criteria = $col['criteria'];
                $criteria_column = !empty($criteria['column']) ? $criteria['column'] : $col['column'];
                
                $column_ref = $table_ref . '.`' . $criteria_column . '`';
                $operator = $this->sanitize_operator($criteria['operator']);
                $value = $criteria['value'];
                
                // Handle different operators
                if ($operator === 'LIKE') {
                    $value = "'%" . $this->db->escape_like_str($value) . "%'";
                } elseif ($operator === 'IN') {
                    // Handle IN operator with comma-separated values
                    $in_values = array_map(function($v) {
                        return $this->db->escape(trim($v));
                    }, explode(',', $value));
                    $value = '(' . implode(', ', $in_values) . ')';
                } elseif ($operator === 'NOT IN') {
                    $in_values = array_map(function($v) {
                        return $this->db->escape(trim($v));
                    }, explode(',', $value));
                    $value = '(' . implode(', ', $in_values) . ')';
                } elseif ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
                    $value = '';
                } elseif ($criteria['type'] === 'number') {
                    $value = floatval($value);
                } else {
                    $value = $this->db->escape($value);
                }
                
                if ($operator === 'IS NULL' || $operator === 'IS NOT NULL') {
                    $where_parts[] = $column_ref . ' ' . $operator;
                } else {
                    $where_parts[] = $column_ref . ' ' . $operator . ' ' . $value;
                }
            }
            
            // Build ORDER BY part
            if (isset($col['criteria']) && !empty($col['criteria']['sort']) && $col['criteria']['sort'] !== 'none') {
                $criteria = $col['criteria'];
                $sort_column = !empty($criteria['column']) ? $criteria['column'] : $col['column'];
                $sort_direction = strtoupper($criteria['sort']) === 'DESC' ? 'DESC' : 'ASC';
                
                if ($sort_column !== '*') {
                    $order_parts[] = $table_ref . '.`' . $sort_column . '` ' . $sort_direction;
                }
            }
        }
        
        // Build the complete SQL
        if (empty($select_parts)) {
            $select_parts[] = '*';
        }
        
        $sql = 'SELECT ' . implode(",\n       ", $select_parts);
        
        if (!empty($from_table)) {
            $sql .= "\nFROM " . $from_table;
        }
        
        // Add JOINs if provided
        if (!empty($joins)) {
            foreach ($joins as $join) {
                if (empty($join['table']) || empty($join['type'])) {
                    continue;
                }
                
                $join_type = $this->sanitize_join_type($join['type']);
                $join_table = '`' . $join['table'] . '`';
                
                if (!empty($join['alias'])) {
                    $join_table .= ' AS `' . $join['alias'] . '`';
                }
                
                $join_sql = "\n" . $join_type . ' ' . $join_table;
                
                // Add ON condition
                if (!empty($join['on_left']) && !empty($join['on_right'])) {
                    $join_sql .= ' ON ' . $join['on_left'] . ' = ' . $join['on_right'];
                }
                
                $join_parts[] = $join_sql;
            }
            
            $sql .= implode('', $join_parts);
        }
        
        if (!empty($where_parts)) {
            $sql .= "\nWHERE " . implode("\n  AND ", $where_parts);
        }
        
        if (!empty($order_parts)) {
            $sql .= "\nORDER BY " . implode(", ", $order_parts);
        }
        
        return $sql;
    }
    
    /**
     * Sanitize JOIN type to prevent injection
     */
    private function sanitize_join_type($type)
    {
        $valid_types = ['INNER JOIN', 'LEFT JOIN', 'RIGHT JOIN', 'LEFT OUTER JOIN', 'RIGHT OUTER JOIN', 'CROSS JOIN', 'NATURAL JOIN'];
        
        $type = strtoupper(trim($type));
        
        if (in_array($type, $valid_types)) {
            return $type;
        }
        
        return 'INNER JOIN';
    }
    
    /**
     * Sanitize SQL operator to prevent injection
     */
    private function sanitize_operator($operator)
    {
        $valid_operators = ['=', '!=', '<>', '<', '>', '<=', '>=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN', 'IS NULL', 'IS NOT NULL', 'BETWEEN'];
        
        $operator = strtoupper(trim($operator));
        
        if (in_array($operator, $valid_operators)) {
            return $operator;
        }
        
        return '=';
    }
    
    /**
     * Build CodeIgniter 3 Query Builder syntax from column configurations
     */
    private function build_ci3_syntax($columns, $joins = [])
    {
        $lines = [];
        $lines[] = '// CodeIgniter 3 Query Builder';
        $lines[] = '$this->db';
        
        $select_parts = [];
        $from_table = '';
        $used_from = false;
        
        foreach ($columns as $col) {
            if (empty($col['table'])) {
                continue;
            }
            
            // First table becomes FROM
            if (!$used_from) {
                $from_table = $col['table'];
                $used_from = true;
                $lines[] = "    ->from('{$col['table']}')";
            }
            
            // Determine table reference
            $table_ref = !empty($col['table_alias']) ? $col['table_alias'] : $col['table'];
            
            // Build SELECT part (only if show is true)
            if (isset($col['show']) && $col['show']) {
                if ($col['column'] === '*') {
                    $select_parts[] = "{$table_ref}.*";
                } else {
                    $select_part = "{$table_ref}.{$col['column']}";
                    if (!empty($col['column_alias'])) {
                        $select_part .= " AS {$col['column_alias']}";
                    }
                    $select_parts[] = $select_part;
                }
            }
            
            // Build WHERE part
            if (isset($col['criteria']) && $col['criteria']['enabled'] && !empty($col['criteria']['value'])) {
                $criteria = $col['criteria'];
                $criteria_column = !empty($criteria['column']) ? $criteria['column'] : $col['column'];
                $column_ref = "{$table_ref}.{$criteria_column}";
                $operator = $this->sanitize_operator($criteria['operator']);
                $value = $criteria['value'];
                
                // Generate appropriate CI3 method
                if ($operator === 'LIKE') {
                    $lines[] = "    ->like('{$column_ref}', '{$value}')";
                } elseif ($operator === 'NOT LIKE') {
                    $lines[] = "    ->not_like('{$column_ref}', '{$value}')";
                } elseif ($operator === 'IN') {
                    $in_values = array_map('trim', explode(',', $value));
                    $in_str = "['" . implode("', '", $in_values) . "']";
                    $lines[] = "    ->where_in('{$column_ref}', {$in_str})";
                } elseif ($operator === 'NOT IN') {
                    $in_values = array_map('trim', explode(',', $value));
                    $in_str = "['" . implode("', '", $in_values) . "']";
                    $lines[] = "    ->where_not_in('{$column_ref}', {$in_str})";
                } elseif ($operator === 'IS NULL') {
                    $lines[] = "    ->where('{$column_ref} IS NULL')";
                } elseif ($operator === 'IS NOT NULL') {
                    $lines[] = "    ->where('{$column_ref} IS NOT NULL')";
                } elseif ($operator === '=') {
                    $lines[] = "    ->where('{$column_ref}', '{$value}')";
                } else {
                    $lines[] = "    ->where(\"{$column_ref} {$operator}\", '{$value}')";
                }
            }
            
            // Build ORDER BY part
            if (isset($col['criteria']) && !empty($col['criteria']['sort']) && $col['criteria']['sort'] !== 'none') {
                $criteria = $col['criteria'];
                $sort_column = !empty($criteria['column']) ? $criteria['column'] : $col['column'];
                $sort_direction = strtoupper($criteria['sort']) === 'DESC' ? 'DESC' : 'ASC';
                
                if ($sort_column !== '*') {
                    $lines[] = "    ->order_by('{$table_ref}.{$sort_column}', '{$sort_direction}')";
                }
            }
        }
        
        // Add JOINs
        if (!empty($joins)) {
            $join_lines = [];
            foreach ($joins as $join) {
                if (empty($join['table']) || empty($join['type'])) {
                    continue;
                }
                
                $join_type = strtolower(str_replace(' JOIN', '', $join['type']));
                if ($join_type === 'inner') $join_type = '';
                
                $join_table = $join['table'];
                if (!empty($join['alias'])) {
                    $join_table .= ' AS ' . $join['alias'];
                }
                
                $on_condition = '';
                if (!empty($join['on_left']) && !empty($join['on_right'])) {
                    // Remove backticks for CI3 syntax
                    $on_left = str_replace('`', '', $join['on_left']);
                    $on_right = str_replace('`', '', $join['on_right']);
                    $on_condition = "{$on_left} = {$on_right}";
                }
                
                if ($join_type) {
                    $join_lines[] = "    ->join('{$join_table}', '{$on_condition}', '{$join_type}')";
                } else {
                    $join_lines[] = "    ->join('{$join_table}', '{$on_condition}')";
                }
            }
            
            // Insert joins after from()
            if (!empty($join_lines)) {
                $from_index = 2; // After $this->db and ->from()
                array_splice($lines, $from_index + 1, 0, $join_lines);
            }
        }
        
        // Add SELECT at the beginning (after from and joins)
        if (!empty($select_parts)) {
            $select_str = implode(', ', $select_parts);
            // Find where to insert select (after $this->db)
            array_splice($lines, 1, 0, ["    ->select('{$select_str}')"]);
        }
        
        // Add get() at the end
        $lines[] = "    ->get();";
        $lines[] = "";
        $lines[] = '// Get result';
        $lines[] = '$result = $query->result();';
        
        return implode("\n", $lines);
    }
    
    /**
     * Parse raw SQL to CodeIgniter 3 Query Builder syntax
     */
    private function parse_raw_sql_to_ci3($sql)
    {
        $lines = [];
        $lines[] = '// CodeIgniter 3 Query Builder (from raw SQL)';
        $lines[] = '$query = $this->db';
        
        // Clean the SQL
        $sql = trim($sql);
        $sql = preg_replace('/\s+/', ' ', $sql); // Normalize whitespace
        
        // Parse SELECT
        if (preg_match('/SELECT\s+(.+?)\s+FROM/i', $sql, $matches)) {
            $select = trim($matches[1]);
            if ($select !== '*') {
                $lines[] = "    ->select('{$select}')";
            }
        }
        
        // Parse FROM
        if (preg_match('/FROM\s+`?(\w+)`?(?:\s+(?:AS\s+)?`?(\w+)`?)?/i', $sql, $matches)) {
            $table = $matches[1];
            $alias = isset($matches[2]) ? $matches[2] : '';
            if ($alias && $alias !== $table) {
                $lines[] = "    ->from('{$table} AS {$alias}')";
            } else {
                $lines[] = "    ->from('{$table}')";
            }
        }
        
        // Parse JOINs
        preg_match_all('/(LEFT|RIGHT|INNER|OUTER|CROSS)?\s*JOIN\s+`?(\w+)`?(?:\s+(?:AS\s+)?`?(\w+)`?)?\s+ON\s+(.+?)(?=(?:LEFT|RIGHT|INNER|OUTER|CROSS)?\s*JOIN|WHERE|ORDER|GROUP|LIMIT|$)/i', $sql, $joins, PREG_SET_ORDER);
        foreach ($joins as $join) {
            $type = strtolower(trim($join[1])) ?: '';
            $table = $join[2];
            $alias = isset($join[3]) ? $join[3] : '';
            $condition = trim($join[4]);
            
            $table_str = $alias ? "{$table} AS {$alias}" : $table;
            
            if ($type) {
                $lines[] = "    ->join('{$table_str}', '{$condition}', '{$type}')";
            } else {
                $lines[] = "    ->join('{$table_str}', '{$condition}')";
            }
        }
        
        // Parse WHERE
        if (preg_match('/WHERE\s+(.+?)(?=ORDER|GROUP|LIMIT|$)/i', $sql, $matches)) {
            $where = trim($matches[1]);
            // Split by AND/OR (simplified)
            $conditions = preg_split('/\s+AND\s+/i', $where);
            foreach ($conditions as $condition) {
                $condition = trim($condition);
                if (!empty($condition)) {
                    $lines[] = "    ->where(\"{$condition}\")";
                }
            }
        }
        
        // Parse ORDER BY
        if (preg_match('/ORDER\s+BY\s+(.+?)(?=LIMIT|$)/i', $sql, $matches)) {
            $order = trim($matches[1]);
            $orders = explode(',', $order);
            foreach ($orders as $ord) {
                $ord = trim($ord);
                if (preg_match('/(.+?)\s+(ASC|DESC)/i', $ord, $m)) {
                    $lines[] = "    ->order_by('{$m[1]}', '{$m[2]}')";
                } else if (!empty($ord)) {
                    $lines[] = "    ->order_by('{$ord}')";
                }
            }
        }
        
        // Parse LIMIT
        if (preg_match('/LIMIT\s+(\d+)(?:\s*,\s*(\d+))?/i', $sql, $matches)) {
            if (isset($matches[2])) {
                $lines[] = "    ->limit({$matches[2]}, {$matches[1]})";
            } else {
                $lines[] = "    ->limit({$matches[1]})";
            }
        }
        
        $lines[] = "    ->get();";
        $lines[] = "";
        $lines[] = '// Get result';
        $lines[] = '$result = $query->result();';
        
        return implode("\n", $lines);
    }
}
