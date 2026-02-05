<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Install extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        
        // Only load database if already installed (for complete page)
        if ($this->is_installed() && $this->uri->segment(2) === 'complete') {
            $this->load->database();
        }
        
        // Check if already installed (except for reinstall)
        if ($this->is_installed() && $this->uri->segment(2) !== 'reinstall') {
            redirect('auth');
        }
    }

    /**
     * Main installer page - redirects to step 1
     */
    public function index()
    {
        redirect('install/step1');
    }

    /**
     * Step 1: Database Configuration
     */
    public function step1()
    {
        $data['title'] = 'Step 1: Database Configuration';
        $data['step'] = 1;
        
        $this->load->view('installer/header', $data);
        $this->load->view('installer/step1', $data);
        $this->load->view('installer/footer');
    }

    /**
     * Test database connection (AJAX)
     */
    public function test_connection()
    {
        $this->load->model('Install_model');
        
        $hostname = $this->input->post('hostname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $database = $this->input->post('database');
        
        $result = $this->Install_model->test_db_connection($hostname, $username, $password, $database);
        
        echo json_encode($result);
    }

    /**
     * Create database (AJAX/Form Submit)
     */
    public function create_database()
    {
        $this->load->model('Install_model');
        
        $hostname = $this->input->post('hostname');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $database = $this->input->post('database');
        
        // Test connection first
        $test = $this->Install_model->test_db_connection($hostname, $username, $password);
        
        if (!$test['status']) {
            echo json_encode([
                'status' => false,
                'message' => $test['message']
            ]);
            return;
        }
        
        // Create database
        $result = $this->Install_model->create_database($hostname, $username, $password, $database);
        
        if ($result['status']) {
            // Save to session for next step
            $this->session->set_userdata([
                'install_db_hostname' => $hostname,
                'install_db_username' => $username,
                'install_db_password' => $password,
                'install_db_database' => $database
            ]);
            
            // Save database config to file
            $this->Install_model->save_db_config($hostname, $username, $password, $database);
            
            echo json_encode([
                'status' => true,
                'message' => $result['message']
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Step 2: Import SQL Schema
     */
    public function step2()
    {
        // Check if step 1 completed
        if (!$this->session->userdata('install_db_database')) {
            redirect('install/step1');
        }
        
        $data['title'] = 'Step 2: Import Database Schema';
        $data['step'] = 2;
        
        $this->load->view('installer/header', $data);
        $this->load->view('installer/step2', $data);
        $this->load->view('installer/footer');
    }

    /**
     * Import SQL file (AJAX)
     */
    public function import_sql()
    {
        // Load database connection (database should exist now from step 1)
        $this->load->database();
        
        $this->load->model('Install_model');
        
        $sql_file = FCPATH . 'installer_rbac_schema.sql';
        
        if (!file_exists($sql_file)) {
            echo json_encode([
                'status' => false,
                'message' => 'SQL file not found: ' . $sql_file
            ]);
            return;
        }
        
        $result = $this->Install_model->execute_sql_file($sql_file);
        
        if ($result['status']) {
            $this->session->set_userdata('install_sql_imported', true);
        }
        
        echo json_encode($result);
    }

    /**
     * Step 3: Application Configuration
     */
    public function step3()
    {
        // Check if previous steps completed
        if (!$this->session->userdata('install_db_database') || !$this->session->userdata('install_sql_imported')) {
            redirect('install/step1');
        }
        
        $data['title'] = 'Step 3: Application Setup';
        $data['step'] = 3;
        
        $this->load->view('installer/header', $data);
        $this->load->view('installer/step3', $data);
        $this->load->view('installer/footer');
    }

    public function finalize()
    {
        // Load database now (after SQL import is complete)
        $this->load->database();
        
        $this->load->model('Install_model');
        
        // Validate form
        $this->form_validation->set_rules('nama_aplikasi', 'Application Name', 'required');
        $this->form_validation->set_rules('alamat', 'Address', 'required');
        $this->form_validation->set_rules('telp', 'Phone', 'required');
        $this->form_validation->set_rules('nama_developer', 'Developer Name', 'required');
        $this->form_validation->set_rules('admin_name', 'Admin Name', 'required');
        $this->form_validation->set_rules('admin_email', 'Admin Email', 'required|valid_email');
        $this->form_validation->set_rules('admin_password', 'Admin Password', 'required|min_length[6]');
        $this->form_validation->set_rules('admin_nik', 'Admin NIK', 'required');
        $this->form_validation->set_rules('uninstall_secret_key', 'Uninstall Secret Key', 'required|min_length[8]');
        $this->form_validation->set_rules('default_timezone', 'Default Timezone', 'required');
        $this->form_validation->set_rules('session_timeout', 'Session Timeout', 'required|numeric');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('install/step3');
        }
        
        // Handle logo upload
        $logo = 'default-logo.png';
        if (!empty($_FILES['logo']['name'])) {
            $config['upload_path'] = './assets/images/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg';
            $config['max_size'] = 2048;
            $config['file_name'] = 'logo_' . time();
            
            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('logo')) {
                $upload_data = $this->upload->data();
                $logo = $upload_data['file_name'];
            }
        }

        // Handle title icon upload
        $title_icon = 'default-icon.png';
        if (!empty($_FILES['title_icon']['name'])) {
            // Need to initialize upload lib again or clear config if reusing
            $config_icon['upload_path'] = './assets/images/';
            $config_icon['allowed_types'] = 'gif|jpg|png|jpeg|ico'; // Added ico
            $config_icon['max_size'] = 1024; // 1MB for icon
            $config_icon['file_name'] = 'icon_' . time();

            // Should initialize with new config. Since 'upload' lib is already loaded, use initialize
            $this->upload->initialize($config_icon);

            if ($this->upload->do_upload('title_icon')) {
                $upload_icon_data = $this->upload->data();
                $title_icon = $upload_icon_data['file_name'];
            }
        }
        
        // Save application settings
        $app_data = [
            'nama_aplikasi' => $this->input->post('nama_aplikasi'),
            'alamat' => $this->input->post('alamat'),
            'telp' => $this->input->post('telp'),
            'nama_developer' => $this->input->post('nama_developer'),
            'logo' => $logo,
            'title_icon' => $title_icon,
            'uninstall_secret_key' => password_hash($this->input->post('uninstall_secret_key'), PASSWORD_DEFAULT),
            'default_timezone' => $this->input->post('default_timezone'),
            'session_timeout' => $this->input->post('session_timeout')
        ];
        $this->db->insert('tbl_aplikasi', $app_data);
        
        // Create default admin user
        $admin_data = [
            'nama' => $this->input->post('admin_name'),
            'nik' => $this->input->post('admin_nik'),
            'email' => $this->input->post('admin_email'),
            'password' => password_hash($this->input->post('admin_password'), PASSWORD_DEFAULT),
            'level' => 'Super Admin',
            'date_created' => date('Y-m-d'),
            'image' => 'default.png',
            'is_active' => 1
        ];
        $this->db->insert('mst_user', $admin_data);
        $admin_id = $this->db->insert_id();
        
        // Assign Super Admin role to admin user (role_id = 1)
        $this->db->insert('tbl_user_roles', [
            'user_id' => $admin_id,
            'role_id' => 1
        ]);
        
        // Create installation lock file
        $this->Install_model->create_lock_file();
        
        // Clear session
        $this->session->unset_userdata(['install_db_hostname', 'install_db_username', 'install_db_password', 'install_db_database', 'install_sql_imported']);
        
        // Redirect to completion page
        redirect('install/complete');
    }

    /**
     * Installation complete page
     */
    public function complete()
    {
        $data['title'] = 'Installation Complete';
        $data['step'] = 4;
        
        $this->load->view('installer/header', $data);
        $this->load->view('installer/complete', $data);
        $this->load->view('installer/footer');
    }

    /**
     * Check if system is already installed
     */
    private function is_installed()
    {
        $lock_file = APPPATH . 'config/installed.lock';
        return file_exists($lock_file);
    }

    /*
     * Reinstall option removed for security.
     * To reinstall, manually delete application/config/installed.lock
     */
}
