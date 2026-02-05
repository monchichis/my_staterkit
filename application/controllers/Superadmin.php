<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Superadmin extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		
		// Load session first (not autoloaded anymore due to wrapper fix)
		$this->load->library('session');
		
		// Load form validation (not autoloaded anymore)
		$this->load->library('form_validation');
		
		// Load database
		$this->load->database();
		
		// Load dbforge for uninstall functionality
		$this->load->dbforge();
		
		is_logged_in();
		// is_admin(); // Access control is now handled below
        
        // Redirect if not Super Admin
        if ($this->session->userdata('level') != 'Super Admin') {
            $role_slug = strtolower(str_replace(' ', '_', $this->session->userdata('level')));
            redirect($role_slug);
        }

		$this->load->helper('ata');
		$this->load->helper('tglindo');
		$this->load->helper('rupiah');
		$this->load->model('Admin_model', 'admin');
		$this->load->model('User_model', 'user');
		$this->load->model('Rbac_model', 'rbac_model');
	}
	
	public function index()
	{
		$id_user = $this->session->userdata('id_user');
		$data['title'] = 'Beranda';
		$data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$data['list_user'] = $this->db->get('mst_user')->result_array();
		$data['identitas'] = $this->db->get('tbl_aplikasi')->row();
		$data['user_aktif'] = $this->admin->countUserAktif();
		$data['user_tak_aktif'] = $this->admin->countUserTidakAktif();
		$data['user_bulan'] = $this->admin->countUserBulan();
		$data['total_user'] = $this->admin->countAllUser();
		// Get timezone info for dashboard display
		$data['timezone_info'] = $this->get_timezone_info();
        
        // Fetch Dashboard Components (Charts & Widgets)
        $dashboard_components = $this->_get_dashboard_components('dashboard');
        $data['charts'] = $dashboard_components['charts'];
        $data['summary_widgets'] = $dashboard_components['summary_widgets'];
		
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar_superadmin', $data);
		$this->load->view('superadmin/index', $data);
		$this->load->view('templates/footer');
		$this->load->view('superadmin/summary_widget/detail_modal');
		$this->load->view('superadmin/summary_widget/detail_modal_js');
	}
	
	public function setup_aplikasi(){
		$data['title'] = 'Identitas Aplikasi';
		$data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
		$data['data_aplikasi'] = $this->db->get_where('tbl_aplikasi')->result();
		$this->load->view('templates/header', $data);
		$this->load->view('templates/sidebar_superadmin', $data);
		$this->load->view('superadmin/aplikasi/v_aplikasi', $data);
		$this->load->view('templates/footer');
	}
	public function update_aplikasi(){
		$upload_image = $_FILES['logo']['name'];
		if ($upload_image) {
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']     = '2048';
			$config['upload_path'] = './assets/dist/aplikasi/';
			$this->load->library('upload', $config);
			if ($this->upload->do_upload('logo')) {
				$data['data_aplikasi'] = $this->db->get_where('tbl_aplikasi')->result();
				$old_image = $data['data_aplikasi']['logo'];
				if ($old_image != 'default.png') {
					unlink(FCPATH . 'assets/dist/aplikasi/' . $old_image);
				}
				$new_image = $this->upload->data('file_name');
				$this->db->set('logo', $new_image);
			} else {
				echo $this->upload->display_errors();
			}
		}
		$id = $this->input->post('id');
		$nama_aplikasi = $this->input->post('nama_aplikasi');
		$alamat = $this->input->post('alamat');
		$telp = $this->input->post('telp');
		$nama_developer = $this->input->post('nama_developer');
		
		$this->db->set('id', $id);
		$this->db->set('nama_aplikasi', $nama_aplikasi);
		$this->db->set('alamat', $alamat);
		$this->db->set('telp', $telp);
		$this->db->set('nama_developer', $nama_developer);
		
		$this->db->update('tbl_aplikasi');
		$this->db->where('id',$id);
		$this->session->set_flashdata('message', 'Simpan Perubahan');
		redirect('superadmin/index');
	}
	
	/**
	 * Change application skin/theme
	 * Saves the selected skin to database
	 */
	public function change_skin()
	{
		$skin = $this->input->post('skin');
		
		// Validate skin value
		$valid_skins = ['skin-1', 'skin-2', 'skin-3', 'skin-4', 'md-skin'];
		
		if (!in_array($skin, $valid_skins)) {
			echo json_encode([
				'status' => false,
				'message' => 'Skin tidak valid!'
			]);
			return;
		}
		
		// Check if skin_theme column exists, if not create it
		$fields = $this->db->field_exists('skin_theme', 'tbl_aplikasi');
		if (!$fields) {
			$this->load->dbforge();
			$field = [
				'skin_theme' => [
					'type' => 'VARCHAR',
					'constraint' => '50',
					'default' => 'md-skin'
				]
			];
			$this->dbforge->add_column('tbl_aplikasi', $field);
		}
		
		// Update skin in database
		$this->db->update('tbl_aplikasi', ['skin_theme' => $skin]);
		
		echo json_encode([
			'status' => true,
			'message' => 'Tema berhasil diubah!',
			'csrfHash' => $this->security->get_csrf_hash()
		]);
	}

    /**
     * Change Application Environment
     */
    public function change_environment()
    {
        $env = $this->input->post('env');
        $valid_envs = ['development', 'testing', 'production'];
        
        if (!in_array($env, $valid_envs)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid environment selected!',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }
        
        // Write to env.php file in root
        $env_file = FCPATH . 'env.php';
        
        if (file_put_contents($env_file, $env)) {
            echo json_encode([
                'status' => true,
                'message' => 'Environment changed to ' . ucfirst($env) . '! Please reload the page.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Failed to write environment file. Please check permissions.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        }
    }

	/**
	 * Toggle Maintenance Mode
	 */
	public function toggle_maintenance()
	{
		// Check if maintenance_mode column exists, if not create it
		if (!$this->db->field_exists('maintenance_mode', 'tbl_aplikasi')) {
			$this->load->dbforge();
			$field = [
				'maintenance_mode' => [
					'type' => 'TINYINT',
					'constraint' => '1',
					'default' => 0
				]
			];
			$this->dbforge->add_column('tbl_aplikasi', $field);
		}

		$current_status = $this->input->post('status'); // 1 or 0
		
		$this->db->update('tbl_aplikasi', ['maintenance_mode' => $current_status]);
		
		echo json_encode([
			'status' => true,
			'message' => $current_status == 1 ? 'Maintenance Mode Diaktifkan' : 'Maintenance Mode Dinonaktifkan',
			'csrfHash' => $this->security->get_csrf_hash()
		]);
	}
	public function edit_profile()
	{
		$id_user = $this->session->userdata('id_user');
		$upload_image = $_FILES['image']['name'];
		
		if ($upload_image) {
			$config['allowed_types'] = 'gif|jpg|png|jpeg';
			$config['max_size']     = '2048';
			$config['upload_path'] = './assets/dist/img/profile/';
			$this->load->library('upload', $config);
			
			if ($this->upload->do_upload('image')) {
				$data['user'] = $this->db->get_where('mst_user', ['id_user' => $id_user])->row_array();
				$old_image = $data['user']['image'];
				
				// Delete old image if not default
				if ($old_image != 'default.png' && file_exists(FCPATH . 'assets/dist/img/profile/' . $old_image)) {
					unlink(FCPATH . 'assets/dist/img/profile/' . $old_image);
				}
				
				$new_image = $this->upload->data('file_name');
				
				// Update image using Query Builder (secure)
				$this->db->where('id_user', $id_user);
				$this->db->update('mst_user', ['image' => $new_image]);
			} else {
				$this->session->set_flashdata('msg', '<div class="alert alert-danger">' . $this->upload->display_errors() . '</div>');
				redirect('superadmin/index');
				return;
			}
		}
		
		$nama = $this->input->post('nama', true);
		
		// Update nama using Query Builder (secure)
		$this->db->where('id_user', $id_user);
		$this->db->update('mst_user', ['nama' => $nama]);
		
		$this->session->set_flashdata('message', 'Simpan Perubahan');
		redirect('superadmin/index');
	}

	public function ubah_password()
	{
		$id_user = $this->session->userdata('id_user');
		$current_password = $this->input->post('current_password');
		$new_password = $this->input->post('new_password1');
		$confirm_password = $this->input->post('new_password2');
		
		// Get current user data from database
		$user = $this->db->get_where('mst_user', ['id_user' => $id_user])->row_array();
		
		if (!$user) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">User tidak ditemukan!</div>');
			redirect('superadmin/index');
			return;
		}
		
		// Validate current password matches database
		if (!password_verify($current_password, $user['password'])) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">Ubah Password Gagal! <br> Password lama tidak sesuai</div>');
			redirect('superadmin/index');
			return;
		}
		
		// Validate new password matches confirmation
		if ($new_password !== $confirm_password) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">Ubah Password Gagal! <br> Konfirmasi password baru tidak sama</div>');
			redirect('superadmin/index');
			return;
		}
		
		// Validate new password is not same as current password
		if ($current_password === $new_password) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">Ubah Password Gagal! <br> Password baru tidak boleh sama dengan password lama</div>');
			redirect('superadmin/index');
			return;
		}
		
		// Validate minimum password length
		if (strlen($new_password) < 3) {
			$this->session->set_flashdata('msg', '<div class="alert alert-danger font-weight-bolder text-center" role="alert">Ubah Password Gagal! <br> Password baru minimal 3 karakter</div>');
			redirect('superadmin/index');
			return;
		}
		
		// All validations passed, update password
		$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
		$this->db->where('id_user', $id_user);
		$this->db->update('mst_user', ['password' => $password_hash]);
		
		$this->session->set_flashdata('message', 'Ubah Password');
		redirect('superadmin/index');
	}
	

	public function man_user()
	{
		$this->form_validation->set_rules('nama', 'Nama Lengkap', 'required|trim');
		$this->form_validation->set_rules('email', 'Alamat Email', 'required|trim|is_unique[mst_user.email]', array(
			'is_unique' => 'Alamat Email sudah ada'
		));
		$this->form_validation->set_rules('password1', 'Password', 'required|trim|min_length[3]|matches[password2]', array(
			'matches' => 'Password tidak sama',
			'min_length' => 'password min 3 karakter'
		));
		$this->form_validation->set_rules('password2', 'Password', 'required|trim|matches[password1]');
		// $this->form_validation->set_rules('level','level','required|callback_cek_level');
		// $this->form_validation->set_rules('level','level','required|callback_cek_level_admin');
		$this->form_validation->set_rules('nik','NIK','required|trim|is_unique[mst_user.nik]', array(
			'is_unique' => 'Nomor Induk Kepegawaian Sudah Digunakan'
		));
		if ($this->form_validation->run() == FALSE) {
			$data['title'] = 'Management User';
			$data['user'] = $this->db->get_where('mst_user', ['email' => $this->session->userdata('email')])->row_array();
			$data['list_user'] = $this->admin->getAllUser();
			$data['roles'] = $this->rbac_model->get_active_roles();
			
			

			$this->load->view('templates/header', $data);
			$this->load->view('templates/sidebar_superadmin', $data);
			$this->load->view('superadmin/master/man_user', $data);
			$this->load->view('templates/footer');
		} else {
			$data = array(
				'nama' => $this->input->post('nama', true),
				'nik' => $this->input->post('nik', true),
				
				'email' => $this->input->post('email', true),
				'level' => $this->input->post('level', true),
				'password' => password_hash($this->input->post('password1'), PASSWORD_DEFAULT),
				'date_created' => date('Y/m/d'),
				'image' => 'default.png',
				'is_active' => 1
			);
			
        	$this->db->insert('mst_user', $data);
        	$new_user_id = $this->db->insert_id();
        	
        	// Assign roles to user
        	$roles = $this->input->post('roles');
        	if (!empty($roles)) {
        		$this->rbac_model->assign_roles_to_user($new_user_id, $roles);
        	}
        	
			$this->session->set_flashdata('message', 'Tambah Data');
			
			redirect('superadmin/man_user');
        
			
		}
	}

	public function get_user()
	{
		$id_user = $this->input->post('id_user');
		$user = $this->db->get_where('mst_user', ['id_user' => $id_user])->row_array();
		
		// Get user roles
		$user_roles = $this->rbac_model->get_user_role_ids($id_user);
		$user['roles'] = $user_roles;
		
		echo json_encode($user);
	}

	public function edit_user()
	{
		$id_user = $this->input->post('id_user');
		$nama = $this->input->post('nama');
		
		$nik = $this->input->post('nik');
		$level = $this->input->post('level');
		$is_active = $this->input->post('is_active');

		$this->db->set('nama', $nama);
		
		$this->db->set('nik', $nik);
		$this->db->set('level', $level);
		$this->db->set('is_active', $is_active);
		$this->db->where('id_user', $id_user);
		$this->db->update('mst_user');
		
		// Update user roles
		$roles = $this->input->post('roles');
		if (is_array($roles)) {
			$this->rbac_model->assign_roles_to_user($id_user, $roles);
		} else {
			// If no roles selected, remove all roles
			$this->rbac_model->assign_roles_to_user($id_user, []);
		}
		
		$this->session->set_flashdata('message', 'Ubah Data');
		redirect('superadmin/man_user');
	}

	public function toggle_user_status($id_user)
	{
		// Get current user status
		$user = $this->db->get_where('mst_user', ['id_user' => $id_user])->row_array();
		
		if (!$user) {
			$this->session->set_flashdata('message', 'User tidak ditemukan');
			redirect('superadmin/man_user');
			return;
		}
		
		// Toggle the is_active status (0 to 1, or 1 to 0)
		$new_status = ($user['is_active'] == 1) ? 0 : 1;
		
		// Update the status in database
		$this->db->set('is_active', $new_status);
		$this->db->where('id_user', $id_user);
		$this->db->update('mst_user');
		
		// Set flashdata message
		$status_text = ($new_status == 1) ? 'Aktif' : 'Tidak Aktif';
		$this->session->set_flashdata('message','Status User'. $status_text);
		
		// Redirect back to management user page
		redirect('superadmin/man_user');
	}


	public function disable_user()
	{
		// Get input data
		$id_user = $this->input->post('id_user');
		$secret_key = $this->input->post('secret_key');
		
		if (empty($id_user) || empty($secret_key)) {
			echo json_encode([
				'status' => false,
				'message' => 'User ID and secret key are required!'
			]);
			return;
		}
		
		// Get stored secret key from database
		$app_settings = $this->db->get('tbl_aplikasi')->row();
		
		if (!$app_settings || empty($app_settings->uninstall_secret_key)) {
			echo json_encode([
				'status' => false,
				'message' => 'Secret key not found in database!'
			]);
			return;
		}
		
		// Verify secret key using password_verify
		if (!password_verify($secret_key, $app_settings->uninstall_secret_key)) {
			echo json_encode([
				'status' => false,
				'message' => 'Invalid secret key! Please check and try again.'
			]);
			return;
		}
		
		// Disable the user
		$this->db->set('is_active', 0);
		$this->db->where('id_user', $id_user);
		$this->db->update('mst_user');
		
		echo json_encode([
			'status' => true,
			'message' => 'User disabled successfully!'
		]);
	}

	public function backup_database(){
        $this->load->dbutil();

        $prefs = array(     
            'format'      => 'sql',             
            'filename'    => "backupdb_gusananta_".date("Ymd-His").'.sql'
            );

        $backup =& $this->dbutil->backup($prefs); 

        $db_name = "backupdb_gusananta_".date("Ymd-His") .'.sql';
        $save = FCPATH.'assets/backupdb/'.$db_name;
        $this->load->helper('file');
        write_file($save, $backup); 


        $this->load->helper('download');
        force_download($db_name, $backup);
    }
    
    public function uninstall()
    {
        // CRITICAL: Only Super Admin can uninstall
        $this->load->helper('rbac_helper');
        
        if (!has_role('Super Admin')) {
            echo json_encode([
                'status' => false,
                'message' => 'Access Denied! Only Super Admin can uninstall the application.'
            ]);
            return;
        }
        
        // Get secret key from POST (sent via AJAX)
        $input_secret_key = $this->input->post('secret_key');
        
        if (empty($input_secret_key)) {
            echo json_encode([
                'status' => false,
                'message' => 'Secret key is required!'
            ]);
            return;
        }
        
        // Get stored secret key from database
        $app_settings = $this->db->get('tbl_aplikasi')->row();
        
        if (!$app_settings || empty($app_settings->uninstall_secret_key)) {
            echo json_encode([
                'status' => false,
                'message' => 'Secret key not found in database!'
            ]);
            return;
        }
        
        // Verify secret key using password_verify
        if (!password_verify($input_secret_key, $app_settings->uninstall_secret_key)) {
            echo json_encode([
                'status' => false,
                'message' => 'Invalid secret key! Please check and try again.'
            ]);
            return;
        }
        
        try {
            // 1. CLEANUP GENERATED FILES
            // Get all roles
            $this->load->model('Rbac_model');
            $roles = $this->Rbac_model->get_all_roles();
            
            foreach ($roles as $role) {
                $role_name = $role->role_name;
                // Skip default system roles if necessary, currently cleaning everything not core
                if (in_array($role_name, ['Super Admin', 'User'])) {
                    continue; // Optional: Preserve core roles files if they are static
                }

                $folder_name = strtolower(str_replace(' ', '_', $role_name));
                $controller_name = ucfirst(str_replace(' ', '', $role_name));
                
                // Delete Controller File
                $controller_path = APPPATH . 'controllers/' . $controller_name . '.php';
                if (file_exists($controller_path)) {
                    unlink($controller_path);
                }
                
                // Delete Sidebar File
                $sidebar_path = APPPATH . 'views/templates/sidebar_' . $folder_name . '.php';
                if (file_exists($sidebar_path)) {
                    unlink($sidebar_path);
                }
                
                // Delete View Folder and its contents
                $view_folder_path = APPPATH . 'views/' . $folder_name;
                if (is_dir($view_folder_path)) {
                    // Simple recursive delete for folder
                    $files = glob($view_folder_path . '/*');
                    foreach ($files as $file) {
                        if (is_file($file)) {
                            unlink($file);
                        }
                    }
                    rmdir($view_folder_path);
                }
            }

            // 2. CLEANUP CRUD GENERATED FILES
            // Get all CRUD history records
            $crud_history = $this->db->get('tbl_crud_history')->result();
            
            foreach ($crud_history as $crud) {
                // Delete Controller File
                $controller_path = APPPATH . 'controllers' . DIRECTORY_SEPARATOR . $crud->controller_name . '.php';
                if (file_exists($controller_path)) {
                    unlink($controller_path);
                }
                
                // Delete Model File
                $model_path = APPPATH . 'models' . DIRECTORY_SEPARATOR . $crud->model_name . '.php';
                if (file_exists($model_path)) {
                    unlink($model_path);
                }
                
                // Delete View Folder and its contents (recursively)
                // Normalize the path separators for cross-platform compatibility
                $view_folder_path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $crud->view_directory);
                
                if (is_dir($view_folder_path)) {
                    $this->delete_directory_recursive($view_folder_path);
                } else {
                    // Fallback: Try using table_name to construct the view path
                    $fallback_view_path = APPPATH . 'views' . DIRECTORY_SEPARATOR . $crud->table_name;
                    if (is_dir($fallback_view_path)) {
                        $this->delete_directory_recursive($fallback_view_path);
                    }
                }

                // 2a. CLEANUP UPLOADS FOLDER (TARGETED)
                // Delete uploads/table_name if it exists
                $upload_folder_path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $crud->table_name;
                if (is_dir($upload_folder_path)) {
                    $this->delete_directory_recursive($upload_folder_path);
                }
            }

            // 3. CLEANUP TEMPORARY/DEBUG FILES IN PROJECT ROOT
            $temp_files = [
                'Baca dulu.txt',
                'database_crud_history.sql',
                'debug_perms.php',
                'fix_perms_ci.php',
                'fix_perms_pdo.php',
                'insert_perms.sql',
                'master_project_ci3.sql',
                'module_id.txt',
                'perms_check.txt',
                'roles.txt',
                'temp_man_user_updates.php'
            ];
            
            foreach ($temp_files as $temp_file) {
                $file_path = FCPATH . $temp_file;
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // 4. CLEANUP UPLOADED IMAGES
            // Clean assets/dist/images (application logo folder)
            $images_folder = FCPATH . 'assets/dist/images';
            if (is_dir($images_folder)) {
                $files = glob($images_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        // Keep default images
                        if (!in_array($filename, ['default.png', 'default.jpg', 'default-logo.png', '.gitkeep', 'index.html'])) {
                            unlink($file);
                        }
                    }
                }
            }
            
            // Clean assets/dist/aplikasi (alternative logo folder if used)
            $aplikasi_folder = FCPATH . 'assets/dist/aplikasi';
            if (is_dir($aplikasi_folder)) {
                $files = glob($aplikasi_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        if (!in_array($filename, ['default.png', 'default.jpg', '.gitkeep', 'index.html'])) {
                            unlink($file);
                        }
                    }
                }
            }
            
            // Clean assets/dist/img/profile (user profile photos)
            $profile_folder = FCPATH . 'assets/dist/img/profile';
            if (is_dir($profile_folder)) {
                $files = glob($profile_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        // Keep only default.png
                        if (!in_array($filename, ['default.png', '.gitkeep', 'index.html'])) {
                            unlink($file);
                        }
                    }
                }
            }


            
            // Clean assets/images (installer logo folder)
            $assets_images_folder = FCPATH . 'assets/images';
            if (is_dir($assets_images_folder)) {
                $files = glob($assets_images_folder . '/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        $filename = basename($file);
                        // Keep default logo
                        if (!in_array($filename, ['default-logo.png', 'default.png', '.gitkeep', 'index.html'])) {
                            unlink($file);
                        }
                    }
                }
            }

            // 4. DROP DATABASE
            // Get database name from config
            $db_name = $this->db->database;
            
            // Drop the entire database
            $this->db->query("DROP DATABASE IF EXISTS `$db_name`");
            
            // Delete installed.lock file
            $lock_file = APPPATH . 'config/installed.lock';
            if (file_exists($lock_file)) {
                unlink($lock_file);
            }
            
            // Clear session
            $this->session->sess_destroy();
            
            // Return success
            echo json_encode([
                'status' => true,
                'message' => 'Application uninstalled successfully! Generated files deleted.'
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'status' => false,
                'message' => 'Uninstall failed: ' . htmlspecialchars($e->getMessage())
            ]);
        }
    }
    
    /**
     * Helper method to recursively delete a directory and all its contents
     * @param string $dir Directory path to delete
     * @return bool Success status
     */
    private function delete_directory_recursive($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }
        
        $items = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($items as $item) {
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            
            if (is_dir($path)) {
                $this->delete_directory_recursive($path);
            } else {
                unlink($path);
            }
        }
        
        return rmdir($dir);
    }
    
    // ============================================
    // NETWORK TOOLS METHODS
    // ============================================
    
    /**
     * Sanitize hostname/IP input to prevent command injection
     */
    private function sanitize_host($host)
    {
        // Remove dangerous characters, allow only alphanumeric, dots, hyphens
        $host = preg_replace('/[^a-zA-Z0-9\.\-]/', '', $host);
        // Limit length
        return substr($host, 0, 255);
    }
    
    /**
     * Execute ping command
     */
    public function network_ping()
    {
        $host = $this->input->post('host');
        $count = $this->input->post('count') ?: 4;
        
        if (empty($host)) {
            echo json_encode(['status' => false, 'message' => 'Hostname is required']);
            return;
        }
        
        $host = $this->sanitize_host($host);
        $count = min(max(intval($count), 1), 10); // Limit 1-10 pings
        
        // Windows ping command
        $command = "ping -n {$count} {$host}";
        
        $output = [];
        $return_var = 0;
        exec($command . " 2>&1", $output, $return_var);
        
        echo json_encode([
            'status' => true,
            'command' => "ping -n {$count} {$host}",
            'output' => implode("\n", $output),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * Execute netstat command
     */
    public function network_netstat()
    {
        $option = $this->input->post('option') ?: '-an';
        
        // Whitelist allowed options
        $allowed_options = ['-an', '-b', '-e', '-n', '-o', '-r', '-s'];
        if (!in_array($option, $allowed_options)) {
            $option = '-an';
        }
        
        $command = "netstat {$option}";
        
        $output = [];
        $return_var = 0;
        exec($command . " 2>&1", $output, $return_var);
        
        // Limit output to prevent memory issues
        $output = array_slice($output, 0, 200);
        
        echo json_encode([
            'status' => true,
            'command' => $command,
            'output' => implode("\n", $output),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * Execute traceroute (tracert) command
     */
    public function network_traceroute()
    {
        $host = $this->input->post('host');
        
        if (empty($host)) {
            echo json_encode(['status' => false, 'message' => 'Hostname is required']);
            return;
        }
        
        $host = $this->sanitize_host($host);
        
        // Windows tracert command with max 15 hops
        $command = "tracert -h 15 {$host}";
        
        $output = [];
        $return_var = 0;
        exec($command . " 2>&1", $output, $return_var);
        
        echo json_encode([
            'status' => true,
            'command' => "tracert {$host}",
            'output' => implode("\n", $output),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * Execute nslookup command
     */
    public function network_nslookup()
    {
        $host = $this->input->post('host');
        
        if (empty($host)) {
            echo json_encode(['status' => false, 'message' => 'Domain is required']);
            return;
        }
        
        $host = $this->sanitize_host($host);
        
        $command = "nslookup {$host}";
        
        $output = [];
        $return_var = 0;
        exec($command . " 2>&1", $output, $return_var);
        
        echo json_encode([
            'status' => true,
            'command' => $command,
            'output' => implode("\n", $output),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * Execute ipconfig command
     */
    public function network_ipconfig()
    {
        $option = $this->input->post('option') ?: '/all';
        
        // Whitelist allowed options
        $allowed_options = ['/all', '/release', '/renew', '/flushdns', '/displaydns'];
        if (!in_array($option, $allowed_options)) {
            $option = '/all';
        }
        
        // Only allow /all and /displaydns for viewing (not destructive)
        if (!in_array($option, ['/all', '/displaydns'])) {
            $option = '/all';
        }
        
        $command = "ipconfig {$option}";
        
        $output = [];
        $return_var = 0;
        exec($command . " 2>&1", $output, $return_var);
        
        // Limit output for /displaydns
        if ($option === '/displaydns') {
            $output = array_slice($output, 0, 300);
        }
        
        echo json_encode([
            'status' => true,
            'command' => $command,
            'output' => implode("\n", $output),
            'csrfHash' => $this->security->get_csrf_hash()
        ]);
    }
    
    /**
     * Get public IP address with location
     */
    public function get_public_ip()
    {
        $ip = null;
        $location = null;
        $country = null;
        $city = null;
        $isp = null;
        
        // Try ip-api.com first (includes location)
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents('http://ip-api.com/json', false, $context);
        
        if ($response !== false) {
            $data = json_decode($response, true);
            if ($data && isset($data['query']) && $data['status'] === 'success') {
                $ip = $data['query'];
                $country = isset($data['country']) ? $data['country'] : null;
                $city = isset($data['city']) ? $data['city'] : null;
                $isp = isset($data['isp']) ? $data['isp'] : null;
                $location = isset($data['countryCode']) ? $data['countryCode'] : null;
            }
        }
        
        // Fallback to ipinfo.io if ip-api fails
        if (!$ip) {
            $response = @file_get_contents('https://ipinfo.io/json', false, $context);
            if ($response !== false) {
                $data = json_decode($response, true);
                if ($data && isset($data['ip'])) {
                    $ip = $data['ip'];
                    $country = isset($data['country']) ? $data['country'] : null;
                    $city = isset($data['city']) ? $data['city'] : null;
                    $isp = isset($data['org']) ? $data['org'] : null;
                    $location = $country;
                }
            }
        }
        
        // Last fallback to ipify (no location)
        if (!$ip) {
            $response = @file_get_contents('https://api.ipify.org?format=json', false, $context);
            if ($response !== false) {
                $data = json_decode($response, true);
                if ($data && isset($data['ip'])) {
                    $ip = $data['ip'];
                }
            }
        }
        
        if ($ip) {
            echo json_encode([
                'status' => true,
                'ip' => $ip,
                'country' => $country,
                'city' => $city,
                'isp' => $isp,
                'location' => $location
            ]);
        } else {
            echo json_encode([
                'status' => false,
                'message' => 'Tidak terhubung ke jaringan internet'
            ]);
        }
    }
    
}
