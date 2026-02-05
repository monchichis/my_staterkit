<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Chart_api extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->model('Chart_model');
        $this->load->model('Rbac_model');
        
        if (!$this->session->userdata('id_user')) {
            show_error('Unauthorized', 401);
        }
    }

    public function get_data($id)
    {
        $chart = $this->Chart_model->get_chart($id);
        if (!$chart) {
            show_404();
            return;
        }

        // Security Check: Is user allowed?
        $allowed_roles = json_decode($chart->allowed_roles, true);
        if (!is_array($allowed_roles)) $allowed_roles = [];
        
        $user_id = $this->session->userdata('id_user');
        $user_roles = $this->Rbac_model->get_user_role_ids($user_id);
        
        // Check intersection
        $has_access = !empty(array_intersect($allowed_roles, $user_roles));
        
        if (!$has_access) {
             show_error('Forbidden', 403);
             return;
        }

        // Fetch Data
        $config = json_decode($chart->data_config, true);
        
        $filters = $this->input->post('filters');
        if (!is_array($filters)) $filters = [];
        
        $data = $this->Chart_model->fetch_chart_data($config, $filters);
        
        // Return new CSRF Hash for next request
        $data['csrfHash'] = $this->security->get_csrf_hash();
        
        echo json_encode($data);
    }
}
