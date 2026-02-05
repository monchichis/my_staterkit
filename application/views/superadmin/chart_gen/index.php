<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Chart Generator</h2>
        <ol class="breadcrumb">
            <li>
                <a href="<?= site_url('dashboard') ?>">Home</a>
            </li>
            <li class="active">
                <strong>Chart Generator</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Chart List</h5>
                    <div class="ibox-tools">
                        <a href="<?= site_url('superadmin/chart_generator/add') ?>" class="btn btn-primary btn-xs">
                            <i class="fa fa-plus"></i> Create New Chart
                        </a>
                    </div>
                </div>
                <div class="ibox-content">

                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example" >
                            <thead>
                            <tr>
                                <th>No</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Placement</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $no = 1; foreach ($charts as $c): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $c->chart_title ?></td>
                                    <td><?= ucfirst($c->chart_type) ?></td>
                                    <td><?= $c->placement_identifier ?></td>
                                    <td>
                                        <?php if ($c->is_active): ?>
                                            <span class="label label-primary">Active</span>
                                        <?php else: ?>
                                            <span class="label label-danger">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('superadmin/chart_generator/edit/' . $c->id) ?>" class="btn btn-info btn-sm"><i class="fa fa-pencil"></i> Edit</a>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                            data-id="<?= $c->id ?>" 
                                            data-title="<?= htmlspecialchars($c->chart_title) ?>">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                        <form id="delete-form-<?= $c->id ?>" action="<?= site_url('superadmin/chart_generator/delete/' . $c->id) ?>" method="post" style="display:none;">
                                            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                        </form>
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

<script>
// Wait for jQuery to load (it's in footer.php which loads after this view)
(function checkJQuery() {
    if (typeof jQuery !== 'undefined' && typeof Swal !== 'undefined') {
        jQuery(document).ready(function($) {
            $('.btn-delete').on('click', function() {
                var chartId = $(this).data('id');
                var chartTitle = $(this).data('title');
                
                Swal.fire({
                    title: 'Hapus Chart?',
                    html: 'Apakah Anda yakin ingin menghapus chart <strong>"' + chartTitle + '"</strong>?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fa fa-trash"></i> Ya, Hapus!',
                    cancelButtonText: '<i class="fa fa-times"></i> Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#delete-form-' + chartId).submit();
                    }
                });
            });
        });
    } else {
        setTimeout(checkJQuery, 50);
    }
})();
</script>
