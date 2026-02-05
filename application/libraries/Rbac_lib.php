<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RBAC Library
 * Role-Based Access Control System
 */
class Rbac_lib
{
    protected $CI;
    protected $user_id;
    protected $user_permissions = [];
    protected $user_roles = [];
    
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        
        // Load user permissions if logged in
        if ($this->CI->session->userdata('id_user')) {
            $this->user_id = $this->CI->session->userdata('id_user');
            $this->load_user_permissions();
        }
    }
    
    /**
     * Load user permissions from database into memory
     * Uses version-based cache invalidation for dynamic permission updates
     */
    private function load_user_permissions()
    {
        if (!$this->user_id) {
            return false;
        }
        
        // Check session cache first, but also verify cache version
        $cached_permissions = $this->CI->session->userdata('user_permissions');
        $cached_roles = $this->CI->session->userdata('user_roles');
        $cached_version = $this->CI->session->userdata('permissions_cache_version');
        
        // Get current permissions version from database (updated when permissions change)
        $current_version = $this->get_permissions_version();
        
        // If cache is valid and version matches, use cached data
        if ($cached_permissions && $cached_roles && $cached_version === $current_version) {
            $this->user_permissions = $cached_permissions;
            $this->user_roles = $cached_roles;
            return true;
        }
        
        // Load from database (cache is invalid or outdated)
        $query = "
            SELECT DISTINCT p.permission_key, p.permission_name, r.role_name
            FROM tbl_user_roles ur
            JOIN mst_roles r ON ur.role_id = r.id_role
            JOIN tbl_role_permissions rp ON r.id_role = rp.role_id
            JOIN mst_permissions p ON rp.permission_id = p.id_permission
            WHERE ur.user_id = ? AND r.is_active = 1 AND p.is_active = 1
        ";
        
        $result = $this->CI->db->query($query, [$this->user_id]);
        
        foreach ($result->result() as $row) {
            $this->user_permissions[] = $row->permission_key;
            if (!in_array($row->role_name, $this->user_roles)) {
                $this->user_roles[] = $row->role_name;
            }
        }
        
        // Cache in session with version
        $this->CI->session->set_userdata([
            'user_permissions' => $this->user_permissions,
            'user_roles' => $this->user_roles,
            'permissions_cache_version' => $current_version
        ]);
        
        return true;
    }
    
    /**
     * Get current permissions version (for cache invalidation)
     * Returns a hash that changes whenever role_permissions table changes
     */
    private function get_permissions_version()
    {
        // Use count + checksum of role_permissions as version
        $query = $this->CI->db->query("SELECT COUNT(*) as cnt, GROUP_CONCAT(CONCAT(role_id, '-', permission_id) ORDER BY role_id, permission_id) as data FROM tbl_role_permissions");
        $row = $query->row();
        return $row ? md5($row->cnt . '_' . $row->data) : 'v1';
    }
    
    /**
     * Check if user has specific permission
     * @param string $permission_key Permission key (e.g., 'user.create')
     * @return bool
     */
    public function has_permission($permission_key)
    {
        // 1. Check exact match
        if (in_array($permission_key, $this->user_permissions)) {
            return true;
        }
        
        // 2. Check for wildcard 'manage' permission
        // Example: if checking 'user.create', check if user has 'user.manage'
        $parts = explode('.', $permission_key);
        if (count($parts) > 1) {
            array_pop($parts); // Remove action (e.g., 'create')
            $parts[] = 'manage'; // Add 'manage'
            $manage_key = implode('.', $parts);
            
            if (in_array($manage_key, $this->user_permissions)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if user has any of the specified permissions
     * @param array $permissions Array of permission keys
     * @return bool
     */
    public function has_any_permission($permissions)
    {
        foreach ($permissions as $permission) {
            if ($this->has_permission($permission)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Check if user has all specified permissions
     * @param array $permissions Array of permission keys
     * @return bool
     */
    public function has_all_permissions($permissions)
    {
        foreach ($permissions as $permission) {
            if (!$this->has_permission($permission)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Check if user has specific role
     * @param string $role_name Role name
     * @return bool
     */
    public function has_role($role_name)
    {
        return in_array($role_name, $this->user_roles);
    }
    
    /**
     * Get all user permissions
     * @return array
     */
    public function get_permissions()
    {
        return $this->user_permissions;
    }
    
    /**
     * Get all user roles
     * @return array
     */
    public function get_roles()
    {
        return $this->user_roles;
    }
    
    /**
     * Require permission or show styled 403 error page
     * @param string $permission_key
     */
    public function require_permission($permission_key)
    {
        if (!$this->has_permission($permission_key)) {
            // Check if user is logged in
            if (!$this->user_id) {
                redirect('auth/login');
                return;
            }
            
            // Load user data for header
            $data['user'] = $this->CI->db->get_where('mst_user', ['id_user' => $this->user_id])->row_array();
            $data['title'] = 'Access Denied';
            $data['message'] = 'Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.';
            $data['required_permission'] = $permission_key;
            
            // Get role and set dashboard URL
            $role = $this->CI->session->userdata('level');
            if ($role == 'Super Admin') {
                $sidebar_view = 'templates/sidebar_superadmin';
                $data['dashboard_url'] = base_url('superadmin');
            } else {
                $role_slug = strtolower(str_replace(' ', '_', $role));
                $sidebar_view = 'templates/sidebar_' . $role_slug;
                $data['dashboard_url'] = base_url($role_slug);
                
                // Check if sidebar exists, fallback to admin if not
                $sidebar_path = APPPATH . 'views/' . $sidebar_view . '.php';
                if (!file_exists($sidebar_path)) {
                    $sidebar_view = 'templates/sidebar_admin';
                }
            }
            
            // Load views as string and echo directly (bypass CI output buffering)
            echo $this->CI->load->view('templates/header', $data, TRUE);
            echo $this->CI->load->view($sidebar_view, $data, TRUE);
            echo $this->CI->load->view('errors/access_denied', $data, TRUE);
            
            // Footer needs special handling for {elapsed_time} pseudo-variable
            $footer = $this->CI->load->view('templates/footer', $data, TRUE);
            $elapsed_time = $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
            $footer = str_replace('{elapsed_time}', $elapsed_time, $footer);
            echo $footer;
            
            exit;
        }
    }
    
    /**
     * Check if a module is active by controller name
     * @param string $controller_name
     * @return bool
     */
    public function is_module_active($controller_name)
    {
        $query = $this->CI->db->get_where('mst_modules', ['controller_name' => $controller_name]);
        $module = $query->row();
        
        // If module doesn't exist in database, allow access (not managed by RBAC)
        if (!$module) {
            return true;
        }
        
        return $module->is_active == 1;
    }
    
    /**
     * Require module to be active or show error page
     * @param string $controller_name
     */
    public function require_active_module($controller_name)
    {
        if (!$this->is_module_active($controller_name)) {
            // Load user data for header
            $data['user'] = $this->CI->db->get_where('mst_user', ['id_user' => $this->user_id])->row_array();
            $data['title'] = 'Module Inactive';
            $data['message'] = 'Maaf, modul ini sedang tidak aktif.';
            $data['required_permission'] = 'module.active';
            
            // Get role and set dashboard URL
            $role = $this->CI->session->userdata('level');
            if ($role == 'Super Admin') {
                $sidebar_view = 'templates/sidebar_superadmin';
                $data['dashboard_url'] = base_url('superadmin');
            } else {
                $role_slug = strtolower(str_replace(' ', '_', $role));
                $sidebar_view = 'templates/sidebar_' . $role_slug;
                $data['dashboard_url'] = base_url($role_slug);
                
                $sidebar_path = APPPATH . 'views/' . $sidebar_view . '.php';
                if (!file_exists($sidebar_path)) {
                    $sidebar_view = 'templates/sidebar_admin';
                }
            }
            
            echo $this->CI->load->view('templates/header', $data, TRUE);
            echo $this->CI->load->view($sidebar_view, $data, TRUE);
            echo $this->CI->load->view('errors/access_denied', $data, TRUE);
            
            $footer = $this->CI->load->view('templates/footer', $data, TRUE);
            $elapsed_time = $this->CI->benchmark->elapsed_time('total_execution_time_start', 'total_execution_time_end');
            $footer = str_replace('{elapsed_time}', $elapsed_time, $footer);
            echo $footer;
            
            exit;
        }
    }
    
    /**
     * Build menu based on user permissions
     * @return array
     */
    public function build_menu()
    {
        $query = "
            SELECT DISTINCT m.id_module, m.module_name, m.controller_name, m.icon, m.parent_id, m.sort_order
            FROM mst_modules m
            JOIN mst_permissions p ON m.id_module = p.module_id
            JOIN tbl_role_permissions rp ON p.id_permission = rp.permission_id
            JOIN tbl_user_roles ur ON rp.role_id = ur.role_id
            WHERE ur.user_id = ? AND m.is_active = 1
            ORDER BY m.sort_order, m.module_name
        ";
        
        $result = $this->CI->db->query($query, [$this->user_id]);
        
        $menu = [];
        foreach ($result->result_array() as $row) {
            if ($row['parent_id'] == null) {
                $menu[$row['id_module']] = $row;
                $menu[$row['id_module']]['children'] = [];
            }
        }
        
        // Add children
        foreach ($result->result_array() as $row) {
            if ($row['parent_id'] != null && isset($menu[$row['parent_id']])) {
                $menu[$row['parent_id']]['children'][] = $row;
            }
        }
        
        return $menu;
    }
    
    /**
     * Refresh user permissions (call after role/permission changes)
     */
    public function refresh_permissions()
    {
        $this->user_permissions = [];
        $this->user_roles = [];
        $this->CI->session->unset_userdata(['user_permissions', 'user_roles']);
        $this->load_user_permissions();
    }
    
    /**
     * Check module access
     * @param string $controller_name
     * @param string $action (view, create, edit, delete)
     * @return bool
     */
    public function can_access($controller_name, $action = 'view')
    {
        $permission_key = strtolower($controller_name) . '.' . strtolower($action);
        return $this->has_permission($permission_key);
    }
}
