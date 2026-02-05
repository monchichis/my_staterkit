<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MY_Controller - Base controller for the application
 * 
 * This controller handles global initialization including:
 * - Loading and applying the default timezone from database
 */
class MY_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load session library if not already loaded
        if (!isset($this->session)) {
            $this->load->library('session');
        }
        
        // Apply settings (Timezone & Session Timeout)
        $this->_load_settings();
        
        // Check Maintenance Mode
        $this->_check_maintenance_mode();
    }
    
    /**
     * Check and enforce maintenance mode
     */
    protected function _check_maintenance_mode()
    {
        // Skip if running from CLI
        if (is_cli()) return;
        
        // Load database if not already loaded
        if (!isset($this->db) || !is_object($this->db)) {
            $this->load->database();
        }
        
        // Check database connection and table existence to prevent errors during install/setup
        if (!$this->db->table_exists('tbl_aplikasi')) return;
        
        // Check maintenance mode status
        $query = $this->db->select('maintenance_mode')->get('tbl_aplikasi');
        $status = 0;
        if ($query && $query->num_rows() > 0) {
            $row = $query->row();
            $status = isset($row->maintenance_mode) ? $row->maintenance_mode : 0;
        }
        
        // Share status with all views
        $this->load->vars(['maintenance_mode' => $status]);
        
        if ($status == 1) {
            $role = $this->session->userdata('level');
            
            // 1. Super Admin Bypass
            if ($role == 'Super Admin') return;
            
            // 2. Allow if not logged in (Auth controller handles login)
            if (!$this->session->userdata('id_user')) return;
            
            $class = $this->router->fetch_class();
            $method = $this->router->fetch_method();
            
            // 3. Allow Auth controller (login, logout, blocked)
            if (strtolower($class) == 'auth') return;
            
            // 4. Determine allowed Dashboard Controller for the User
            // Convention: Role Name "Gudang Utama" -> Controller "Gudangutama" (or similar)
            // Auth.php uses: ucfirst(str_replace(' ', '', $primary_role))
            $role_controller = strtolower(str_replace(' ', '', $role));
            
            // 5. Enforce Restriction
            // If accessing any other controller OR any method other than index on the dashboard
            // OR if strictly enforcing maintenance on dashboard as well (Global Maintenance)
            
            if ($this->input->is_ajax_request()) {
                // If it's an AJAX request, check if it's the specific maintenance toggle action 
                // from Super Admin (though they are bypassed above) or other allowed AJAX
                // Since this block is for restricted users:
                
                // Allow some specific ajax if needed, but generally block
                echo json_encode(['status' => false, 'message' => 'Maintenance Mode Active']);
                exit;
            }
            
            // For any standard request by a restricted user, show Maintenance View and STOP
            echo $this->load->view('errors/maintenance', [], TRUE);
            exit;
        }
    }
    
    /**
     * Load application settings (Timezone & Session Timeout)
     */
    protected function _load_settings()
    {
        // Check if database is configured and app is installed
        $lock_file = APPPATH . 'config/installed.lock';
        if (!file_exists($lock_file)) {
            // Not installed yet, use defaults
            date_default_timezone_set('Asia/Jakarta');
            return;
        }
        
        try {
            // Load database if not already loaded
            if (!isset($this->db) || !is_object($this->db)) {
                $this->load->database();
            }
            
            // Get settings from tbl_aplikasi
            $query = $this->db->select('default_timezone, session_timeout')
                              ->from('tbl_aplikasi')
                              ->limit(1)
                              ->get();
            
            if ($query && $query->num_rows() > 0) {
                $row = $query->row();
                
                // 1. Apply Timezone
                $timezone = isset($row->default_timezone) && !empty($row->default_timezone) 
                            ? $row->default_timezone 
                            : 'Asia/Jakarta';
                            
                // Validate timezone before setting
                if (in_array($timezone, timezone_identifiers_list())) {
                    date_default_timezone_set($timezone);
                } else {
                    date_default_timezone_set('Asia/Jakarta');
                }

                // 2. Set Session Timeout (Global Variable for Views)
                $timeout = isset($row->session_timeout) && is_numeric($row->session_timeout) 
                           ? (int)$row->session_timeout 
                           : 300; // Default 5 minutes
                           
                $this->load->vars(['session_timeout' => $timeout]);
                
            } else {
                date_default_timezone_set('Asia/Jakarta');
                $this->load->vars(['session_timeout' => 300]);
            }
        } catch (Exception $e) {
            // Fallback to default
            date_default_timezone_set('Asia/Jakarta');
            $this->load->vars(['session_timeout' => 300]);
        }
    }
    
    /**
     * Get current timezone info with UTC offset
     * 
     * @return array Timezone info with 'timezone' and 'offset'
     */
    protected function get_timezone_info()
    {
        $timezone = date_default_timezone_get();
        $dateTimeZone = new DateTimeZone($timezone);
        $dateTime = new DateTime('now', $dateTimeZone);
        $offset = $dateTimeZone->getOffset($dateTime);
        
        // Format offset as +/-HH:MM
        $hours = floor(abs($offset) / 3600);
        $minutes = floor((abs($offset) % 3600) / 60);
        $sign = $offset >= 0 ? '+' : '-';
        $offsetFormatted = sprintf('%s%02d:%02d', $sign, $hours, $minutes);
        
        return [
            'timezone' => $timezone,
            'offset' => $offsetFormatted,
            'display' => $timezone . ' (UTC' . $offsetFormatted . ')'
        ];
    }

    /**
     * Get dashboard components (Charts & Widgets) for the current user
     * 
     * @param string $placement 'dashboard' or 'report_page'
     * @return array
     */
    protected function _get_dashboard_components($placement = 'dashboard')
    {
        $id_user = $this->session->userdata('id_user');
        
        $this->load->model('Rbac_model');
        $this->load->model('Chart_model');
        $this->load->model('Summary_model');
        
        $user_roles = $this->Rbac_model->get_user_role_ids($id_user);
        
        return [
            'charts' => $this->Chart_model->get_charts_for_view($placement, $user_roles),
            'summary_widgets' => $this->Summary_model->get_widgets_for_dashboard($user_roles, $placement)
        ];
    }
}
