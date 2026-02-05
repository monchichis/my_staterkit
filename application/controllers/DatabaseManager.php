<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DatabaseManager extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // CRITICAL: Load session library FIRST before calling any helper functions
        // that depend on it (is_logged_in and is_admin both use session)
        $this->load->library('session');
        
        // Now we can safely call these helper functions
        is_logged_in();
        is_admin();

        // Load other required libraries
        $this->load->dbforge();
        
        // Load helpers
        $this->load->helper('ata');
    }

    public function index()
    {
        $data['title'] = 'Database Manager';
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        
        $data['tables'] = $this->db->list_tables();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/database/index', $data);
        $this->load->view('templates/footer');
    }

    public function create()
    {
        $data['title'] = 'Create New Table';
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        
        $data['tables'] = $this->db->list_tables();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/database/create', $data);
        $this->load->view('templates/footer');
    }

    public function get_table_columns() 
    {
        $table = $this->input->post('table');
        
        if (empty($table)) {
            echo json_encode(['error' => 'Table name is empty']);
            return;
        }
        
        if (!$this->db->table_exists($table)) {
            echo json_encode(['error' => 'Table not found']);
            return;
        }

        $fields = $this->db->list_fields($table);
        echo json_encode(['fields' => $fields, 'csrfHash' => $this->security->get_csrf_hash()]);
    }

    public function store()
    {
        $table_name = $this->input->post('table_name');
        $columns = $this->input->post('columns');

        if (!$table_name || empty($columns)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Table name and at least one column are required.</div>');
            redirect('DatabaseManager/create');
        }

        // Validate table name (alphanumeric and underscores only)
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $table_name)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid table name. Use only letters, numbers, and underscores.</div>');
            redirect('DatabaseManager/create');
        }

        $fields = [];
        $key = '';
        $primary_key_set = false;
        $foreign_keys = [];

        foreach ($columns as $col) {
            if (empty($col['name'])) continue;

            // Validate column name
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $col['name'])) {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid column name: ' . htmlspecialchars($col['name']) . '. Use only letters, numbers, and underscores.</div>');
                redirect('DatabaseManager/create');
            }

            $field_config = [
                'type' => $col['type'],
            ];

            if (!empty($col['length'])) {
                $field_config['constraint'] = $col['length'];
            }

            if (isset($col['auto_increment']) && $col['auto_increment'] == '1') {
                $field_config['auto_increment'] = TRUE;
            }
            
            if (isset($col['null']) && $col['null'] == '1') {
                $field_config['null'] = TRUE;
            } else {
                 $field_config['null'] = FALSE;
            }
            
            if (!empty($col['default'])) {
                 $field_config['default'] = $col['default'];
            }

            $fields[$col['name']] = $field_config;

            if (isset($col['primary_key']) && $col['primary_key'] == '1') {
                $this->dbforge->add_key($col['name'], TRUE);
                $primary_key_set = true;
            }

            if (isset($col['is_foreign_key']) && $col['is_foreign_key'] == '1') {
                if (!empty($col['ref_table']) && !empty($col['ref_column'])) {
                    // Validate reference table and column names
                    if (!preg_match('/^[a-zA-Z0-9_]+$/', $col['ref_table']) || !preg_match('/^[a-zA-Z0-9_]+$/', $col['ref_column'])) {
                        $this->session->set_flashdata('message', '<div class="alert alert-danger">Invalid foreign key reference.</div>');
                        redirect('DatabaseManager/create');
                    }
                    
                    $foreign_keys[] = [
                        'column' => $col['name'],
                        'ref_table' => $col['ref_table'],
                        'ref_column' => $col['ref_column']
                    ];
                }
            }
        }

        $this->dbforge->add_field($fields);
        
        if ($this->dbforge->create_table($table_name, TRUE)) {
            // Apply Foreign Keys with error handling
            $fk_errors = [];
            foreach ($foreign_keys as $fk) {
                try {
                    // Use db->escape_identifiers for table/column names
                    $constraint_name = 'fk_' . $table_name . '_' . $fk['column'];
                    $sql = "ALTER TABLE " . $this->db->escape_identifiers($table_name) . 
                           " ADD CONSTRAINT " . $this->db->escape_identifiers($constraint_name) . 
                           " FOREIGN KEY (" . $this->db->escape_identifiers($fk['column']) . ")" .
                           " REFERENCES " . $this->db->escape_identifiers($fk['ref_table']) . 
                           "(" . $this->db->escape_identifiers($fk['ref_column']) . ")" .
                           " ON DELETE CASCADE ON UPDATE CASCADE";
                    
                    if (!$this->db->query($sql)) {
                        $fk_errors[] = "Failed to create foreign key for column: " . $fk['column'];
                    }
                } catch (Exception $e) {
                    $fk_errors[] = "Error creating foreign key for column " . $fk['column'] . ": " . $e->getMessage();
                }
            }

            if (!empty($fk_errors)) {
                $error_msg = '<div class="alert alert-warning">Table created but some foreign keys failed:<ul>';
                foreach ($fk_errors as $err) {
                    $error_msg .= '<li>' . htmlspecialchars($err) . '</li>';
                }
                $error_msg .= '</ul></div>';
                $this->session->set_flashdata('message', $error_msg);
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-success">Table ' . htmlspecialchars($table_name) . ' created successfully.</div>');
            }
            
            redirect('DatabaseManager');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to create table.</div>');
             redirect('DatabaseManager/create');
        }
    }

    public function detail($table)
    {
        if (!$this->db->table_exists($table)) {
             redirect('DatabaseManager');
        }

        $data['title'] = 'Table Structure: ' . $table;
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['table_name'] = $table;
        $data['fields'] = $this->db->field_data($table);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/database/detail', $data);
        $this->load->view('templates/footer');
    }

    public function drop($table)
    {
        try {
            // First, try to disable foreign key checks temporarily
            $this->db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            if ($this->dbforge->drop_table($table, TRUE)) {
                // Re-enable foreign key checks
                $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
                
                $this->session->set_flashdata('message', '<div class="alert alert-success">Table ' . htmlspecialchars($table) . ' deleted successfully.</div>');
                $this->session->set_flashdata('message_type', 'success');
            } else {
                // Re-enable foreign key checks
                $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
                
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to delete table.</div>');
                $this->session->set_flashdata('message_type', 'error');
            }
        } catch (Exception $e) {
            // Re-enable foreign key checks in case of error
            $this->db->query('SET FOREIGN_KEY_CHECKS = 1');
            
            $error_message = $e->getMessage();
            
            // Check if it's a foreign key constraint error
            if (strpos($error_message, 'foreign key constraint') !== false || 
                strpos($error_message, 'Cannot delete or update a parent row') !== false) {
                
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Cannot delete table <strong>' . htmlspecialchars($table) . '</strong>. This table has foreign key relationships with other tables. Please delete the related data first or remove the foreign key constraints.</div>');
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message_title', 'Foreign Key Constraint');
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger">Error deleting table: ' . htmlspecialchars($error_message) . '</div>');
                $this->session->set_flashdata('message_type', 'error');
                $this->session->set_flashdata('message_title', 'Database Error');
            }
        }
        
        redirect('DatabaseManager');
    }

    public function add_column($table)
    {
        $data['title'] = 'Add Column to ' . $table;
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['table_name'] = $table;
        $data['tables'] = $this->db->list_tables();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/database/add_column', $data);
        $this->load->view('templates/footer');
    }

    public function store_column($table)
    {
        $col = $this->input->post('column');
        
        $field_config = [
            'type' => $col['type'],
        ];

        if (!empty($col['length'])) {
            $field_config['constraint'] = $col['length'];
        }

        if (isset($col['auto_increment']) && $col['auto_increment'] == '1') {
            $field_config['auto_increment'] = TRUE;
        }
        
        if (isset($col['null']) && $col['null'] == '1') {
            $field_config['null'] = TRUE;
        } else {
             $field_config['null'] = FALSE;
        }
        
        if (!empty($col['default'])) {
             $field_config['default'] = $col['default'];
        }

        $fields = [$col['name'] => $field_config];

        if ($this->dbforge->add_column($table, $fields)) {
            
            // Check for Foreign Key
            if (isset($col['is_foreign_key']) && $col['is_foreign_key'] == '1') {
                if (!empty($col['ref_table']) && !empty($col['ref_column'])) {
                    try {
                        $constraint_name = 'fk_' . $table . '_' . $col['name'];
                        // Ensure constraint name is unique/randomized if needed, but standard naming is usually okay
                        // If constraint exists, MySQL might error, so maybe add random suffix or handle error
                        
                        $sql = "ALTER TABLE " . $this->db->escape_identifiers($table) . 
                               " ADD CONSTRAINT " . $this->db->escape_identifiers($constraint_name) . 
                               " FOREIGN KEY (" . $this->db->escape_identifiers($col['name']) . ")" .
                               " REFERENCES " . $this->db->escape_identifiers($col['ref_table']) . 
                               "(" . $this->db->escape_identifiers($col['ref_column']) . ")" .
                               " ON DELETE CASCADE ON UPDATE CASCADE";
                        
                        if ($this->db->query($sql)) {
                            $this->session->set_flashdata('message', '<div class="alert alert-success">Column added successfully with Foreign Key relation.</div>');
                        } else {
                            $this->session->set_flashdata('message', '<div class="alert alert-warning">Column added but failed to create Foreign Key. Check data types compatibility.</div>');
                        }
                    } catch (Exception $e) {
                         $this->session->set_flashdata('message', '<div class="alert alert-warning">Column added but Foreign Key error: ' . $e->getMessage() . '</div>');
                    }
                }
            } else {
                 $this->session->set_flashdata('message', '<div class="alert alert-success">Column added successfully.</div>');
            }
             
        } else {
             $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to add column.</div>');
        }
        redirect('DatabaseManager/detail/' . $table);
    }

    public function edit_column($table, $field_name)
    {
        $data['title'] = 'Edit Column: ' . $field_name;
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['table_name'] = $table;
        $data['field_name'] = $field_name;
        
        // Get column details - simplistic approach as DBForge doesn't expose easy get_column
        // In a real scenario you might parse SHOW COLUMNS output better.
        // For now relying on user re-entering data or defaults.
        $fields = $this->db->field_data($table);
        foreach($fields as $f) {
            if($f->name == $field_name) {
                $data['current_field'] = $f;
                break;
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/database/edit_column', $data);
        $this->load->view('templates/footer');
    }

    public function update_column($table, $original_name)
    {
         $col = $this->input->post('column');
        
        $field_config = [
            'name' => $col['name'],
            'type' => $col['type'],
        ];

        if (!empty($col['length'])) {
            $field_config['constraint'] = $col['length'];
        }
        
        if (isset($col['null']) && $col['null'] == '1') {
            $field_config['null'] = TRUE;
        } else {
             $field_config['null'] = FALSE;
        }
        
        if (!empty($col['default'])) {
             $field_config['default'] = $col['default'];
        }

        $fields = [$original_name => $field_config];

        if ($this->dbforge->modify_column($table, $fields)) {
             $this->session->set_flashdata('message', '<div class="alert alert-success">Column updated successfully.</div>');
        } else {
             $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to update column.</div>');
        }
        redirect('DatabaseManager/detail/' . $table);
    }

    public function drop_column($table, $field)
    {
        if ($this->dbforge->drop_column($table, $field)) {
             $this->session->set_flashdata('message', '<div class="alert alert-success">Column deleted successfully.</div>');
        } else {
             $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to delete column.</div>');
        }
        redirect('DatabaseManager/detail/' . $table);
    }
}
