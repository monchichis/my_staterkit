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
            <li>
                <a href="<?php echo base_url('user/index'); ?>"><i class="fa fa-home"></i> <span class="nav-label">Dashboards</span>  </a>

            </li>
          
      
           
            <li>
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
          <!--   <li>
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
    