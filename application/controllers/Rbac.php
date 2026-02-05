<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rbac extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load session first (not autoloaded anymore due to wrapper fix)
        $this->load->library('session');
        
        // Load database
        $this->load->database();
        
        // Load form validation library
        $this->load->library('form_validation');
        
        $this->load->model('Rbac_model');
        $this->load->library('rbac_lib', null, 'rbac');
        $this->load->helper('rbac_helper');
        
        // Check if user is logged in
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
    }
    
    // ========== ROLES MANAGEMENT ==========
    
    public function roles()
    {
        $this->rbac->require_permission('rbac.roles.view');
        
        $data['title'] = 'Roles Management';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['roles'] = $this->Rbac_model->get_all_roles();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/roles', $data);
        $this->load->view('templates/footer');
    }
    
    public function add_role()
    {
        $this->rbac->require_permission('rbac.roles.manage');
        
        $this->form_validation->set_rules('role_name', 'Role Name', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'role_name' => $this->input->post('role_name'),
                'role_description' => $this->input->post('role_description'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->create_role($data);
            
            // Create folder and sidebar for the new role
            $role_name = $this->input->post('role_name');
            $folder_name = strtolower(str_replace(' ', '_', $role_name));
            $controller_name = ucfirst(str_replace(' ', '', $role_name)); // Ensure CamelCase for controller class
            
            // 1. Create view folder
            $view_folder_path = APPPATH . 'views/' . $folder_name;
            if (!is_dir($view_folder_path)) {
                mkdir($view_folder_path, 0755, true);

                // Create a basic index view as well
                $view_content = "<div class=\"flash-data\" data-flashdata=\"<?php echo \$this->session->flashdata('message'); ?>\"></div>
<div class=\"row wrapper border-bottom white-bg page-heading\">
    <div class=\"col-lg-10\">
        <h2><?php echo \$title; ?></h2>
        <ol class=\"breadcrumb\">
            <li class=\"breadcrumb-item\">
                <a href=\"#\">Home</a>
            </li>
            <li class=\"breadcrumb-item\">
                <a><?php echo \$title; ?></a>
            </li>
            
        </ol>
    </div>
</div>
<br/>
<div class=\"wrapper wrapper-content animated fadeInRight\">
    <div class=\"row\">
        <div class=\"col-lg-12\">
            <div class=\"ibox \">
                <div class=\"ibox-title\">
                    <h5>Welcome to " . $role_name . " Dashboard</h5>
                </div>
                <div class=\"ibox-content\">
                    <p>
                        You are logged in as <strong>" . $role_name . "</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>";
                file_put_contents($view_folder_path . '/index.php', $view_content);
            }
            
            // 2. Create sidebar file from template
            $sidebar_template_path = APPPATH . 'views/templates/sidebar_template.php';
            $sidebar_file_path = APPPATH . 'views/templates/sidebar_' . $folder_name . '.php';
            
            if (file_exists($sidebar_template_path)) {
                $sidebar_content = file_get_contents($sidebar_template_path);
                // Replace placeholder with actual controller name
                $sidebar_content = str_replace('ROLE_CONTROLLER', $folder_name, $sidebar_content); // Assuming url uses folder_name/slug
                file_put_contents($sidebar_file_path, $sidebar_content);
            }

            // 3. Create Controller File
            $controller_content = "<?php
defined('BASEPATH') or exit('No direct script access allowed');

class " . $controller_name . " extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load session and database
        \$this->load->library('session');
        \$this->load->database();
        \$this->load->library('rbac_lib', null, 'rbac');
        
        // Check login and role
        if (!\$this->session->userdata('id_user')) {
             redirect('auth');
        }
        // Optional: Check specific role permission if strictly enforced
        // \$this->rbac->check_access_throw('" . $role_name . "'); 
    }

    public function index()
    {
        \$id_user = \$this->session->userdata('id_user');
        \$data['title'] = 'Beranda';
        \$data['user'] = \$this->db->get_where('mst_user', ['email' => \$this->session->userdata('email')])->row_array();
        \$data['list_user'] = \$this->db->get('mst_user')->result_array();
        \$data['identitas'] = \$this->db->get('tbl_aplikasi')->row();

        \$this->load->view('templates/header', \$data);
        \$this->load->view('templates/sidebar_" . $folder_name . "', \$data);
        \$this->load->view('" . $folder_name . "/index', \$data);
        \$this->load->view('templates/footer');
    }
}
";
            $controller_file_path = APPPATH . 'controllers/' . $controller_name . '.php';
            file_put_contents($controller_file_path, $controller_content);
            
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Role created successfully! Controller, View, and Sidebar created.</div>');
        }
        
        redirect('rbac/roles');
    }
    
    public function edit_role($id_role)
    {
        $this->rbac->require_permission('rbac.roles.manage');
        
        $this->form_validation->set_rules('role_name', 'Role Name', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'role_name' => $this->input->post('role_name'),
                'role_description' => $this->input->post('role_description'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->update_role($id_role, $data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Role updated successfully!</div>');
        }
        
        redirect('rbac/roles');
    }
    
    public function delete_role($id_role)
    {
        $this->rbac->require_permission('rbac.roles.manage');
        
        $this->Rbac_model->delete_role($id_role);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Role deleted successfully!</div>');
        redirect('rbac/roles');
    }
    
    // ========== PERMISSIONS MANAGEMENT ==========
    
    public function permissions()
    {
        $this->rbac->require_permission('rbac.permissions.view');
        
        $data['title'] = 'Permissions Management';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['permissions'] = $this->Rbac_model->get_permissions_by_module();
        $data['modules'] = $this->Rbac_model->get_all_modules();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/permissions', $data);
        $this->load->view('templates/footer');
    }
    
    public function add_permission()
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('permission_name', 'Permission Name', 'required|trim');
        $this->form_validation->set_rules('permission_key', 'Permission Key', 'required|trim|is_unique[mst_permissions.permission_key]');
        $this->form_validation->set_rules('module_id', 'Module', 'required');
        
        // Custom message for is_unique
        $this->form_validation->set_message('is_unique', '{field} already exists. Please use a different key.');

        if ($this->form_validation->run() == FALSE) {
             // Check if it failed specifically because of is_unique
             if (form_error('permission_key') && strpos(form_error('permission_key'), 'already exists') !== false) {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger">Data already exists! Permission key must be unique.</div>');
                // Redirect to index as requested
                redirect('rbac/permissions');
                return;
             }

            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'permission_name' => $this->input->post('permission_name'),
                'permission_key' => $this->input->post('permission_key'),
                'permission_description' => $this->input->post('permission_description'),
                'module_id' => $this->input->post('module_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->create_permission($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Permission created successfully!</div>');
        }
        
        redirect('rbac/permissions');
    }

    public function edit_permission($id_permission)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('permission_name', 'Permission Name', 'required|trim');
        
        // Custom callback or manual check for uniqueness on edit involves ignoring current ID
        // Simplified: check if key changed, then check uniqueness
        $original_key = $this->db->get_where('mst_permissions', ['id_permission' => $id_permission])->row()->permission_key;
        $new_key = $this->input->post('permission_key');
        
        if ($original_key != $new_key) {
             $this->form_validation->set_rules('permission_key', 'Permission Key', 'required|trim|is_unique[mst_permissions.permission_key]');
        } else {
             $this->form_validation->set_rules('permission_key', 'Permission Key', 'required|trim');
        }
        
        $this->form_validation->set_rules('module_id', 'Module', 'required');
        
        $this->form_validation->set_message('is_unique', '{field} already exists. Please use a different key.');

        if ($this->form_validation->run() == FALSE) {
             // Check if it failed specifically because of is_unique
             if (form_error('permission_key') && strpos(form_error('permission_key'), 'already exists') !== false) {
                 $this->session->set_flashdata('msg', '<div class="alert alert-danger">Data already exists! Permission key must be unique.</div>');
                 // Redirect to index as requested
                 redirect('rbac/permissions');
                 return;
             }

            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'permission_name' => $this->input->post('permission_name'),
                'permission_key' => $this->input->post('permission_key'),
                'permission_description' => $this->input->post('permission_description'),
                'module_id' => $this->input->post('module_id'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->update_permission($id_permission, $data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Permission updated successfully!</div>');
        }
        
        redirect('rbac/permissions');
    }
    
    public function delete_permission($id_permission)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->Rbac_model->delete_permission($id_permission);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Permission deleted successfully!</div>');
        redirect('rbac/permissions');
    }
    
    // ========== PERMISSION ASSIGNMENT ==========
    
    public function assign_permissions()
    {
        $this->rbac->require_permission('rbac.assign.permissions');
        
        $data['title'] = 'Assign Permissions to Roles';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['matrix'] = $this->Rbac_model->get_permission_matrix();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/assign_permissions', $data);
        $this->load->view('templates/footer');
    }
    
    public function save_permissions()
    {
        $this->rbac->require_permission('rbac.assign.permissions');
        
        $role_id = $this->input->post('role_id');
        $permissions = $this->input->post('permissions');
        
        if (!$permissions) {
            $permissions = [];
        }
        
        $this->Rbac_model->sync_role_permissions($role_id, $permissions);
        
        // Refresh permissions cache so changes take effect immediately
        $this->rbac->refresh_permissions();
        
        echo json_encode([
            'status' => true, 
            'message' => 'Permissions updated successfully!',
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    // ========== USER-ROLE ASSIGNMENT ==========
    
    public function assign_user_roles()
    {
        $this->rbac->require_permission('rbac.assign.roles');
        
        $data['title'] = 'Assign Roles to Users';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['users'] = $this->db->get('mst_user')->result();
        $data['roles'] = $this->Rbac_model->get_roles_with_permission_count();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/assign_user_roles', $data);
        $this->load->view('templates/footer');
    }
    
    public function get_user_roles()
    {
        $user_id = $this->input->post('user_id');
        $roles = $this->Rbac_model->get_user_role_ids($user_id);
        
        echo json_encode([
            'status' => true, 
            'roles' => $roles,
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    public function save_user_roles()
    {
        $this->rbac->require_permission('rbac.assign.roles');
        
        $user_id = $this->input->post('user_id');
        $roles = $this->input->post('roles');
        
        if (!$roles) {
            $roles = [];
        }

        // Validate roles have permissions
        if (!empty($roles)) {
            foreach ($roles as $role_id) {
                // Get role permission count
                $role_data = $this->Rbac_model->get_role($role_id);
                $role_permissions = $this->Rbac_model->get_role_permissions($role_id);
                
                if (empty($role_permissions)) {
                    echo json_encode([
                        'status' => false, 
                        'message' => 'Role "' . $role_data->role_name . '" does not have any permissions assigned. Please assign permissions to this role first.',
                        'csrfHash' => $this->security->get_csrf_hash()
                    ]);
                    return;
                }
            }
        }
        
        $this->Rbac_model->sync_user_roles($user_id, $roles);
        
        echo json_encode([
            'status' => true, 
            'message' => 'User roles updated successfully!',
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    // ========== MODULES MANAGEMENT ==========
    
    public function modules()
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $data['title'] = 'Modules Management';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['modules'] = $this->Rbac_model->get_all_modules();
        $data['parent_menus'] = $this->Rbac_model->get_all_parent_menus();
        $data['generated_controllers'] = $this->db->get('tbl_crud_history')->result();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/modules', $data);
        $this->load->view('templates/footer');
    }
    
    public function add_module()
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('module_name', 'Module Name', 'required|trim');
        $this->form_validation->set_rules('controller_name', 'Controller Name', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'module_name' => $this->input->post('module_name'),
                'module_description' => $this->input->post('module_description'),
                'controller_name' => $this->input->post('controller_name'),
                'icon' => $this->input->post('icon'),
                'parent_id' => $this->input->post('parent_id') ? $this->input->post('parent_id') : null,
                'parent_menu_id' => $this->input->post('parent_menu_id') ? $this->input->post('parent_menu_id') : null,
                'sort_order' => $this->input->post('sort_order'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->create_module($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Module created successfully!</div>');
        }
        
        redirect('rbac/modules');
    }

    public function edit_module($id_module)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('module_name', 'Module Name', 'required|trim');
        $this->form_validation->set_rules('controller_name', 'Controller Name', 'required|trim');
        $this->form_validation->set_rules('icon', 'Icon', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'module_name' => $this->input->post('module_name'),
                'module_description' => $this->input->post('module_description'),
                'controller_name' => $this->input->post('controller_name'),
                'icon' => $this->input->post('icon'),
                'parent_id' => $this->input->post('parent_id') ? $this->input->post('parent_id') : null,
                'parent_menu_id' => $this->input->post('parent_menu_id') ? $this->input->post('parent_menu_id') : null,
                'sort_order' => $this->input->post('sort_order'),
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->update_module($id_module, $data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Module updated successfully!</div>');
        }
        
        redirect('rbac/modules');
    }
    
    public function delete_module($id_module)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->Rbac_model->delete_module($id_module);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Module deleted successfully!</div>');
        redirect('rbac/modules');
    }
    
    // ========== PARENT MENU MANAGEMENT ==========
    
    public function parent_menus()
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $data['title'] = 'Parent Menu Management';
        $data['user'] = $this->db->get_where('mst_user', ['id_user' => $this->session->userdata('id_user')])->row_array();
        $data['parent_menus'] = $this->Rbac_model->get_all_parent_menus();
        
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar_superadmin', $data);
        $this->load->view('rbac/parent_menus', $data);
        $this->load->view('templates/footer');
    }
    
    public function add_parent_menu()
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('menu_name', 'Menu Name', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'menu_name' => $this->input->post('menu_name'),
                'icon' => $this->input->post('icon') ?: 'fa-folder',
                'sort_order' => $this->input->post('sort_order') ?: 0,
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->create_parent_menu($data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Parent menu created successfully!</div>');
        }
        
        redirect('rbac/parent_menus');
    }
    
    public function edit_parent_menu($id_parent_menu)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->form_validation->set_rules('menu_name', 'Menu Name', 'required|trim');
        
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger">'.validation_errors().'</div>');
        } else {
            $data = [
                'menu_name' => $this->input->post('menu_name'),
                'icon' => $this->input->post('icon') ?: 'fa-folder',
                'sort_order' => $this->input->post('sort_order') ?: 0,
                'is_active' => $this->input->post('is_active') ? 1 : 0
            ];
            
            $this->Rbac_model->update_parent_menu($id_parent_menu, $data);
            $this->session->set_flashdata('msg', '<div class="alert alert-success">Parent menu updated successfully!</div>');
        }
        
        redirect('rbac/parent_menus');
    }
    
    public function delete_parent_menu($id_parent_menu)
    {
        $this->rbac->require_permission('rbac.permissions.manage');
        
        $this->Rbac_model->delete_parent_menu($id_parent_menu);
        $this->session->set_flashdata('msg', '<div class="alert alert-success">Parent menu deleted successfully!</div>');
        redirect('rbac/parent_menus');
    }
}
