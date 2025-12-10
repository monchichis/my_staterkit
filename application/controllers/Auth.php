<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Makassar');

class Auth extends CI_Controller
{
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
                        if ($user['level'] == 'Admin') {
                            $this->session->set_flashdata('message', 'Login');
                            redirect('admin');
                        } elseif ($user['level'] == 'Kadis') {
                            redirect('kepsek');
                        } else{
                            $this->session->set_flashdata('message', 'Login');
                            redirect('user');
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
    public function do_reset(){
            $email = $this->input->post('email');
            $nik = $this->input->post('nik');

            $query = $this->db->query("SELECT * FROM mst_user WHERE nik = '$nik' AND email = '$email' ");
           
            if ($query->num_rows() > 0 )  {
                $pass = password_hash($email, PASSWORD_DEFAULT);
                $this->db->query("UPDATE mst_user SET password = '$pass' WHERE nik ='$nik' and email ='$email' ");
                $this->session->set_flashdata('message',"Reset Password");
                redirect('Auth');
            } else {
                 $this->session->set_flashdata('msg','<div class="alert alert-danger" role="alert">Data Tidak Cocok</div>');
                 redirect('Auth/reset');
            }
    }

    public function blocked()
    {
        $data['title'] = 'Access Forbidden';
        $data['user'] = $this->db->get_where('mst_user', ['level' => $this->session->userdata('level')])->row_array();
        $this->load->view('auth/blocked', $data);
    }

    public function logout()
    {
        $this->session->unset_userdata('id_user');
        $this->session->unset_userdata('level');
        $this->session->unset_userdata('email');
        $this->session->set_flashdata('message', 'Logout');
        redirect('auth/index');
    }
}
