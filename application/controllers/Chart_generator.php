<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chart_generator extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required libraries
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->database();
        
        $this->load->model('Rbac_model');
        $this->load->model('Chart_model');
        
        // Check if user is logged in (uses helper function)
        is_logged_in();
        
        // Redirect if not Super Admin (access control)
        if ($this->session->userdata('level') != 'Super Admin') {
            redirect('auth/blocked');
        }
    }

    public function index()
    {
        $data['title'] = 'Chart Generator';
        $data['charts'] = $this->Chart_model->get_charts();
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$data['list_user'] = $this->db->get('mst_user')->result_array();
		$data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data); // Assuming Superadmin for now, or dynamic
        $this->load->view('superadmin/chart_gen/index', $data);
        $this->load->view('templates/footer');
    }

    public function add()
    {
        $data['title'] = 'Add New Chart';
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$data['list_user'] = $this->db->get('mst_user')->result_array();
		$data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        $data['tables'] = $this->Chart_model->get_available_tables();
        $data['roles'] = $this->Rbac_model->get_active_roles();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/chart_gen/form', $data);
        $this->load->view('templates/footer');
    }
    
    public function edit($id)
    {
        $data['title'] = 'Edit Chart';
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$data['list_user'] = $this->db->get('mst_user')->result_array();
		$data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        $data['chart'] = $this->Chart_model->get_chart($id);
        $data['tables'] = $this->Chart_model->get_available_tables();
        $data['roles'] = $this->Rbac_model->get_active_roles();
        
        if (!$data['chart']) show_404();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/chart_gen/form', $data);
        $this->load->view('templates/footer');
    }

    public function save()
    {
        // Validation would go here
        
        $data = [
            'chart_title' => $this->input->post('chart_title'),
            'chart_type' => $this->input->post('chart_type'),
            'placement_identifier' => $this->input->post('placement_identifier'),
            'allowed_roles' => json_encode($this->input->post('allowed_roles')),
            'data_config' => $this->input->post('data_config'), // JSON string from frontend builder
            'is_active' => $this->input->post('is_active') ? 1 : 0
        ];
        
        $id = $this->input->post('id');
        
        if ($id) {
            $this->Chart_model->update_chart($id, $data);
        } else {
            $this->Chart_model->create_chart($data);
        }
        
        redirect('superadmin/chart_generator');
    }
    
    public function delete($id)
    {
        $this->Chart_model->delete_chart($id);
        redirect('superadmin/chart_generator');
    }

    // AJAX Endpoint for getting columns
    public function get_table_columns()
    {
        $table = $this->input->post('table');
        $columns = $this->Chart_model->get_table_columns($table);
        
        // Include new CSRF hash for token regeneration
        $response = [
            'columns' => $columns,
            'csrfHash' => $this->security->get_csrf_hash()
        ];
        echo json_encode($response);
    }

    // AJAX Endpoint for Preview
    public function preview_chart()
    {
        $config_json = $this->input->post('config');
        $config = json_decode($config_json, true);
        
        $filters = $this->input->post('filters');
        // Fallback or check for JSON specific input
        if (is_string($filters)) {
            $decoded = json_decode($filters, true);
            if (is_array($decoded)) $filters = $decoded;
        }
        
        if (!is_array($filters)) $filters = [];
        
        $chart_data = $this->Chart_model->fetch_chart_data($config, $filters);
        
        // Include new CSRF hash for token regeneration
        $chart_data['csrfHash'] = $this->security->get_csrf_hash();
        
        echo json_encode($chart_data);
    }
}
