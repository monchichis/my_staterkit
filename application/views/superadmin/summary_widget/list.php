<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?= $title ?></h2>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>List of Summary Widgets</h5>
                    <div class="ibox-tools">
                        <a href="<?= site_url('summary_widget/create') ?>" class="btn btn-primary btn-xs">Create New Widget</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?php if($this->session->flashdata('message')): ?>
                        <div class="alert alert-success"><?= $this->session->flashdata('message') ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Table</th>
                                    <th>Column</th>
                                    <th>Aggregate</th>
                                    <th>Color</th>
                                    <th>Roles</th>
                                    <th>Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($widgets as $w): ?>
                                <tr>
                                    <td><?= $w->title ?></td>
                                    <td><?= $w->table_name ?></td>
                                    <td><?= $w->column_name ?></td>
                                    <td><?= $w->aggregate_func ?></td>
                                    <td><span class="badge <?= $w->bg_color_class ?>"><?= $w->bg_color_class ?></span></td>
                                    <td>
                                        <?php 
                                            $roles = json_decode($w->allowed_roles);
                                            echo is_array($roles) ? count($roles) . ' Roles' : 'None';
                                        ?>
                                    </td>
                                    <td><?= $w->is_active ? '<i class="fa fa-check text-navy"></i>' : '<i class="fa fa-times text-danger"></i>' ?></td>
                                    <td>
                                        <a href="<?= site_url('summary_widget/edit/' . $w->id) ?>" class="btn btn-xs btn-info"><i class="fa fa-pencil"></i></a>
                                        <a href="<?= site_url('summary_widget/delete/' . $w->id) ?>" class="btn btn-xs btn-danger delete-widget" data-title="<?= $w->title ?>"><i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
