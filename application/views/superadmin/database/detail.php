<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Table Structure: <?= $table_name; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager'); ?>">Database Manager</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Table Detail</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Structure for table '<?= $table_name; ?>'</h5>
                    <div class="ibox-tools">
                         <a href="<?= base_url('DatabaseManager/add_column/' . $table_name); ?>" class="btn btn-primary btn-xs"><i class="fa fa-plus"></i> Add Column</a>
                         <a href="<?= base_url('DatabaseManager'); ?>" class="btn btn-default btn-xs"><i class="fa fa-arrow-left"></i> Back to List</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?= $this->session->flashdata('message'); ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Field</th>
                                    <th>Type</th>
                                    <th>Max Length</th>
                                    <th>Price Key</th>
                                    <th>Default</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($fields as $field) : ?>
                                    <tr>
                                        <td><?= $field->name; ?></td>
                                        <td><?= $field->type; ?></td>
                                        <td><?= $field->max_length; ?></td>
                                        <td><?= $field->primary_key ? 'YES' : 'NO'; ?></td>
                                        <td><?= $field->default; ?></td>
                                        <td>
                                            <a href="<?= base_url('DatabaseManager/edit_column/' . $table_name . '/' . $field->name); ?>" class="btn btn-warning btn-xs"><i class="fa fa-edit"></i> Edit</a>
                                            <a href="<?= base_url('DatabaseManager/drop_column/' . $table_name . '/' . $field->name); ?>" class="btn btn-danger btn-xs btn-hapus-col"><i class="fa fa-trash"></i> Drop</a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.btn-hapus-col');
        
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this column!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#6c757d",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        });
    });
</script>
