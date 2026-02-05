<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Database Manager</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Database Manager</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">

    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>List of Tables in Database</h5>
                    <div class="ibox-tools">
                        <a href="<?= base_url('DatabaseManager/create'); ?>" class="btn btn-primary btn-xs">Create New Table</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <?= $this->session->flashdata('message'); ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover dataTables-example">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Table Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1;
                                foreach ($tables as $table) : ?>
                                    <tr>
                                        <td><?= $i++; ?></td>
                                        <td><?= $table; ?></td>
                                        <td>
                                            <a href="<?= base_url('DatabaseManager/detail/' . $table); ?>" class="btn btn-info btn-xs"><i class="fa fa-eye"></i> Structure</a>
                                            <a href="<?= base_url('DatabaseManager/drop/' . $table); ?>" class="btn btn-danger btn-xs btn-hapus"><i class="fa fa-trash"></i> Drop</a>
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
        // Handle delete confirmation
        const deleteButtons = document.querySelectorAll('.btn-hapus');
        
        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const href = this.getAttribute('href');
                
                Swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this table!",
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
        
        // Display flashdata messages with SweetAlert
        <?php if ($this->session->flashdata('message_type')): ?>
            <?php 
                $message = strip_tags($this->session->flashdata('message'));
                $type = $this->session->flashdata('message_type');
                $title = $this->session->flashdata('message_title') ?: ($type === 'success' ? 'Success!' : 'Error!');
            ?>
            Swal.fire({
                title: "<?php echo addslashes($title); ?>",
                text: "<?php echo addslashes($message); ?>",
                icon: "<?php echo $type; ?>",
                confirmButtonText: "OK"
            });
        <?php endif; ?>
    });
</script>
