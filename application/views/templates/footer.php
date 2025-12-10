<?php $identitas = $this->db->get('tbl_aplikasi')->row(); ?>

<br/><br/><br/>
<div class="footer" >
    <div class="float-right">
        Page rendered in <strong>{elapsed_time}</strong> seconds. <?php echo  (ENVIRONMENT === 'production') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
    </div>
    <div>
       Developer by : <span class="badge badge-primary"><?= $identitas->nama_developer ?></span> &copy; <?php echo date('Y'); ?>
    </div>
</div>

</div>
</div>

<!-- Mainly scripts -->
<script src="<?php echo base_url('assets/'); ?>template/js/jquery-3.1.1.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>template/js/popper.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/bootstrap.js"></script>
<script src="<?php echo base_url('assets/'); ?>template/js/plugins/metisMenu/jquery.metisMenu.js"></script>

<script src="<?php echo base_url('assets/'); ?>template/js/plugins/dataTables/datatables.min.js"></script>
    <script src="<?php echo base_url('assets/'); ?>template/js/plugins/dataTables/dataTables.bootstrap4.min.js"></script>
<script src="<?php echo base_url('assets/'); ?>template/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

<!-- Custom and plugin javascript -->
<script src="<?php echo base_url('assets/'); ?>template/js/inspinia.js"></script>
<script src="<?php echo base_url('assets/'); ?>template/js/plugins/pace/pace.min.js"></script>

<script src="<?php echo base_url('assets/'); ?>template/js/plugins/slimscroll/jquery.slimscroll.min.js"></script>


 <script src="<?php echo base_url('assets/'); ?>template/js/plugins/sweetalert/sweetalert.min.js"></script>


<script>

    $(document).ready(function () {

        // $('#tombol-logout').click(function(){
        //     swal({
        //         title: "Welcome in Alerts",
        //         text: "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
        //     });
        // });

        // $('.demo2').click(function(){
        //     swal({
        //         title: "Good job!",
        //         text: "You clicked the button!",
        //         type: "success"
        //     });
        // });

        $('.logout').click(function () {
            swal({
                title: "Konfirmasi Logout",
                text: "Klik keluar untuk mengakhiri session!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Keluar",
                closeOnConfirm: false
            }, function () {
                document.location.href = '<?=base_url('auth/logout'); ?>';
            });
            
        });

        
        $('.backup').click(function () {
            swal({
                title: "Konfirmasi Backup",
                text: "Klik Backup Untuk Export Database!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Backup",
                closeOnConfirm: false
            }, function () {
                document.location.href = '<?= base_url('admin/backup_database'); ?>';
                swal("Success!", "Your Data has been Backup.", "success");
            });
            
        });

        const flashData = $('.flash-data').data('flashdata');
        if (flashData) {
            swal({
                title: flashData + ' Sukses',
                text: "",
                type: 'success'
            });
        }




    });

</script>
<script>

    $(document).ready(function(){
            $('.dataTables-example').DataTable({
                pageLength: 10,
                responsive: true,
                dom: '<"html5buttons"B>lTfgitp',
                buttons: [
                    // { extend: 'copy'},
                    // {extend: 'csv'},
                    // {extend: 'excel', title: 'ExampleFile'},
                    // {extend: 'pdf', title: 'ExampleFile'},

                    // {extend: 'print',
                    //  customize: function (win){
                    //         $(win.document.body).addClass('white-bg');
                    //         $(win.document.body).css('font-size', '10px');

                    //         $(win.document.body).find('table')
                    //                 .addClass('compact')
                    //                 .css('font-size', 'inherit');
                    // }
                    // }
                ]

            });

        });
   
</script>
<script>
    $('.tombol-hapus').on('click', function(e) {
        e.preventDefault();
        const href = $(this).attr('href');
        Swal.fire({
            title: 'Yakin untuk menghapus ?',
            text: 'Data akan dihapus',
            type: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        })
    });
</script>

</body>

</html>