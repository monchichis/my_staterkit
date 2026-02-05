<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?php echo $title; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a><?php echo $title; ?></a>
            </li>
        </ol>
    </div>
</div>
<br/>
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Welcome to Your Dashboard</h5>
                </div>
                <div class="ibox-content">
                    <p>
                        You are logged in as <strong><?= $user['nama'] ?></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Summary Widgets (Automatically displayed based on role permissions) -->
    <?php if(!empty($summary_widgets)): ?>
    <div class="row">
        <?php foreach($summary_widgets as $w): ?>
        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="widget style1 <?= $w->bg_color_class ?>">
                <div class="row">
                    <div class="col-4">
                        <i class="fa fa-cubes fa-5x"></i>
                    </div>
                    <div class="col-8 text-right">
                        <span> <?= $w->title ?> </span>
                        <h2 class="font-bold"><?= is_numeric($w->value) ? number_format($w->value) : $w->value ?></h2>
                        <a href="#" class="view-widget-detail" data-widget-id="<?= $w->id ?>" style="color: rgba(255,255,255,0.8); font-size: 11px; text-decoration: underline;">View Detail</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Dynamic Charts (Automatically displayed based on role permissions) -->
    <?php $this->load->view('templates/chart_renderer', ['charts' => isset($charts) ? $charts : []]); ?>
</div>
