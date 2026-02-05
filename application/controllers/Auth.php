<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load libraries required for all methods
        $this->load->library('session');
        $this->load->library('form_validation');
        $this->load->database();
    }

    public function index()
    {
        $this->form_validation->set_rules('email', 'Alamat Email', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        if ($this->form_validation->run() == false) {
            $this->load->view('auth/index');
        } else {
            $email = $this->input->post('email');
            $password = $this->input->post('password');
            $user = $this->db->get_where('mst_user', array('email' => $email))->row_array();
            if ($user) {
                if ($user['is_active'] == 1) {
                    if (password_verify($password, $user['password'])) {
                        $data = [
                            'id_user' => $user['id_user'],
                            'email' => $user['email'],
                            'level' => $user['level']
                        ];
                        $this->session->set_userdata($data);
                        
                        // Load RBAC library and permissions
                        $this->load->library('rbac_lib', null, 'rbac');
                        $permissions = $this->rbac->get_permissions();
                        // $roles = $this->rbac->get_roles();
                        
                        // Dynamic Redirect based on PRIMARY role
                        // We assume the stored 'level' in session or mst_user is the primary role name
                        // Or we fetch first role from RBAC
                        
                        $primary_role = $user['level']; // Fallback to level column
                        
                        // If using RBAC multiple roles, we might want to prioritize
                        // For now staying simple as per request: redirect to controller based on role name
                        
                        if ($primary_role == 'Super Admin') {
                             $this->session->set_flashdata('message', 'Login');
                             redirect('superadmin');
                        } else {
                            // Slugify the role name to match controller/folder convention
                            // e.g. "Gudang Utama" -> "gudang_utama"
                            $role_slug = strtolower(str_replace(' ', '_', $primary_role));
                            
                            // Verify if controller exists, otherwise default to user or blocked
                            if (file_exists(APPPATH . 'controllers/' . ucfirst(str_replace(' ', '', $primary_role)) . '.php')) {
                                $this->session->set_flashdata('message', 'Login');
                                redirect($role_slug);
                            } else {
                                // Fallback or could be just 'user'
                                redirect('user'); 
                            }
                        }
                    } else {
                        $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Password salah</div>');
                        redirect('auth/index');
                    }
                } else {
                    $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">User Tidak aktif</div>');
                    redirect('auth/index');
                }
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Email dan Password tidak sama</div>');
                redirect('auth/index');
            }
        }
    }

    public function reset(){
        $data['title'] = 'Reset Password';

        $this->load->view('auth/reset', $data);
    }
    public function do_reset()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('nik', 'NIK', 'trim|required|numeric');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Reset Password';
            $this->load->view('auth/reset', $data);
        } else {
            $email = $this->input->post('email', true);
            $nik = $this->input->post('nik', true);

            $user = $this->db->get_where('mst_user', ['nik' => $nik, 'email' => $email])->row_array();

            if ($user) {
                // Generate random password
                $new_password = substr(md5(uniqid(rand(), true)), 0, 8);
                $pass = password_hash($new_password, PASSWORD_DEFAULT);
                
                $this->db->set('password', $pass);
                $this->db->where('nik', $nik);
                $this->db->where('email', $email);
                $this->db->update('mst_user');
                
                // In a real app, you would email this. For now, show it (or better, change flow to email)
                // Since email config isn't set up, we'll show it in flashdata for now but warn
                $this->session->set_flashdata('message', 'Reset Password Berhasil. Password baru anda: ' . $new_password);
                redirect('auth');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger" role="alert">Data Tidak Cocok</div>');
                redirect('auth/reset');
            }
        }
    }

    public function blocked()
    {
        
        $data['title'] = 'Akses Ditolak';
        $data['user'] = $this->db->get_where('mst_user', ['level' => $this->session->userdata('level')])->row_array();
        $this->load->view('auth/blocked', $data);
    }

    public function refresh_session()
    {
        if ($this->session->userdata('id_user')) {
             $this->session->sess_regenerate();
             echo json_encode(['status' => true]);
        } else {
             echo json_encode(['status' => false]);
        }
    }

    public function logout()
    {
        $this->session->unset_userdata('id_user');
        $this->session->unset_userdata('level');
        $this->session->unset_userdata('email');
        $this->session->set_flashdata('message', 'Keluar');
        redirect('auth/index');
    }
}
