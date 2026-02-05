<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * RBAC Helper Functions
 * Convenient functions for permission checking in views
 */

if (!function_exists('can')) {
    /**
     * Check if current user has permission
     * @param string $permission_key
     * @return bool
     */
    function can($permission_key)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->has_permission($permission_key);
    }
}

if (!function_exists('has_role')) {
    /**
     * Check if current user has role
     * @param string $role_name
     * @return bool
     */
    function has_role($role_name)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->has_role($role_name);
    }
}

if (!function_exists('can_any')) {
    /**
     * Check if user has any of the permissions
     * @param array $permissions
     * @return bool
     */
    function can_any($permissions)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->has_any_permission($permissions);
    }
}

if (!function_exists('can_all')) {
    /**
     * Check if user has all permissions
     * @param array $permissions
     * @return bool
     */
    function can_all($permissions)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->has_all_permissions($permissions);
    }
}

if (!function_exists('show_if_can')) {
    /**
     * Display content if user has permission
     * @param string $permission_key
     * @param string $content
     * @return string
     */
    function show_if_can($permission_key, $content)
    {
        return can($permission_key) ? $content : '';
    }
}

if (!function_exists('require_permission')) {
    /**
     * Require permission or show error
     * @param string $permission_key
     */
    function require_permission($permission_key)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        $CI->rbac->require_permission($permission_key);
    }
}

if (!function_exists('get_user_permissions')) {
    /**
     * Get all user permissions
     * @return array
     */
    function get_user_permissions()
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->get_permissions();
    }
}

if (!function_exists('get_user_roles')) {
    /**
     * Get all user roles
     * @return array
     */
    function get_user_roles()
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->get_roles();
    }
}

if (!function_exists('is_module_active')) {
    /**
     * Check if a module is active by controller name
     * @param string $controller_name
     * @return bool
     */
    function is_module_active($controller_name)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        return $CI->rbac->is_module_active($controller_name);
    }
}

if (!function_exists('require_active_module')) {
    /**
     * Require module to be active or show error page
     * @param string $controller_name
     */
    function require_active_module($controller_name)
    {
        $CI =& get_instance();
        if (!isset($CI->rbac)) {
            $CI->load->library('rbac_lib', null, 'rbac');
        }
        $CI->rbac->require_active_module($controller_name);
    }
}

