<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>
<!-- Main Sidebar Container -->
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="sidebar-collapse">
        <ul class="nav metismenu" id="side-menu">
            <li class="nav-header">
                <div class="dropdown profile-element">
                    <img alt="image" width="50%" height="50%" class="rounded-circle" src="<?php echo base_url('assets/dist/img/profile/' . $user['image']); ?>"/>
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <span class="block m-t-xs font-bold"><?php echo $user['nama']; ?></span>
                        <span class="text-muted text-xs block"><?php echo $user['level']; ?> <b class="caret"></b></span>
                    </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                     
                        <li><a class="dropdown-item logout" href="#">Logout</a></li>
                    </ul>
                </div>
                <div class="logo-element">
                    IN+
                </div>
            </li>
            
            <!-- Dashboard untuk Role ini -->
            <?php 
            $role_slug = strtolower(str_replace(' ', '_', $this->session->userdata('level')));
            ?>
            <li id="menu-dashboard" data-tour-title="Dashboard" data-tour-content="Ini adalah halaman utama dashboard Anda.">
                <a href="<?php echo base_url($role_slug); ?>"><i class="fa fa-home"></i> <span class="nav-label">Dashboard</span></a>
            </li>

            <!-- Dynamic Modules with Parent Menu Grouping -->
            <?php 
            // Check for Maintenance Mode
            $is_maintenance = isset($maintenance_mode) && $maintenance_mode == 1;
            $user_role = $this->session->userdata('level');

            // Only show dynamic menus if NOT in maintenance mode OR if user is Super Admin
            if (!$is_maintenance || $user_role == 'Super Admin'):

                $ci =& get_instance();
                $ci->load->model('Rbac_model');
                
                $user_id = $this->session->userdata('id_user');
                
                // Get modules grouped by parent menu
                $parent_menus_grouped = $ci->Rbac_model->get_modules_grouped_by_parent_menu($user_id);
                $standalone_modules = $ci->Rbac_model->get_standalone_modules($user_id);
                
                // Render standalone modules (no parent menu) as top-level items
                // Skip 'dashboard' controller since each role has its own dashboard
                foreach ($standalone_modules as $module): 
                    if (!$module->parent_id && strtolower($module->controller_name) != 'dashboard'): // Only top level, skip dashboard
                ?>
                <li id="menu-<?php echo $module->controller_name; ?>" data-tour-title="<?php echo $module->module_name; ?>" data-tour-content="Akses fitur <?php echo $module->module_name; ?> di sini.">
                    <a href="<?php echo base_url($module->controller_name); ?>">
                        <i class="<?php echo $module->icon; ?>"></i> 
                        <span class="nav-label"><?php echo $module->module_name; ?></span>
                    </a>
                </li>
                <?php 
                    endif;
                endforeach; 
                
                // Render parent menus with their child modules
                foreach ($parent_menus_grouped as $parent_menu): 
                    // Only show parent menu if it has modules
                    if (!empty($parent_menu['modules'])):
                ?>
                <li id="menu-parent-<?php echo str_replace(' ', '', $parent_menu['menu_name']); ?>" data-tour-title="<?php echo $parent_menu['menu_name']; ?>" data-tour-content="Menu grup <?php echo $parent_menu['menu_name']; ?> yang berisi modul-modul terkait.">
                    <a href="#"><i class="<?php echo $parent_menu['icon']; ?>"></i> <span class="nav-label"><?php echo $parent_menu['menu_name']; ?></span><span class="fa arrow"></span></a>
                    <ul class="nav nav-second-level collapse">
                        <?php foreach ($parent_menu['modules'] as $module): ?>
                        <li>
                            <a href="<?php echo base_url($module['controller_name']); ?>">
                                <i class="<?php echo $module['icon']; ?>"></i> <?php echo $module['module_name']; ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php 
                    endif;
                endforeach; 
            
            else:
            ?>
                <!-- Maintenance Message for Non-Super Admin -->
                <li>
                    <div class="alert alert-danger m-2" style="font-size: 11px;">
                        <i class="fa fa-warning"></i> MAINTENANCE MODE ACTIVE
                        <br>Menu dinonaktifkan sementara.
                    </div>
                </li>
            <?php endif; ?>
           
            <!-- Logout -->
            <li id="menu-logout" data-tour-title="Logout" data-tour-content="Keluar dari sesi Anda saat ini.">
                <a href="#" class="logout"><i class="fa fa-sign-out"></i> <span class="nav-label">Logout </span></a>
            </li>

            <li>
                <a href="#" onclick="startTour(); return false;">
                    <i class="fa fa-question-circle"></i> Start Tour
                </a>
            </li>
           
        </ul>

    </div>
</nav>


<div id="page-wrapper" class="gray-bg">
<div class="row border-bottom">
    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
            <form role="search" class="navbar-form-custom" action="search_results.html">
                <div class="form-group">
                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                </div>
            </form>
        </div>
        <ul class="nav navbar-top-links navbar-right">
           <!--  <li>

                
                <span class="m-r-sm text-muted welcome-message"><marquee><?= $identitas->nama_aplikasi ?></marquee></span>
            </li> -->
          

            <li>
                <a href="#" class="logout">
                    <i class="fa fa-sign-out"></i> Log out
                </a>
            </li>
        </ul>

    </nav>
</div>
 <div class="">
    
