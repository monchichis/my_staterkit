<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?= $title ?>: <?= $widget->title ?></h2>
    </div>
    <div class="col-lg-2">
         <a href="<?= site_url('summary_widget') ?>" class="btn btn-default m-t-md">Back to List</a>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Data Detail (Max 1000 rows)</h5>
                </div>
                <div class="ibox-content">
                    <div class="alert alert-info">
                        Showing data from table <strong><?= $widget->table_name ?></strong> used for aggregate <strong><?= $widget->aggregate_func ?>(<?= $widget->column_name ?>)</strong>.
                    </div>
                
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <?php if(!empty($rows)): ?>
                                        <?php foreach($rows[0] as $key => $val): ?>
                                            <th><?= $key ?></th>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <th>No Data</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(!empty($rows)): ?>
                                    <?php foreach($rows as $row): ?>
                                    <tr>
                                        <?php foreach($row as $val): ?>
                                            <td><?= is_null($val) ? 'NULL' : htmlspecialchars($val) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="100">No matching records found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
