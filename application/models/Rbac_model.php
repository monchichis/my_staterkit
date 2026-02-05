<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rbac_model extends CI_Model
{
    // ========== ROLE MANAGEMENT ==========
    
    public function get_all_roles()
    {
        return $this->db->order_by('role_name', 'ASC')->get('mst_roles')->result();
    }

    public function get_roles_with_permission_count()
    {
        $this->db->select('r.*, COUNT(rp.permission_id) as permission_count');
        $this->db->from('mst_roles r');
        $this->db->join('tbl_role_permissions rp', 'r.id_role = rp.role_id', 'left');
        $this->db->group_by('r.id_role');
        $this->db->order_by('r.role_name', 'ASC');
        return $this->db->get()->result();
    }

    
    public function get_active_roles()
    {
        return $this->db->where('is_active', 1)->order_by('role_name', 'ASC')->get('mst_roles')->result();
    }
    
    public function get_role($id_role)
    {
        return $this->db->get_where('mst_roles', ['id_role' => $id_role])->row();
    }
    
    public function create_role($data)
    {
        return $this->db->insert('mst_roles', $data);
    }
    
    public function update_role($id_role, $data)
    {
        $this->db->where('id_role', $id_role);
        return $this->db->update('mst_roles', $data);
    }
    
    public function delete_role($id_role)
    {
        return $this->db->delete('mst_roles', ['id_role' => $id_role]);
    }
    
    // ========== MODULE MANAGEMENT ==========
    
    public function get_all_modules()
    {
        return $this->db->order_by('sort_order', 'ASC')->get('mst_modules')->result();
    }
    
    public function get_module($id_module)
    {
        return $this->db->get_where('mst_modules', ['id_module' => $id_module])->row();
    }
    
    public function create_module($data)
    {
        return $this->db->insert('mst_modules', $data);
    }
    
    public function update_module($id_module, $data)
    {
        $this->db->where('id_module', $id_module);
        return $this->db->update('mst_modules', $data);
    }
    
    public function delete_module($id_module)
    {
        return $this->db->delete('mst_modules', ['id_module' => $id_module]);
    }

    // ========== PARENT MENU MANAGEMENT ==========
    
    public function get_all_parent_menus()
    {
        return $this->db->order_by('sort_order', 'ASC')->get('mst_parent_menus')->result();
    }
    
    public function get_active_parent_menus()
    {
        return $this->db->where('is_active', 1)->order_by('sort_order', 'ASC')->get('mst_parent_menus')->result();
    }
    
    public function get_parent_menu($id_parent_menu)
    {
        return $this->db->get_where('mst_parent_menus', ['id_parent_menu' => $id_parent_menu])->row();
    }
    
    public function create_parent_menu($data)
    {
        // Auto-calculate sort_order if not provided or set to 0
        if (!isset($data['sort_order']) || $data['sort_order'] == 0) {
            $max = $this->db->select_max('sort_order')->get('mst_parent_menus')->row();
            $data['sort_order'] = ($max && $max->sort_order) ? $max->sort_order + 1 : 1;
        }
        return $this->db->insert('mst_parent_menus', $data);
    }
    
    public function update_parent_menu($id_parent_menu, $data)
    {
        $this->db->where('id_parent_menu', $id_parent_menu);
        return $this->db->update('mst_parent_menus', $data);
    }
    
    public function delete_parent_menu($id_parent_menu)
    {
        return $this->db->delete('mst_parent_menus', ['id_parent_menu' => $id_parent_menu]);
    }
    
    public function get_modules_grouped_by_parent_menu($user_id = null)
    {
        // 1. Prepare filtering data FIRST to avoid Query Builder conflict
        $accessible_module_ids = [];
        if ($user_id) {
            $accessible_modules = $this->get_user_accessible_modules($user_id);
            $accessible_module_ids = array_map(function($m) { return $m->id_module; }, $accessible_modules);
            
            // If user has no accessible modules, ensure we don't accidentally show everything
            if (empty($accessible_module_ids)) {
                $accessible_module_ids = [-1]; 
            }
        }

        // 2. Build the Main Query
        $this->db->select('pm.*, m.id_module, m.module_name, m.controller_name, m.icon as module_icon, m.sort_order as module_sort_order, m.is_active as module_is_active');
        $this->db->from('mst_parent_menus pm');
        $this->db->join('mst_modules m', 'pm.id_parent_menu = m.parent_menu_id AND m.is_active = 1', 'left');
        $this->db->where('pm.is_active', 1);
        
        // 3. Apply Filter
        if ($user_id) {
            $this->db->where_in('m.id_module', $accessible_module_ids);
        }
        
        $this->db->order_by('pm.sort_order', 'ASC');
        $this->db->order_by('m.sort_order', 'ASC');
        
        $result = $this->db->get()->result();
        
        $grouped = [];
        foreach ($result as $row) {
            // Only add parent menu if it has at least one accessible module
            if ($row->id_module) {
                if (!isset($grouped[$row->id_parent_menu])) {
                    $grouped[$row->id_parent_menu] = [
                        'id_parent_menu' => $row->id_parent_menu,
                        'menu_name' => $row->menu_name,
                        'icon' => $row->icon,
                        'sort_order' => $row->sort_order,
                        'modules' => []
                    ];
                }
                
                $grouped[$row->id_parent_menu]['modules'][] = [
                    'id_module' => $row->id_module,
                    'module_name' => $row->module_name,
                    'controller_name' => $row->controller_name,
                    'icon' => $row->module_icon,
                    'sort_order' => $row->module_sort_order
                ];
            }
        }
        
        return $grouped;
    }
    
    public function get_standalone_modules($user_id = null)
    {
        // 1. Prepare filtering data FIRST
        $accessible_module_ids = [];
        if ($user_id) {
            $accessible_modules = $this->get_user_accessible_modules($user_id);
            $accessible_module_ids = array_map(function($m) { return $m->id_module; }, $accessible_modules);
            
            if (empty($accessible_module_ids)) {
                $accessible_module_ids = [-1];
            }
        }

        // 2. Build Main Query
        $this->db->where('parent_menu_id IS NULL', null, false);
        $this->db->where('is_active', 1);
        
        // 3. Apply Filter
        if ($user_id) {
            $this->db->where_in('id_module', $accessible_module_ids);
        }
        
        return $this->db->order_by('sort_order', 'ASC')->get('mst_modules')->result();
    }
    
    public function is_module_active($controller_name)
    {
        $module = $this->db->get_where('mst_modules', ['controller_name' => $controller_name])->row();
        return $module && $module->is_active == 1;
    }

    public function get_user_accessible_modules($user_id)
    {
        // Select distinct modules where user has at least one permission
        // Using JOINs from User -> Roles -> RolePermissions -> Permissions -> Modules
        $this->db->distinct();
        $this->db->select('m.*');
        $this->db->from('mst_modules m');
        $this->db->join('mst_permissions p', 'p.module_id = m.id_module');
        $this->db->join('tbl_role_permissions rp', 'rp.permission_id = p.id_permission');
        $this->db->join('tbl_user_roles ur', 'ur.role_id = rp.role_id');
        $this->db->where('ur.user_id', $user_id);
        $this->db->where('m.is_active', 1);
        $this->db->order_by('m.sort_order', 'ASC');
        
        return $this->db->get()->result();
    }
    
    // ========== PERMISSION MANAGEMENT ==========
    
    public function get_all_permissions()
    {
        $this->db->select('p.*, m.module_name');
        $this->db->from('mst_permissions p');
        $this->db->join('mst_modules m', 'p.module_id = m.id_module');
        $this->db->order_by('m.module_name', 'ASC');
        $this->db->order_by('p.permission_name', 'ASC');
        return $this->db->get()->result();
    }
    
    public function get_permissions_by_module()
    {
        $permissions = $this->get_all_permissions();
        $grouped = [];
        
        foreach ($permissions as $perm) {
            if (!isset($grouped[$perm->module_name])) {
                $grouped[$perm->module_name] = [];
            }
            $grouped[$perm->module_name][] = $perm;
        }
        
        return $grouped;
    }
    
    public function get_permission($id_permission)
    {
        return $this->db->get_where('mst_permissions', ['id_permission' => $id_permission])->row();
    }
    
    public function create_permission($data)
    {
        return $this->db->insert('mst_permissions', $data);
    }
    
    public function update_permission($id_permission, $data)
    {
        $this->db->where('id_permission', $id_permission);
        return $this->db->update('mst_permissions', $data);
    }
    
    public function delete_permission($id_permission)
    {
        return $this->db->delete('mst_permissions', ['id_permission' => $id_permission]);
    }
    
    // ========== ROLE-PERMISSION RELATIONSHIP ==========
    
    public function get_role_permissions($role_id)
    {
        $this->db->select('permission_id');
        $this->db->from('tbl_role_permissions');
        $this->db->where('role_id', $role_id);
        $result = $this->db->get()->result();
        
        return array_column($result, 'permission_id');
    }
    
    public function assign_permission_to_role($role_id, $permission_id)
    {
        $data = [
            'role_id' => $role_id,
            'permission_id' => $permission_id
        ];
        
        // Check if already exists
        $exists = $this->db->get_where('tbl_role_permissions', $data)->num_rows();
        if ($exists > 0) {
            return true;
        }
        
        return $this->db->insert('tbl_role_permissions', $data);
    }
    
    public function remove_permission_from_role($role_id, $permission_id)
    {
        return $this->db->delete('tbl_role_permissions', [
            'role_id' => $role_id,
            'permission_id' => $permission_id
        ]);
    }
    
    public function sync_role_permissions($role_id, $permission_ids)
    {
        // Delete existing permissions
        $this->db->delete('tbl_role_permissions', ['role_id' => $role_id]);
        
        // Insert new permissions
        if (!empty($permission_ids)) {
            $data = [];
            foreach ($permission_ids as $permission_id) {
                $data[] = [
                    'role_id' => $role_id,
                    'permission_id' => $permission_id
                ];
            }
            return $this->db->insert_batch('tbl_role_permissions', $data);
        }
        
        return true;
    }
    
    // ========== USER-ROLE RELATIONSHIP ==========
    
    public function get_user_roles($user_id)
    {
        $this->db->select('r.*, ur.id as user_role_id');
        $this->db->from('tbl_user_roles ur');
        $this->db->join('mst_roles r', 'ur.role_id = r.id_role');
        $this->db->where('ur.user_id', $user_id);
        return $this->db->get()->result();
    }
    
    public function get_user_role_ids($user_id)
    {
        $this->db->select('role_id');
        $this->db->from('tbl_user_roles');
        $this->db->where('user_id', $user_id);
        $result = $this->db->get()->result();
        
        return array_column($result, 'role_id');
    }
    
    public function assign_role_to_user($user_id, $role_id)
    {
        $data = [
            'user_id' => $user_id,
            'role_id' => $role_id
        ];
        
        // Check if already exists
        $exists = $this->db->get_where('tbl_user_roles', $data)->num_rows();
        if ($exists > 0) {
            return true;
        }
        
        return $this->db->insert('tbl_user_roles', $data);
    }
    
    public function remove_role_from_user($user_id, $role_id)
    {
        return $this->db->delete('tbl_user_roles', [
            'user_id' => $user_id,
            'role_id' => $role_id
        ]);
    }
    
    public function sync_user_roles($user_id, $role_ids)
    {
        // Delete existing roles
        $this->db->delete('tbl_user_roles', ['user_id' => $user_id]);
        
        // Insert new roles
        if (!empty($role_ids)) {
            $data = [];
            foreach ($role_ids as $role_id) {
                $data[] = [
                    'user_id' => $user_id,
                    'role_id' => $role_id
                ];
            }
            return $this->db->insert_batch('tbl_user_roles', $data);
        }
        
        return true;
    }
    
    public function assign_roles_to_user($user_id, $role_ids)
    {
        // This is an alias for sync_user_roles for better naming
        return $this->sync_user_roles($user_id, $role_ids);
    }
    
    public function get_user_roles_with_names($user_id)
    {
        $this->db->select('r.id_role, r.role_name');
        $this->db->from('tbl_user_roles ur');
        $this->db->join('mst_roles r', 'ur.role_id = r.id_role');
        $this->db->where('ur.user_id', $user_id);
        return $this->db->get()->result();
    }
    
    // ========== PERMISSION MATRIX ==========
    
    public function get_permission_matrix()
    {
        $roles = $this->get_all_roles();
        $permissions = $this->get_permissions_by_module();
        
        $matrix = [];
        foreach ($roles as $role) {
            $role_perms = $this->get_role_permissions($role->id_role);
            $matrix[$role->id_role] = [
                'role' => $role,
                'permissions' => $role_perms
            ];
        }
        
        return [
            'roles' => $roles,
            'permissions' => $permissions,
            'matrix' => $matrix
        ];
    }
    
    // ========== AUDIT LOG ==========
    
    public function log_rbac_action($user_id, $action, $target_type, $target_id, $description)
    {
        $data = [
            'user_id' => $user_id,
            'action' => $action,
            'target_type' => $target_type,
            'target_id' => $target_id,
            'description' => $description
        ];
        
        return $this->db->insert('tbl_rbac_audit_log', $data);
    }
    
    public function get_audit_logs($limit = 50)
    {
        $this->db->select('a.*, u.nama as user_name');
        $this->db->from('tbl_rbac_audit_log a');
        $this->db->join('mst_user u', 'a.user_id = u.id_user');
        $this->db->order_by('a.created_at', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }
}
