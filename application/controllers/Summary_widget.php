<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Summary_widget extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $this->load->database();
        $this->load->library('form_validation');
        $this->load->model('Summary_model');
        $this->load->model('Rbac_model');
        
        // Ensure user is logged in
        if (!$this->session->userdata('id_user')) {
            redirect('auth/login');
        }
        
        // Ensure user is Superadmin or has permission (assuming superadmin for now based on path)
        // Adjust role check as needed for the specific system
    }

    public function index()
    {
        $data['title'] = 'Summary Widgets';
        $data['widgets'] = $this->Summary_model->get_all_widgets();
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['list_user'] = $this->db->get('mst_user')->result_array();
        $data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/summary_widget/list', $data);
        $this->load->view('templates/footer');
        $this->load->view('superadmin/summary_widget/list_js');
    }

    public function create()
    {
        $data['title'] = 'Create Summary Widget';
        $data['tables'] = $this->Summary_model->get_available_tables();
        $data['roles'] = $this->Rbac_model->get_all_roles();
         $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['list_user'] = $this->db->get('mst_user')->result_array();
        $data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/summary_widget/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('superadmin/summary_widget/form_js', $data);
    }

    public function edit($id)
    {
        $data['title'] = 'Edit Summary Widget';
        $data['widget'] = $this->Summary_model->get_widget($id);
        $data['tables'] = $this->Summary_model->get_available_tables();
        $data['roles'] = $this->Rbac_model->get_all_roles();
         $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['list_user'] = $this->db->get('mst_user')->result_array();
        $data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/summary_widget/form', $data);
        $this->load->view('templates/footer');
        $this->load->view('superadmin/summary_widget/form_js', $data);
    }

    public function detail($id)
    {
        // Check if AJAX request for modal
        if ($this->input->is_ajax_request()) {
            $widget = $this->Summary_model->get_widget($id);
            if (!$widget) {
                echo json_encode(['error' => 'Widget not found']);
                return;
            }

            $detail_data = $this->Summary_model->get_detail_data_by_aggregate($id);
            
            echo json_encode([
                'csrfHash' => $this->security->get_csrf_hash(),
                'widget' => $widget,
                'data' => $detail_data
            ]);
            return;
        }

        // Fallback to old page view if not AJAX
        $data['title'] = 'Widget Data Detail';
        $data['widget'] = $this->Summary_model->get_widget($id);
        $data['rows'] = $this->Summary_model->get_detail_data($id);
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['list_user'] = $this->db->get('mst_user')->result_array();
        $data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('superadmin/summary_widget/detail', $data);
        $this->load->view('templates/footer');
    }
    
    public function save()
    {
        if ($this->input->method() === 'post') {
            $id = $this->input->post('id');
            
            $allowed_roles = $this->input->post('allowed_roles');
            $roles_json = json_encode($allowed_roles ? $allowed_roles : []);
            
            $data = [
                'title' => $this->input->post('title'),
                'table_name' => $this->input->post('table_name'),
                'column_name' => $this->input->post('column_name'),
                'aggregate_func' => $this->input->post('aggregate_func'),
                'bg_color_class' => $this->input->post('bg_color_class'),
                'formatting' => $this->input->post('formatting'),
                'placement' => $this->input->post('placement'),
                'allowed_roles' => $roles_json,
                'is_active' => $this->input->post('is_active') ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (!$id) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }

            $this->Summary_model->save($data, $id);
            $this->session->set_flashdata('message', 'Widget saved successfully');
            redirect('summary_widget');
        }
    }

    public function delete($id)
    {
        $this->Summary_model->delete($id);
        $this->session->set_flashdata('message', 'Widget deleted successfully');
        redirect('summary_widget');
    }

    public function get_table_columns()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $table = $this->input->post('table');
        $columns = $this->Summary_model->get_table_columns($table);
        
        echo json_encode([
            'csrfHash' => $this->security->get_csrf_hash(),
            'columns' => $columns
        ]);
    }

    public function preview_value()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $table = $this->input->post('table');
        $column = $this->input->post('column');
        $agg = $this->input->post('agg');
        $format = $this->input->post('formatting');

        $val = $this->Summary_model->get_aggregate_value($table, $column, $agg);

        if ($format == 'rupiah') {
            $val = rupiah($val);
        }

        echo json_encode([
            'csrfHash' => $this->security->get_csrf_hash(),
            'value' => $val
        ]);
    }
}
