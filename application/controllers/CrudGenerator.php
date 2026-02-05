<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CrudGenerator extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        // Block access in production for security
        if (ENVIRONMENT === 'production') {
            $this->load->view('errors/production_blocked');
            $this->output->_display();
            exit;
        }
        
        $this->load->database();
        $this->load->helper('file');
        $this->load->helper('url');
        $this->load->library('session');
        $this->load->library('form_validation');
        
        // Security check
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
        
        // Restrict to development environment
        if (ENVIRONMENT === 'production') {
            show_error('This feature is disabled in production environment.', 403);
        }

        if ($this->session->userdata('level') != 'Super Admin') { 
             redirect('auth/blocked');
        }
    }

    public function index()
    {
        $data['title'] = 'CRUD Generator';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        
        $tables = $this->db->list_tables();
        
        $system_tables = [
            'mst_modules', 
            'mst_parent_menus',
            'mst_permissions', 
            'mst_roles', 
            'mst_user', 
            'tbl_aplikasi', 
            'tbl_rbac_audit_log', 
            'tbl_role_permissions',
            'tbl_crud_history',
            'tbl_user_roles'
        ];
        
        $data['tables'] = array_diff($tables, $system_tables);
        
        // Initialize empty edit data
        $data['edit_data'] = null;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('crud_generator/index', $data);
        $this->load->view('templates/footer');
    }

    public function edit($id)
    {
        $history = $this->db->get_where('tbl_crud_history', ['id' => $id])->row();
        
        if (!$history) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">History record not found!</div>');
            redirect('CrudGenerator/crud_history');
        }
        
        $data['title'] = 'Edit CRUD Generator';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        
        $tables = $this->db->list_tables();
        $system_tables = [
            'mst_modules', 'mst_parent_menus', 'mst_permissions', 'mst_roles', 'mst_user', 
            'tbl_aplikasi', 'tbl_rbac_audit_log', 'tbl_role_permissions', 'tbl_crud_history', 'tbl_user_roles'
        ];
        $data['tables'] = array_diff($tables, $system_tables);
        
        // Pass history data to view
        $data['edit_data'] = $history;
        $data['field_configs'] = $history->field_configs ? json_decode($history->field_configs, true) : null;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('crud_generator/index', $data);
        $this->load->view('templates/footer');
    }



    public function get_table_fields()
    {
        $table_name = $this->input->post('table_name');
        
        if (empty($table_name)) {
            echo json_encode(['success' => false, 'message' => 'Table name required']);
            return;
        }
        
        $fields = $this->get_field_metadata($table_name);
        
        if ($fields === false) {
            echo json_encode(['success' => false, 'message' => 'Failed to fetch table fields']);
            return;
        }
        
        // Add suggested input types and validation
        foreach ($fields as &$field) {
            $field['suggested_input'] = $this->determine_input_type($field);
            $field['suggested_validation'] = $this->generate_validation_rules($field);
        }
        
        echo json_encode(['success' => true, 'fields' => $fields, 'csrfHash' => $this->security->get_csrf_hash()]);
    }

    public function get_table_columns()
    {
        $table_name = $this->input->post('table_name');
        
        if (empty($table_name)) {
            echo json_encode(['success' => false, 'message' => 'Table name required', 'csrfHash' => $this->security->get_csrf_hash()]);
            return;
        }

        if (!$this->db->table_exists($table_name)) {
             echo json_encode(['success' => false, 'message' => 'Table not found', 'csrfHash' => $this->security->get_csrf_hash()]);
             return;
        }

        $columns = $this->db->list_fields($table_name);
        
        echo json_encode(['success' => true, 'columns' => $columns, 'csrfHash' => $this->security->get_csrf_hash()]);
    }

    /**
     * Get field metadata from database table
     */
    private function get_field_metadata($table_name)
    {
        if (!$this->db->table_exists($table_name)) {
            return false;
        }
        
        $fields = $this->db->field_data($table_name);
        
        if (empty($fields)) {
            return false;
        }
        
        $result = [];
        foreach ($fields as $field) {
            $result[] = [
                'name' => $field->name,
                'type' => $field->type,
                'max_length' => $field->max_length,
                'primary_key' => $field->primary_key,
                'default' => $field->default
            ];
        }
        
        return $result;
    }

    /**
     * Determine appropriate input type based on field metadata
     */
    private function determine_input_type($field)
    {
        $type = strtolower($field['type']);
        $name = strtolower($field['name']);
        
        // Check field name for common patterns
        if (strpos($name, 'email') !== false) {
            return 'email';
        }
        if (strpos($name, 'password') !== false) {
            return 'password';
        }
        if (strpos($name, 'phone') !== false || strpos($name, 'telp') !== false || strpos($name, 'hp') !== false) {
            return 'tel';
        }
        if (strpos($name, 'url') !== false || strpos($name, 'website') !== false || strpos($name, 'link') !== false) {
            return 'url';
        }
        
        // Check data type
        switch ($type) {
            case 'int':
            case 'integer':
            case 'bigint':
            case 'smallint':
            case 'tinyint':
            case 'decimal':
            case 'float':
            case 'double':
                return 'number';
            
            case 'date':
                return 'date';
            
            case 'datetime':
            case 'timestamp':
                return 'datetime-local';
            
            case 'time':
                return 'time';
            
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'textarea';
            
            case 'enum':
                return 'select';
            
            default:
                return 'text';
        }
    }

    /**
     * Generate validation rules based on field metadata
     */
    private function generate_validation_rules($field)
    {
        $rules = [];
        $name = strtolower($field['name']);
        $type = strtolower($field['type']);
        
        // Skip primary key from required
        if (!$field['primary_key']) {
            $rules[] = 'required';
        }
        
        // Add max_length if available
        if (!empty($field['max_length']) && $field['max_length'] > 0) {
            $rules[] = 'max_length[' . $field['max_length'] . ']';
        }
        
        // Add specific validations based on field name
        if (strpos($name, 'email') !== false) {
            $rules[] = 'valid_email';
        }
        
        // Add numeric validation for number types
        if (in_array($type, ['int', 'integer', 'bigint', 'smallint', 'tinyint', 'decimal', 'float', 'double'])) {
            $rules[] = 'numeric';
        }
        
        return implode('|', $rules);
    }

    public function generate()
    {
        $table_name = $this->input->post('table_name');
        $controller_name = ucfirst($this->input->post('controller_name'));
        $model_name = ucfirst($this->input->post('model_name'));
        $field_configs = $this->input->post('field_configs'); // New: field customizations
        $notification_type = $this->input->post('notification_type'); // New: notification type
        
        if (empty($table_name) || empty($controller_name) || empty($model_name)) {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">All fields are required!</div>');
            redirect('CrudGenerator');
        }

        // SMART FOLDER CREATION
        // Check if any field is configured as file upload and create directory immediately
        if ($field_configs) {
            foreach ($field_configs as $field_name => $config) {
                if (isset($config['input_type']) && $config['input_type'] === 'file') {
                    $upload_path = FCPATH . 'uploads/' . $table_name;
                    if (!is_dir($upload_path)) {
                        mkdir($upload_path, 0755, true);
                        // Create index.html to prevent directory listing
                        write_file($upload_path . '/index.html', '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>');
                    }
                    // We only need to create the folder once per table
                    break;
                }
            }
        }

        // Generate Model
        if ($this->generate_model($table_name, $model_name)) {
            // Generate Controller with field configs
            if ($this->generate_controller($table_name, $controller_name, $model_name, $field_configs, $notification_type)) {
                // Generate Views with field configs
                if ($this->generate_views($table_name, $controller_name, $model_name, $field_configs, $notification_type)) {
                    // Save to CRUD history
                    $id = $this->input->post('id');
                    $this->save_crud_history($table_name, $controller_name, $model_name, $field_configs, $notification_type, $id);
                    $this->session->set_flashdata('message', '<div class="alert alert-success">CRUD Generated Successfully! Controller, Model, and Views created.</div>');
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-warning">Controller and Model created, but failed to generate views!</div>');
                }
            } else {
                 $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to generate controller!</div>');
            }
        } else {
             $this->session->set_flashdata('message', '<div class="alert alert-danger">Failed to generate model!</div>');
        }

        redirect('CrudGenerator');
    }

    /**
     * Generate model file for the table
     */
    private function generate_model($table_name, $model_name)
    {
        $fields = $this->get_field_metadata($table_name);
        
        if ($fields === false || empty($fields)) {
            return false;
        }
        
        $pk = $fields[0]['name']; // Assume first field is primary key
        
        $model_content = "<?php\n";
        $model_content .= "defined('BASEPATH') or exit('No direct script access allowed');\n\n";
        $model_content .= "class {$model_name} extends CI_Model\n";
        $model_content .= "{\n";
        $model_content .= "    protected \$table = '{$table_name}';\n";
        $model_content .= "    protected \$pk = '{$pk}';\n\n";
        
        // Get all records
        $model_content .= "    public function get_all()\n";
        $model_content .= "    {\n";
        $model_content .= "        return \$this->db->get(\$this->table)->result();\n";
        $model_content .= "    }\n\n";
        
        // Get single record by ID
        $model_content .= "    public function get_by_id(\$id)\n";
        $model_content .= "    {\n";
        $model_content .= "        return \$this->db->get_where(\$this->table, [\$this->pk => \$id])->row();\n";
        $model_content .= "    }\n\n";
        
        // Insert record
        $model_content .= "    public function insert(\$data)\n";
        $model_content .= "    {\n";
        $model_content .= "        \$this->db->insert(\$this->table, \$data);\n";
        $model_content .= "        return \$this->db->insert_id();\n";
        $model_content .= "    }\n\n";
        
        // Update record
        $model_content .= "    public function update(\$id, \$data)\n";
        $model_content .= "    {\n";
        $model_content .= "        \$this->db->where(\$this->pk, \$id);\n";
        $model_content .= "        return \$this->db->update(\$this->table, \$data);\n";
        $model_content .= "    }\n\n";
        
        // Delete record
        $model_content .= "    public function delete(\$id)\n";
        $model_content .= "    {\n";
        $model_content .= "        \$this->db->where(\$this->pk, \$id);\n";
        $model_content .= "        return \$this->db->delete(\$this->table);\n";
        $model_content .= "    }\n";
        
        $model_content .= "}\n";
        
        $model_path = APPPATH . 'models/' . $model_name . '.php';
        
        return file_put_contents($model_path, $model_content) !== false;
    }

    private function generate_controller($table, $controller_name, $model_name, $field_configs = null, $notification_type = 'bootstrap')
    {
        $fields = $this->get_field_metadata($table);
        
        if ($fields === false || empty($fields)) {
            return false;
        }
        
        $pk = $fields[0]['name'];
        $model_var = strtolower($model_name);
        
        $content = "<?php\ndefined('BASEPATH') or exit('No direct script access allowed');\n\n";
        $content .= "class $controller_name extends MY_Controller\n{\n";
        
        // Constructor
        $content .= "    public function __construct()\n    {\n        parent::__construct();\n";
        $content .= "        \$this->load->model('$model_name', '$model_var');\n";
        $content .= "        \$this->load->library('form_validation');\n";
        $content .= "        \$this->load->helper(['form', 'url']);\n";
        $content .= "        \$this->load->library('session');\n";
        $content .= "        \$this->load->library('rbac_lib', null, 'rbac');\n";
        $content .= "        // Add your RBAC check here\n    }\n\n";
        
        // Add helper method to load FK data if there are FK relationships
        $has_any_fk = false;
        $fk_relationships = [];
        
        // Detect file upload fields
        $has_file_uploads = false;
        $file_upload_fields = [];
        
        if ($field_configs) {
            foreach ($fields as $field) {
                if (isset($field_configs[$field['name']]['has_fk']) && $field_configs[$field['name']]['has_fk']) {
                    $has_any_fk = true;
                    $fk_relationships[] = [
                        'field' => $field['name'],
                        'table' => $field_configs[$field['name']]['fk_table'],
                        'key' => $field_configs[$field['name']]['fk_key'],
                        'display' => $field_configs[$field['name']]['fk_display']
                    ];
                }
                // Check for file upload fields
                if (isset($field_configs[$field['name']]['input_type']) && $field_configs[$field['name']]['input_type'] === 'file') {
                    $has_file_uploads = true;
                    $file_upload_fields[] = [
                        'field' => $field['name'],
                        'multiple' => isset($field_configs[$field['name']]['file_multiple']) && $field_configs[$field['name']]['file_multiple']
                    ];
                }
            }
        }
        
        if ($has_any_fk) {
            $content .= "    private function load_fk_data()\n    {\n";
            $content .= "        \$data = [];\n";
            foreach ($fk_relationships as $fk) {
                $fk_var = strtolower(str_replace('tbl_', '', $fk['table'])) . '_data';
                $content .= "        \$data['$fk_var'] = \$this->db->get('{$fk['table']}')->result();\n";
            }
            $content .= "        return \$data;\n";
            $content .= "    }\n\n";
        }
        
        // Add file upload helper method if there are file fields
        if ($has_file_uploads) {
            $content .= "    private function _do_upload(\$field_name, \$multiple = false)\n    {\n";
            $content .= "        \$upload_path = './uploads/$table/';
        if (!is_dir(\$upload_path)) {
            mkdir(\$upload_path, 0755, true);
        }
        
        \$config['upload_path'] = \$upload_path;
        \$config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
        \$config['max_size'] = 5120; // 5MB
        \$config['encrypt_name'] = TRUE;
        
        \$this->load->library('upload', \$config);
        
        if (\$multiple) {
            \$files = \$_FILES[\$field_name];
            \$uploaded_files = [];
            
            if (!empty(\$files['name'][0])) {
                \$count = count(\$files['name']);
                for (\$i = 0; \$i < \$count; \$i++) {
                    \$_FILES['temp_file']['name'] = \$files['name'][\$i];
                    \$_FILES['temp_file']['type'] = \$files['type'][\$i];
                    \$_FILES['temp_file']['tmp_name'] = \$files['tmp_name'][\$i];
                    \$_FILES['temp_file']['error'] = \$files['error'][\$i];
                    \$_FILES['temp_file']['size'] = \$files['size'][\$i];
                    
                    \$this->upload->initialize(\$config);
                    
                    if (\$this->upload->do_upload('temp_file')) {
                        \$upload_data = \$this->upload->data();
                        \$uploaded_files[] = \$upload_data['file_name'];
                    }
                }
            }
            
            return !empty(\$uploaded_files) ? json_encode(\$uploaded_files) : '';
        } else {
            if (!empty(\$_FILES[\$field_name]['name'])) {
                if (\$this->upload->do_upload(\$field_name)) {
                    \$upload_data = \$this->upload->data();
                    return \$upload_data['file_name'];
                }
            }
            return '';
        }
    }\n\n";
        }
        
        // Index
        $content .= "    public function index()\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".view');\n";
        $content .= "        \$data['title'] = '$controller_name List';\n";
        $content .= "        \$data['{$table}_data'] = \$this->{$model_var}->get_all();\n";
        $content .= "        \$data['user'] = \$this->db->get_where('mst_user', ['id_user' => \$this->session->userdata('id_user')])->row_array();\n";
        $content .= "        \$this->load->view('templates/header', \$data);\n";
        $content .= "        // Dynamic Sidebar\n";
        $content .= "        \$role = \$this->session->userdata('level');\n";
        $content .= "        if (\$role == 'Super Admin') {\n";
        $content .= "            \$sidebar_view = 'templates/sidebar_superadmin';\n";
        $content .= "        } else {\n";
        $content .= "            \$role_slug = strtolower(str_replace(' ', '_', \$role));\n";
        $content .= "            \$sidebar_view = 'templates/sidebar_' . \$role_slug;\n";
        $content .= "        }\n";
        $content .= "        \$this->load->view(\$sidebar_view, \$data);\n";
        $content .= "        \$this->load->view('$table/list', \$data);\n";
        $content .= "        \$this->load->view('templates/footer');\n";
        $content .= "    }\n\n";
        
        // Create
        $content .= "    public function create()\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".create');\n";
        $content .= "        \$data['title'] = 'Create $controller_name';\n";
        $content .= "        \$data['action'] = site_url('$controller_name/create_action');\n";
        $content .= "        \$data['user'] = \$this->db->get_where('mst_user', ['id_user' => \$this->session->userdata('id_user')])->row_array();\n";
        if ($has_any_fk) {
            $content .= "        \$data = array_merge(\$data, \$this->load_fk_data());\n";
        }
        foreach ($fields as $field) {
            $is_file_field = false;
            foreach ($file_upload_fields as $fuf) {
                if ($fuf['field'] === $field['name']) {
                    $is_file_field = true;
                    break;
                }
            }
            if ($is_file_field) {
                $content .= "        \$data['{$field['name']}'] = set_value('{$field['name']}', '', FALSE);\n";
            } else {
                $content .= "        \$data['{$field['name']}'] = set_value('{$field['name']}');\n";
            }
        }
        $content .= "        \$this->load->view('templates/header', \$data);\n";
        $content .= "        // Dynamic Sidebar\n";
        $content .= "        \$role = \$this->session->userdata('level');\n";
        $content .= "        if (\$role == 'Super Admin') {\n";
        $content .= "            \$sidebar_view = 'templates/sidebar_superadmin';\n";
        $content .= "        } else {\n";
        $content .= "            \$role_slug = strtolower(str_replace(' ', '_', \$role));\n";
        $content .= "            \$sidebar_view = 'templates/sidebar_' . \$role_slug;\n";
        $content .= "        }\n";
        $content .= "        \$this->load->view(\$sidebar_view, \$data);\n";
        $content .= "        \$this->load->view('$table/form', \$data);\n";
        $content .= "        \$this->load->view('templates/footer');\n";
        $content .= "    }\n\n";
        
        // Create Action
        $content .= "    public function create_action()\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".create');\n";
        $content .= "        \$this->_rules();\n";
        $content .= "        if (\$this->form_validation->run() == FALSE) {\n";
        $content .= "            \$this->create();\n";
        $content .= "        } else {\n";
        $content .= "            \$data = array(\n";
        foreach ($fields as $field) {
             if ($field['primary_key'] == 1) continue;
             
             // Check if this is a file field
             $is_file_field = false;
             $is_multiple = false;
             foreach ($file_upload_fields as $fuf) {
                 if ($fuf['field'] === $field['name']) {
                     $is_file_field = true;
                     $is_multiple = $fuf['multiple'];
                     break;
                 }
             }
             
             if ($is_file_field) {
                 // Skip for now, will add after the array
                 continue;
             }
             $content .= "                '{$field['name']}' => \$this->input->post('{$field['name']}', TRUE),\n";
        }
        $content .= "            );\n";
        
        // Add file upload handling
        foreach ($file_upload_fields as $fuf) {
            $multiple_str = $fuf['multiple'] ? 'true' : 'false';
            $content .= "            \$uploaded_{$fuf['field']} = \$this->_do_upload('{$fuf['field']}', $multiple_str);\n";
            $content .= "            if (!empty(\$uploaded_{$fuf['field']})) {\n";
            $content .= "                \$data['{$fuf['field']}'] = \$uploaded_{$fuf['field']};\n";
            $content .= "            }\n";
        }
        
        $content .= "            \$this->{$model_var}->insert(\$data);\n";

        // NOTIFICATION LOGIC FOR CREATE
        if ($notification_type == 'bootstrap') {
            $content .= "            \$this->session->set_flashdata('message', '<div class=\"alert alert-success alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><i class=\"fa fa-check\"></i> Create Record Success</div>');\n";
        } else {
             // For SweetAlert/IziToast, we pass a simple string or a specific identifier
            $content .= "            \$this->session->set_flashdata('message', 'Create Record Success');\n";
            $content .= "            \$this->session->set_flashdata('message_type', 'success');\n";
        }
        
        $content .= "            redirect(site_url('$controller_name'));\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        
        // Update
        $content .= "    public function update(\$id)\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".update');\n";
        $content .= "        \$row = \$this->{$model_var}->get_by_id(\$id);\n";
        $content .= "        if (\$row) {\n";
        $content .= "            \$data['title'] = 'Update $controller_name';\n";
        $content .= "            \$data['action'] = site_url('$controller_name/update_action');\n";
        $content .= "            \$data['user'] = \$this->db->get_where('mst_user', ['id_user' => \$this->session->userdata('id_user')])->row_array();\n";
        if ($has_any_fk) {
            $content .= "            \$data = array_merge(\$data, \$this->load_fk_data());\n";
        }
        foreach ($fields as $field) {
            $is_file_field = false;
            foreach ($file_upload_fields as $fuf) {
                if ($fuf['field'] === $field['name']) {
                    $is_file_field = true;
                    break;
                }
            }
            if ($is_file_field) {
                $content .= "            \$data['{$field['name']}'] = set_value('{$field['name']}', \$row->{$field['name']}, FALSE);\n";
            } else {
                $content .= "            \$data['{$field['name']}'] = set_value('{$field['name']}', \$row->{$field['name']});\n";
            }
        }
        $content .= "            \$this->load->view('templates/header', \$data);\n";
        $content .= "            // Dynamic Sidebar\n";
        $content .= "            \$role = \$this->session->userdata('level');\n";
        $content .= "            if (\$role == 'Super Admin') {\n";
        $content .= "                \$sidebar_view = 'templates/sidebar_superadmin';\n";
        $content .= "            } else {\n";
        $content .= "                \$role_slug = strtolower(str_replace(' ', '_', \$role));\n";
        $content .= "                \$sidebar_view = 'templates/sidebar_' . \$role_slug;\n";
        $content .= "            }\n";
        $content .= "            \$this->load->view(\$sidebar_view, \$data);\n";
        $content .= "            \$this->load->view('$table/form', \$data);\n";
        $content .= "            \$this->load->view('templates/footer');\n";
        $content .= "        } else {\n";
        
        // NOTIFICATION LOGIC FOR UPDATE (NOT FOUND)
        if ($notification_type == 'bootstrap') {
             $content .= "            \$this->session->set_flashdata('message', '<div class=\"alert alert-danger alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><i class=\"fa fa-warning\"></i> Record Not Found</div>');\n";
        } else {
             $content .= "            \$this->session->set_flashdata('message', 'Record Not Found');\n";
             $content .= "            \$this->session->set_flashdata('message_type', 'warning');\n";
        }

        $content .= "            redirect(site_url('$controller_name'));\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        
        // Update Action
        $content .= "    public function update_action()\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".update');\n";
        $content .= "        \$this->_rules();\n";
        $content .= "        if (\$this->form_validation->run() == FALSE) {\n";
        $content .= "            \$this->update(\$this->input->post('$pk', TRUE));\n";
        $content .= "        } else {\n";
        $content .= "            \$data = array(\n";
        foreach ($fields as $field) {
             if ($field['primary_key'] == 1) continue;
             
             // Check if this is a file field
             $is_file_field = false;
             foreach ($file_upload_fields as $fuf) {
                 if ($fuf['field'] === $field['name']) {
                     $is_file_field = true;
                     break;
                 }
             }
             
             if ($is_file_field) {
                 // Skip for now, will add after the array
                 continue;
             }
             $content .= "                '{$field['name']}' => \$this->input->post('{$field['name']}', TRUE),\n";
        }
        $content .= "            );\n";
        
        // Fetch old data for file handling
        $content .= "            \$row = \$this->{$model_var}->get_by_id(\$this->input->post('$pk', TRUE));\n";
        
        // Add file upload handling for update
        foreach ($file_upload_fields as $fuf) {
            $content .= "            
            // Handle {$fuf['field']}
            \$old_files_{$fuf['field']} = json_decode(\$row->{$fuf['field']}, true) ?: [];
            if (!is_array(\$old_files_{$fuf['field']})) \$old_files_{$fuf['field']} = [\$row->{$fuf['field']}];
            
            // 1. Handle Removals
            \$removed_files_{$fuf['field']} = \$this->input->post('remove_files_{$fuf['field']}') ?: [];
            foreach (\$removed_files_{$fuf['field']} as \$rem_file) {
                \$key = array_search(\$rem_file, \$old_files_{$fuf['field']});
                if (\$key !== false) {
                    unset(\$old_files_{$fuf['field']}[\$key]);
                    // Delete file from server
                    \$path = './uploads/$table/' . \$rem_file;
                    if (file_exists(\$path)) @unlink(\$path);
                }
            }
            \$old_files_{$fuf['field']} = array_values(\$old_files_{$fuf['field']}); // Re-index
            
            // 2. Handle New Uploads
            \$multiple = " . ($fuf['multiple'] ? 'true' : 'false') . ";
            \$uploaded_{$fuf['field']} = \$this->_do_upload('{$fuf['field']}', \$multiple);
            \$new_files_{$fuf['field']} = [];
            
            if (!empty(\$uploaded_{$fuf['field']})) {
                 \$new_files_{$fuf['field']} = json_decode(\$uploaded_{$fuf['field']}, true) ?: [\$uploaded_{$fuf['field']}];
            }
            
            // 3. Merge
            \$final_files_{$fuf['field']} = array_merge(\$old_files_{$fuf['field']}, \$new_files_{$fuf['field']});
            
            // Encode back to JSON or String
            if (count(\$final_files_{$fuf['field']}) > 0) {
                \$data['{$fuf['field']}'] = json_encode(\$final_files_{$fuf['field']});
            } else {
                \$data['{$fuf['field']}'] = '';
            }
            ";
        }
        
        $content .= "            \$this->{$model_var}->update(\$this->input->post('$pk', TRUE), \$data);\n";
        
        // NOTIFICATION LOGIC FOR UPDATE ACTION
        if ($notification_type == 'bootstrap') {
            $content .= "            \$this->session->set_flashdata('message', '<div class=\"alert alert-success alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><i class=\"fa fa-check\"></i> Update Record Success</div>');\n";
        } else {
            $content .= "            \$this->session->set_flashdata('message', 'Update Record Success');\n";
            $content .= "            \$this->session->set_flashdata('message_type', 'success');\n";
        }

        $content .= "            redirect(site_url('$controller_name'));\n";
        $content .= "        }\n";
        $content .= "    }\n\n";
        
        // Delete
        $content .= "    public function delete(\$id)\n    {\n";
        $content .= "        \$this->rbac->require_permission('" . strtolower($controller_name) . ".delete');\n";
        $content .= "        \$row = \$this->{$model_var}->get_by_id(\$id);\n";
        $content .= "        if (\$row) {\n";
        $content .= "            \$this->{$model_var}->delete(\$id);\n";
        
        // NOTIFICATION LOGIC FOR DELETE
        if ($notification_type == 'bootstrap') {
             $content .= "            \$this->session->set_flashdata('message', '<div class=\"alert alert-success alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><i class=\"fa fa-check\"></i> Delete Record Success</div>');\n";
        } else {
             $content .= "            \$this->session->set_flashdata('message', 'Delete Record Success');\n";
             $content .= "            \$this->session->set_flashdata('message_type', 'success');\n";
        }

        $content .= "            redirect(site_url('$controller_name'));\n";
        $content .= "        } else {\n";
        
        // NOTIFICATION LOGIC FOR DELETE (NOT FOUND)
        if ($notification_type == 'bootstrap') {
             $content .= "            \$this->session->set_flashdata('message', '<div class=\"alert alert-danger alert-dismissible\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button><i class=\"fa fa-warning\"></i> Record Not Found</div>');\n";
        } else {
             $content .= "            \$this->session->set_flashdata('message', 'Record Not Found');\n";
             $content .= "            \$this->session->set_flashdata('message_type', 'warning');\n";
        }

        $content .= "            redirect(site_url('$controller_name'));\n";
        $content .= "        }\n";
        $content .= "    }\n\n";

        // Rules
        $content .= "    public function _rules()\n    {\n";
        foreach ($fields as $field) {
            // Skip rules for file upload fields (handled manually)
            $is_file_field = false;
            foreach ($file_upload_fields as $fuf) {
                if ($fuf['field'] === $field['name']) {
                    $is_file_field = true;
                    break;
                }
            }
            
            if ($is_file_field) {
                continue;
            }

            $rules = $this->generate_validation_rules($field);
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $content .= "        \$this->form_validation->set_rules('{$field['name']}', '$label', '$rules');\n";
        }
        $content .= "        \$this->form_validation->set_error_delimiters('<span class=\"text-danger\">', '</span>');\n";
        $content .= "    }\n";
        
        $content .= "}";

        return write_file(APPPATH . 'controllers/' . $controller_name . '.php', $content);
    }

    private function generate_views($table, $controller_name, $model_name, $field_configs = null, $notification_type = 'bootstrap')
    {
        $fields = $this->get_field_metadata($table);
        
        if ($fields === false || empty($fields)) {
            return false;
        }
        
        $pk = $fields[0]['name'];
        
        // Create views directory
        $view_dir = APPPATH . 'views/' . $table;
        if (!is_dir($view_dir)) {
            mkdir($view_dir, 0755, true);
        }
        
        // Generate List View
        $list_view = $this->generate_list_view($table, $controller_name, $fields, $field_configs, $notification_type);
        write_file($view_dir . '/list.php', $list_view);
        
        // Generate Form View with field configs
        $form_view = $this->generate_form_view($table, $controller_name, $fields, $field_configs);
        write_file($view_dir . '/form.php', $form_view);
        
        return true;
    }

    private function generate_list_view($table, $controller_name, $fields, $field_configs = null, $notification_type = 'bootstrap')
    {
        $pk = $fields[0]['name'];
        
        $content = '<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>' . ucwords(str_replace('_', ' ', $table)) . '</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url(\'superadmin\') ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>' . ucwords(str_replace('_', ' ', $table)) . '</strong>
            </li>
        </ol>
    </div>
</div>

<?php
// Check if there are image fields to include Lightbox assets
$has_image_fields = false;
?>
    <!-- Lightbox2 CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.css" />

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Data List</h5>
                        <div class="ibox-tools">
                            <?php if($this->rbac->can_access(\'' . $controller_name . '\', \'create\')): ?>
                                <a href="<?php echo site_url(\'' . $controller_name . '/create\') ?>" class="btn btn-primary btn-sm">
                                    <i class="fa fa-plus"></i> Add New
                                </a>
                            <?php endif; ?>
                        </div>
                </div>
                <div class="ibox-content">
                    ';

        // NOTIFICATION LOGIC FOR LIST VIEW
        if ($notification_type == 'bootstrap') {
            $content .= '<?php echo $this->session->flashdata(\'message\'); ?>';
        } elseif ($notification_type == 'sweetalert') {
            // SweetAlert uses the flash-data div to trigger
            $content .= '<div class="flash-data" data-flashdata="<?php echo $this->session->flashdata(\'message\'); ?>" data-type="<?php echo $this->session->flashdata(\'message_type\'); ?>"></div>';
            $content .= '<script>
            var flashData = $(".flash-data").data("flashdata");
            var flashType = $(".flash-data").data("type") || "success";
            if(flashData){
                Swal.fire({
                    title: flashType === "success" ? "Success" : "Warning",
                    text: flashData,
                    icon: flashType
                });
            }
            </script>';
        } elseif ($notification_type == 'izitoast') {
            // IziToast implementation
            $content .= '
                    <!-- IziToast CSS and JS -->
                    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css" />
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
                    
                    <?php if($this->session->flashdata(\'message\')): ?>
                    <script>
                        var messageType = \'<?php echo $this->session->flashdata(\'message_type\') ?: \'success\'; ?>\';
                        var message = \'<?php echo $this->session->flashdata(\'message\'); ?>\';
                        
                        if(messageType == \'success\') {
                            iziToast.success({
                                title: \'Success\',
                                message: message,
                                position: \'topRight\'
                            });
                        } else {
                            iziToast.warning({
                                title: \'Warning\',
                                message: message,
                                position: \'topRight\'
                            });
                        }
                    </script>
                    <?php endif; ?>
            ';
        }

        $content .= '
                    <div class="table-responsive">
                    
                    <!-- Lightbox2 Script -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/ekko-lightbox/5.3.0/ekko-lightbox.min.js"></script>
                    <script>
                    window.addEventListener(\'load\', function() {
                        $(document).on(\'click\', \'[data-toggle=\"lightbox\"]\', function(event) {
                            event.preventDefault();
                            $(this).ekkoLightbox();
                        });
                    });
                    </script>
                    
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>No</th>';
        
        foreach ($fields as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            $content .= "\n                                    <th>$label</th>";
        }
        
        $content .= '
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                foreach ($' . $table . '_data as $row): 
                                ?>
                                <tr>
                                    <td><?php echo $no++; ?></td>';
        
        foreach ($fields as $field) {
            // Check if this is a file/image field
            $is_file_field = false;
            if ($field_configs && isset($field_configs[$field['name']]['input_type']) && $field_configs[$field['name']]['input_type'] === 'file') {
                $is_file_field = true;
            }
            
            if ($is_file_field) {
                $content .= "\n                                    <td class=\"text-center\">
                                        <?php 
                                        \$file_data = \$row->{$field['name']};
                                        \$files = json_decode(\$file_data);
                                        if (!\$files) \$files = [\$file_data];
                                        \$file = \$files[0] ?? '';
                                        
                                        if (!empty(\$file)) {
                                            \$ext = strtolower(pathinfo(\$file, PATHINFO_EXTENSION));
                                            if (in_array(\$ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                // Create a unique gallery ID for this row to isolate navigation
                                                \$gallery_id = 'gallery-" . $field['name'] . "-' . \$row->" . $pk . ";
                                                
                                                // Loop through ALL files to add them to the gallery
                                                foreach (\$files as \$index => \$f) {
                                                    \$display_style = (\$index === 0) ? '' : 'display:none;';
                                                    \$img_url = base_url('uploads/" . $table . "/' . \$f);
                                                    
                                                    echo '<a href=\"' . \$img_url . '\" data-toggle=\"lightbox\" data-gallery=\"' . \$gallery_id . '\" data-max-width=\"600\" style=\"' . \$display_style . '\">';
                                                    if (\$index === 0) {
                                                        echo '<img src=\"' . \$img_url . '\" class=\"img-thumbnail\" style=\"max-height: 50px; max-width: 50px; object-fit: cover;\">';
                                                    }
                                                    echo '</a>';
                                                }
                                                
                                                if (count(\$files) > 1) {
                                                    echo '<span class=\"badge badge-info ml-1\">+' . (count(\$files) - 1) . '</span>';
                                                }
                                            } else {
                                                echo '<a href=\"' . base_url('uploads/" . $table . "/' . \$file) . '\" target=\"_blank\" class=\"btn btn-xs btn-default\"><i class=\"fa fa-file\"></i> File</a>';
                                            }
                                        } else {
                                            echo '-';
                                        }
                                        ?>
                                    </td>";
            } else {
                $content .= "\n                                    <td><?php echo \$row->{$field['name']}; ?></td>";
            }
        }
        
        $content .= '
                                    <td>
                                        <?php if($this->rbac->can_access(\'' . $controller_name . '\', \'update\')): ?>
                                            <a href="<?php echo site_url(\'' . $controller_name . '/update/\'.$row->' . $pk . ') ?>" class="btn btn-warning btn-xs">
                                                <i class="fa fa-pencil"></i> Edit
                                            </a>
                                        <?php endif; ?>
                                        <?php if($this->rbac->can_access(\'' . $controller_name . '\', \'delete\')): ?>
                                            <a href="<?php echo site_url(\'' . $controller_name . '/delete/\'.$row->' . $pk . ') ?>" class="btn btn-danger btn-xs tombol-hapus">
                                                <i class="fa fa-trash"></i> Delete
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>';
        
        return $content;
    }

    private function generate_form_view($table, $controller_name, $fields, $field_configs = null)
    {
        $pk = $fields[0]['name'];
        
        // Check if there are file upload fields to determine if we need enctype
        $has_file_fields = false;
        $file_fields = [];
        if ($field_configs) {
            foreach ($fields as $field) {
                if (isset($field_configs[$field['name']]['input_type']) && $field_configs[$field['name']]['input_type'] === 'file') {
                    $has_file_fields = true;
                    $file_fields[] = [
                        'name' => $field['name'],
                        'multiple' => isset($field_configs[$field['name']]['file_multiple']) && $field_configs[$field['name']]['file_multiple']
                    ];
                }
            }
        }
        
        if ($has_file_fields) {
            $form_open_tag = '<?php echo form_open_multipart($action); ?>';
        } else {
            $form_open_tag = '<?php echo form_open($action); ?>';
        }
        
        $content = '<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $title; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url(\'superadmin\') ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo site_url(\'' . $controller_name . '\') ?>">' . ucwords(str_replace('_', ' ', $table)) . '</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Form</strong>
            </li>
        </ol>
    </div>
</div>
';
        
        // Add Dropzone CSS if there are file fields
        if ($has_file_fields) {
            $content .= '
<link rel="stylesheet" href="<?php echo base_url(\'assets/template/css/plugins/dropzone/dropzone.css\'); ?>">
<style>
.dropzone-wrapper {
    border: 2px dashed #1ab394;
    border-radius: 5px;
    background: #f9f9f9;
    min-height: 150px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}
.dropzone-wrapper:hover {
    background: #e8f7f4;
    border-color: #18a689;
}
.dropzone-wrapper .dz-message {
    margin: 2em 0;
    color: #999;
}
.dropzone-wrapper .dz-message i {
    font-size: 48px;
    color: #1ab394;
    margin-bottom: 15px;
}
.file-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
    max-width: 100%;
}
.file-preview-item {
    position: relative;
    width: 120px;
    height: 120px;
    border: 1px solid #ddd;
    border-radius: 5px;
    overflow: hidden;
    background: #fff;
}
.file-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.file-preview-item .file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    font-size: 48px;
    color: #999;
}
.file-preview-item .remove-file {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #ed5565;
    color: #fff;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
}
.file-preview-item .file-name {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: rgba(0,0,0,0.7);
    color: #fff;
    padding: 3px 5px;
    font-size: 10px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.existing-files-label {
    color: #666;
    font-size: 12px;
    margin-bottom: 10px;
}
</style>
';
        }
        
        $content .= '
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox">
                <div class="ibox-title">
                    <h5><?php echo $title; ?></h5>
                </div>
                <div class="ibox-content">
                    ' . $form_open_tag . '
                        
';
        
        foreach ($fields as $field) {
            $label = ucwords(str_replace('_', ' ', $field['name']));
            
            // Use custom input type if provided, otherwise auto-detect
            if ($field_configs && isset($field_configs[$field['name']]['input_type'])) {
                $input_type = $field_configs[$field['name']]['input_type'];
            } else {
                $input_type = $this->determine_input_type($field);
            }
            
            $is_pk = $field['primary_key'] == 1;
            $has_fk = $field_configs && isset($field_configs[$field['name']]['has_fk']) && $field_configs[$field['name']]['has_fk'];
            
            // Check for readonly from config or if it's a primary key
            $readonly = '';
            if ($is_pk) {
                $readonly = ' readonly';
            } elseif ($field_configs && isset($field_configs[$field['name']]['readonly']) && $field_configs[$field['name']]['readonly']) {
                $readonly = ' readonly';
            }
            
            $content .= '                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">' . $label . '</label>
                            <div class="col-sm-10">';
            
            // Generate appropriate input based on type and FK configuration
            if ($input_type == 'file') {
                // File input with Dropzone-style preview
                $is_multiple = isset($field_configs[$field['name']]['file_multiple']) && $field_configs[$field['name']]['file_multiple'];
                $multiple_attr = $is_multiple ? ' multiple' : '';
                $field_name = $is_multiple ? $field['name'] . '[]' : $field['name'];
                
                $content .= '
                                <div class="dropzone-wrapper" id="dropzone_' . $field['name'] . '" onclick="document.getElementById(\'file_' . $field['name'] . '\').click();">
                                    <div class="dz-message">
                                        <i class="fa fa-cloud-upload"></i>
                                        <h4>Drag & drop files here or click to browse</h4>
                                        <p class="text-muted">' . ($is_multiple ? 'You can upload multiple files' : 'Single file upload') . '</p>
                                    </div>
                                </div>
                                <input type="file" class="form-control d-none" id="file_' . $field['name'] . '" name="' . $field_name . '"' . $multiple_attr . ' accept="image/*,.pdf,.doc,.docx,.xls,.xlsx" style="display:none;" onchange="previewFiles(this, \'' . $field['name'] . '\', ' . ($is_multiple ? 'true' : 'false') . ')">
                                <div id="preview_' . $field['name'] . '" class="file-preview-container"></div>
                                
                                <?php if (!empty($' . $field['name'] . ')): ?>
                                <div class="existing-files-label mt-3"><i class="fa fa-paperclip"></i> Existing file(s):</div>
                                <div id="existing_' . $field['name'] . '" class="file-preview-container">
                                    <?php 
                                    $files = json_decode($' . $field['name'] . ');
                                    if (!$files) $files = [$' . $field['name'] . '];
                                    foreach((array)$files as $file): 
                                        if (empty($file)) continue;
                                        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                        $is_image = in_array($ext, ["jpg", "jpeg", "png", "gif", "webp"]);
                                    ?>
                                    <div class="file-preview-item">
                                        <?php if ($is_image): ?>
                                            <img src="<?php echo base_url(\'uploads/' . $table . '/\'); ?><?php echo $file; ?>" alt="">
                                        <?php else: ?>
                                            <div class="file-icon"><i class="fa fa-file"></i></div>
                                        <?php endif; ?>
                                        <span class="file-name"><?php echo $file; ?></span>
                                        <button type="button" class="remove-file" onclick="removeExistingFile(this, \'' . $field['name'] . '\', \'<?php echo $file; ?>\')"><i class="fa fa-times"></i></button>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <!-- Container for removed files -->
                                <div id="removed_files_' . $field['name'] . '"></div>
                                <?php endif; ?>';
            } elseif ($input_type == 'textarea') {
                $content .= '
                                <textarea class="form-control" name="' . $field['name'] . '" rows="5"' . $readonly . '><?php echo $' . $field['name'] . '; ?></textarea>';
            } elseif ($input_type == 'select' || $has_fk) {
                // Generate select dropdown
                if ($has_fk && isset($field_configs[$field['name']]['fk_table'])) {
                    $fk_table = $field_configs[$field['name']]['fk_table'];
                    $fk_key = $field_configs[$field['name']]['fk_key'];
                    $fk_display = $field_configs[$field['name']]['fk_display'];
                    $fk_var = strtolower(str_replace('tbl_', '', $fk_table)) . '_data';
                    
                    $content .= '
                                <select class="form-control" name="' . $field['name'] . '"' . $readonly . '>
                                    <option value="">-- Select ' . $label . ' --</option>
                                    <?php foreach ($' . $fk_var . ' as $item): ?>
                                        <option value="<?php echo $item->' . $fk_key . '; ?>" <?php echo ($' . $field['name'] . ' == $item->' . $fk_key . ') ? \'selected\' : \'\'; ?>>
                                            <?php echo $item->' . $fk_display . '; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>';
                } else {
                    $content .= '
                                <select class="form-control" name="' . $field['name'] . '"' . $readonly . '>
                                    <option value="">-- Select ' . $label . ' --</option>
                                    <!-- Add options here -->
                                </select>';
                }
            } else {
                $content .= '
                                <input type="' . $input_type . '" class="form-control" name="' . $field['name'] . '" value="<?php echo $' . $field['name'] . '; ?>"' . $readonly . '>';
            }
            
            $content .= '
                                <?php echo form_error(\'' . $field['name'] . '\'); ?>
                            </div>
                        </div>
';
        }
        
        $content .= '                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label"></label>
                            <div class="col-sm-10">
                                <a href="<?php echo site_url(\'' . $controller_name . '\') ?>" class="btn btn-white">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>';
        
        // Add JavaScript for file preview if there are file fields
        if ($has_file_fields) {
            $content .= '

<script>
function removeExistingFile(btn, fieldName, fileName) {
    Swal.fire({
        title: \'Anda yakin?\',
        text: "File ini akan dihapus saat anda menyimpan perubahan!",
        icon: \'warning\',
        showCancelButton: true,
        confirmButtonColor: \'#d33\',
        cancelButtonColor: \'#3085d6\',
        confirmButtonText: \'Ya, hapus!\',
        cancelButtonText: \'Batal\'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create hidden input to track removal
            var input = document.createElement("input");
            input.type = "hidden";
            input.name = "remove_files_" + fieldName + "[]";
            input.value = fileName;
            
            document.getElementById("removed_files_" + fieldName).appendChild(input);
            
            // Remove the visual item
            btn.closest(".file-preview-item").remove();
            
            Swal.fire(
                \'Ditandai!\',
                \'File telah ditandai untuk dihapus.\',
                \'success\'
            )
        }
    })
}

function previewFiles(input, fieldName, isMultiple) {
    var previewContainer = document.getElementById("preview_" + fieldName);
    previewContainer.innerHTML = "";
    
    if (input.files && input.files.length > 0) {
        Array.from(input.files).forEach(function(file, index) {
            var reader = new FileReader();
            var isImage = file.type.startsWith("image/");
            
            var previewItem = document.createElement("div");
            previewItem.className = "file-preview-item";
            
            if (isImage) {
                reader.onload = function(e) {
                    var img = document.createElement("img");
                    img.src = e.target.result;
                    previewItem.appendChild(img);
                };
                reader.readAsDataURL(file);
            } else {
                var iconDiv = document.createElement("div");
                iconDiv.className = "file-icon";
                iconDiv.innerHTML = \'<i class="fa fa-file"></i>\';
                previewItem.appendChild(iconDiv);
            }
            
            var fileName = document.createElement("span");
            fileName.className = "file-name";
            fileName.textContent = file.name;
            previewItem.appendChild(fileName);
            
            var removeBtn = document.createElement("button");
            removeBtn.type = "button";
            removeBtn.className = "remove-file";
            removeBtn.innerHTML = \'<i class="fa fa-times"></i>\';
            removeBtn.onclick = function(e) {
                e.stopPropagation();
                previewItem.remove();
                // Note: Cannot remove individual files from FileList, user needs to re-select
            };
            previewItem.appendChild(removeBtn);
            
            previewContainer.appendChild(previewItem);
        });
    }
}

// Drag and drop functionality
document.addEventListener("DOMContentLoaded", function() {
    var dropzones = document.querySelectorAll(".dropzone-wrapper");
    
    dropzones.forEach(function(dropzone) {
        var fieldName = dropzone.id.replace("dropzone_", "");
        var fileInput = document.getElementById("file_" + fieldName);
        
        ["dragenter", "dragover", "dragleave", "drop"].forEach(function(eventName) {
            dropzone.addEventListener(eventName, function(e) {
                e.preventDefault();
                e.stopPropagation();
            }, false);
        });
        
        ["dragenter", "dragover"].forEach(function(eventName) {
            dropzone.addEventListener(eventName, function() {
                dropzone.style.background = "#e8f7f4";
                dropzone.style.borderColor = "#18a689";
            }, false);
        });
        
        ["dragleave", "drop"].forEach(function(eventName) {
            dropzone.addEventListener(eventName, function() {
                dropzone.style.background = "#f9f9f9";
                dropzone.style.borderColor = "#1ab394";
            }, false);
        });
        
        dropzone.addEventListener("drop", function(e) {
            var dt = e.dataTransfer;
            var files = dt.files;
            fileInput.files = files;
            previewFiles(fileInput, fieldName, fileInput.hasAttribute("multiple"));
        }, false);
    });
});
</script>';
        }
        
        return $content;
    }

    // =============================================
    // CRUD History Management Methods
    // =============================================
    
    private function save_crud_history($table_name, $controller_name, $model_name, $field_configs = null, $notification_type = 'bootstrap', $id = null)
    {
        $data = [
            'table_name' => $table_name,
            'controller_name' => $controller_name,
            'model_name' => $model_name,
            'view_directory' => APPPATH . 'views' . DIRECTORY_SEPARATOR . $table_name,
            'generated_at' => date('Y-m-d H:i:s'),
            'generated_by' => $this->session->userdata('id_user'),
            'field_configs' => $field_configs ? json_encode($field_configs) : null,
            'notification_type' => $notification_type
        ];
        
        if ($id) {
            $this->db->where('id', $id);
            $this->db->update('tbl_crud_history', $data);
        } else {
            $this->db->insert('tbl_crud_history', $data);
        }
    }
    
    public function crud_history()
    {
        $data['title'] = 'CRUD History';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        
        // Get all CRUD history with user information
        $this->db->select('tbl_crud_history.*, mst_user.nama as generated_by_name');
        $this->db->from('tbl_crud_history');
        $this->db->join('mst_user', 'tbl_crud_history.generated_by = mst_user.id_user', 'left');
        $this->db->order_by('tbl_crud_history.generated_at', 'DESC');
        $crud_history = $this->db->get()->result();
        
        // Normalize paths for cross-platform compatibility
        foreach ($crud_history as $row) {
            $row->view_directory = str_replace(['/', '\\\\'], DIRECTORY_SEPARATOR, $row->view_directory);
        }
        
        $data['crud_history'] = $crud_history;
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('crud_generator/history', $data);
        $this->load->view('templates/footer');
    }
    
    public function get_crud_files($id)
    {
        $history = $this->db->get_where('tbl_crud_history', ['id' => $id])->row();
        
        if (!$history) {
            echo json_encode(['success' => false, 'message' => 'History not found']);
            return;
        }
        
        $files = [
            'controller' => APPPATH . 'controllers/' . $history->controller_name . '.php',
            'model' => APPPATH . 'models/' . $history->model_name . '.php',
            'list_view' => $history->view_directory . '/list.php',
            'form_view' => $history->view_directory . '/form.php'
        ];
        
        // Check which files exist
        $existing_files = [];
        foreach ($files as $type => $path) {
            if (file_exists($path)) {
                $existing_files[$type] = $path;
            }
        }
        
        echo json_encode(['success' => true, 'files' => $existing_files]);
    }
    
    public function delete_crud_history($id)
    {
        $history = $this->db->get_where('tbl_crud_history', ['id' => $id])->row();
        
        if ($history) {
            $this->db->where('id', $id);
            $this->db->delete('tbl_crud_history');
            $this->session->set_flashdata('message', '<div class="alert alert-success">CRUD history deleted successfully!</div>');
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger">History record not found!</div>');
        }
        
        redirect('CrudGenerator/crud_history');
    }
}
