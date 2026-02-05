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
            
            <!-- Dashboard -->
            <li id="menu-dashboard" data-tour-title="Dashboard" data-tour-content="Ini adalah halaman utama dashboard Anda, tempat Anda melihat ringkasan sistem.">
                <a href="<?php echo base_url('superadmin/index'); ?>"><i class="fa fa-home"></i> <span class="nav-label">Dashboard</span>  </a>
            </li>
       
            <!-- RBAC Management - Only for Super Admin and users with RBAC permissions -->
            <?php if (can('rbac.roles.view') || can('rbac.permissions.view') || has_role('Super Admin')): ?>
            <li id="menu-rbac" data-tour-title="RBAC Management" data-tour-content="Kelola Role, Permission, Modul, dan Parent Menu di sini untuk mengatur hak akses pengguna.">
                <a href="#"><i class="fa fa-shield"></i> <span class="nav-label">RBAC Management</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <?php if (can('rbac.roles.view') || has_role('Super Admin')): ?>
                    <li><a href="<?php echo base_url('rbac/roles'); ?>"><i class="fa fa-users"></i> Roles</a></li>
                    <?php endif; ?>
                    <?php if (can('rbac.modules.view') || has_role('Super Admin')): ?>
                    <li><a href="<?php echo base_url('rbac/modules'); ?>"><i class="fa fa-th"></i> Modules</a></li>
                    <li><a href="<?php echo base_url('rbac/parent_menus'); ?>"><i class="fa fa-sitemap"></i> Parent Menus</a></li>
                    <?php endif; ?>
                    <?php if (can('rbac.permissions.view') || has_role('Super Admin')): ?>
                    <li><a href="<?php echo base_url('rbac/permissions'); ?>"><i class="fa fa-key"></i> Permissions</a></li>
                    <?php endif; ?>
                    <?php if (can('rbac.assign.permissions') || has_role('Super Admin')): ?>
                    <li><a href="<?php echo base_url('rbac/assign_permissions'); ?>"><i class="fa fa-link"></i> Assign Permissions</a></li>
                    <?php endif; ?>
                    <?php if (can('rbac.assign.roles') || has_role('Super Admin')): ?>
                    <li><a href="<?php echo base_url('rbac/assign_user_roles'); ?>"><i class="fa fa-user-plus"></i> Assign User Roles</a></li>
                    <?php endif; ?>
                </ul>
            </li>
            <?php endif; ?>            
            <!-- User Management - Only for Super Admin -->
            <?php if (has_role('Super Admin')): ?>
            <li id="menu-user-management" data-tour-title="Manajemen User" data-tour-content="Kelola data pengguna, tambah pengguna baru dan atur profil pengguna.">
                <a href="<?php echo base_url('superadmin/man_user'); ?>"><i class="fa fa-users"></i> <span class="nav-label">Management User</span></a>
            </li>
            <?php endif; ?>

            
            <!-- Table Generator - For Super Admin and users with permission -->
            <?php if (has_role('Super Admin') || can('table.generator')): ?>
            <li id="menu-table-generator" data-tour-title="Table Generator" data-tour-content="Alat untuk membuat dan mengelola tabel database secara visual.">
                <a href="<?php echo base_url('DatabaseManager'); ?>"><i class="fa fa-database"></i> <span class="nav-label">Table Generator</span></a>
            </li>
            <?php endif; ?>
            
            <!-- Chart Generator - For Super Admin and users with permission -->
            <?php if (has_role('Super Admin') || can('chart.view')): ?>
            <li id="menu-chart-generator" data-tour-title="Chart Generator" data-tour-content="Buat dan kelola grafik dinamis untuk dashboard.">
                <a href="<?php echo base_url('superadmin/chart_generator'); ?>"><i class="fa fa-bar-chart"></i> <span class="nav-label">Chart Generator</span></a>
            </li>
            <?php endif; ?>
            
            <!-- Summary Widgets - For Super Admin -->
            <?php if (has_role('Super Admin')): ?>
            <li id="menu-summary-widgets" data-tour-title="Summary Widgets" data-tour-content="Buat widget ringkasan data dinamis untuk dashboard.">
                <a href="<?= base_url('summary_widget') ?>"><i class="fa fa-calculator"></i> <span class="nav-label">Summary Widgets</span></a>
            </li>
            <?php endif; ?>
            
            <!-- Query Builder - For Super Admin -->
            <?php if (has_role('Super Admin')): ?>
            <li id="menu-query-builder" data-tour-title="Query Builder" data-tour-content="Bangun query database yang kompleks dengan antarmuka grafis.">
                <a href="<?php echo base_url('QueryBuilder'); ?>"><i class="fa fa-search"></i> <span class="nav-label">Query Builder</span></a>
            </li>
            <?php endif; ?>
            
            <!-- CRUD Generator - For Super Admin -->
            <?php if (has_role('Super Admin')): ?>
            <li id="menu-crud-tools" data-tour-title="CRUD Tools" data-tour-content="Generate operasi CRUD (Create, Read, Update, Delete) secara otomatis.">
                <a href="#"><i class="fa fa-code"></i> <span class="nav-label">CRUD Tools</span><span class="fa arrow"></span></a>
                <ul class="nav nav-second-level collapse">
                    <li><a href="<?php echo base_url('CrudGenerator'); ?>"><i class="fa fa-plus-circle"></i> Generate CRUD</a></li>
                    <li><a href="<?php echo base_url('CrudGenerator/crud_history'); ?>"><i class="fa fa-history"></i> CRUD History</a></li>
                </ul>
            </li>
            <?php endif; ?>
            
            <!-- Uninstall Application - Only for Super Admin -->
            <?php if (has_role('Super Admin')): ?>
            <li id="menu-uninstall" data-tour-title="Uninstall Aplikasi" data-tour-content="Hapus instalasi aplikasi dan reset database (Hati-hati!).">
                <a href="#" class="uninstall-app"><i class="fa fa-trash-o text-danger"></i> <span class="nav-label text-danger">Uninstall Application</span></a>
            </li>
            <?php endif; ?>
           
            <!-- Logout -->
            <li id="menu-logout" data-tour-title="Logout" data-tour-content="Keluar dari sesi Anda saat ini.">
                <a href="#" class="logout"><i class="fa fa-sign-out"></i> <span class="nav-label">Logout </span></a>
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
                <a href="#" onclick="startTour(); return false;">
                    <i class="fa fa-question-circle"></i> Start Tour
                </a>
            </li>
            <li>
                <a href="#" class="logout">
                    <i class="fa fa-sign-out"></i> Log out
                </a>
            </li>
        </ul>

    </nav>
</div>
 <div class="">
    
