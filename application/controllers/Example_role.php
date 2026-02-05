<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Example Role Controller Template
 * 
 * This is a template for creating new role-based controllers.
 * Simply copy this file and rename it to match your role name.
 * 
 * Example: For role "Manager", create "Manager.php"
 * 
 * The controller will automatically:
 * - Fetch charts assigned to the role
 * - Fetch summary widgets assigned to the role
 * - Display them on the dashboard
 */
class Example_role extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required libraries
        $this->load->library('session');
        $this->load->database();
        $this->load->library('rbac_lib', null, 'rbac');
        
        // Check if user is logged in
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
        
        // Optional: Add role-specific access control here
        // $this->rbac->check_access_throw('Example Role');
    }

    public function index()
    {
        $id_user = $this->session->userdata('id_user');
        
        // Prepare basic data
        $data['title'] = 'Dashboard'; // Change this to your role's dashboard title
        $data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['identitas'] = $this->db->get('tbl_aplikasi')->row();
        
        // AUTOMATIC: Fetch Dashboard Components (Charts & Widgets)
        // This single line automatically fetches all charts and widgets
        // assigned to this user's roles for the dashboard placement
        $dashboard_components = $this->_get_dashboard_components('dashboard');
        $data['charts'] = $dashboard_components['charts'];
        $data['summary_widgets'] = $dashboard_components['summary_widgets'];
        
        // Load views
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_example_role', $data); // Create your own sidebar
        $this->load->view('example_role/index', $data); // Create your own dashboard view
        $this->load->view('templates/footer');
        $this->load->view('superadmin/summary_widget/detail_modal');
        $this->load->view('superadmin/summary_widget/detail_modal_js');
    }
}
