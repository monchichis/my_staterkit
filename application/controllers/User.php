<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class User extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		is_logged_in();
		is_user();
		$this->load->helper('ata');
		$this->load->helper('tglindo');
		$this->load->helper('rupiah');
		$this->load->model('User_model', 'user');
		$this->load->model('Admin_model', 'admin');
	}

	public function index()
	{
		$id_user = $this->session->userdata('id_user');
		$data['title'] = 'Beranda';
		$data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$id_user = $this->session->userdata('id_user');
		$data['identitas'] = $this->db->get('tbl_aplikasi')->row();
		
		
		
		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar_user', $data);
		$this->load->view('user/index', $data);
		$this->load->view('templates/footer');
	}

	

	public function edit_profile()
	{
		
		$upload_image = $_FILES['image']['name'];
		if ($upload_image) {
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']     = '2048';
			$config['upload_path'] = './assets/dist/img/profile/';
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('image')) {
				$data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
				$old_image = $data['user']['image'];
				if ($old_image != 'default.png') {
					unlink(FCPATH . 'assets/dist/img/profile/' . $old_image);
				}
				$new_image = $this->upload->data('file_name');
				// $this->db->set('image', $new_image);
				$id_user = $this->session->userdata('id_user');
				$this->db->query("UPDATE mst_user set image='$new_image' WHERE id_user='$id_user'");
			} else {
				echo $this->upload->display_errors();
			}
		}
		$nama = $this->input->post('nama');
		
		// $this->db->set('nama', $nama);
		// $this->db->update('mst_user');
		// $this->db->where('id_user',$id_user);
		$id_user = $this->session->userdata('id_user');
		$this->db->query("UPDATE mst_user SET nama='$nama' WHERE id_user='$id_user'");
		$this->session->set_flashdata('message', 'Simpan Perubahan');
		redirect('user/index');
	}

	public function ubah_password()
	{
		$current_password = $this->input->post('current_password');
		$new_password = $this->input->post('new_password1');
		if ($current_password == $new_password) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">Ubah Password Gagal !! <br> Password baru tidak boleh sama dengan password lama</div>');
			redirect('user/index');
		} else {
			$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
			$this->db->set('password', $password_hash);
			$this->db->where('id_user', $this->session->userdata('id_user'));
			$this->db->update('mst_user');
			$this->session->set_flashdata('message', 'Ubah Password');
			redirect('user/index');
		}
	}

	
}
