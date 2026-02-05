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
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
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

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // CSRF Protection (Global)
    var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
    var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

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
            Swal.fire({
                title: "Konfirmasi Logout",
                text: "Klik keluar untuk mengakhiri session!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Keluar",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = '<?=base_url('auth/logout'); ?>';
                }
            });
        });

        
        $('.backup').click(function () {
            Swal.fire({
                title: "Konfirmasi Backup",
                text: "Klik Backup Untuk Export Database!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Backup",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = '<?= base_url('superadmin/backup_database'); ?>';
                    Swal.fire("Berhasil!", "Data Anda telah dibackup.", "success");
                }
            });
        });
        
        $('.uninstall-app').click(function () {
            Swal.fire({
                title: "PERINGATAN: Uninstall Aplikasi",
                text: "Ini akan MENGHAPUS SELURUH DATABASE dan file installed.lock. Aplikasi akan kembali ke halaman install. Tindakan ini TIDAK DAPAT DIBATALKAN!",
                icon: "error",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d",
                confirmButtonText: "Ya, Lanjutkan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show password input using SweetAlert2
                    Swal.fire({
                        title: '<i class="fa fa-key"></i> Masukkan Secret Key',
                        html: '<p>Ketik secret key yang Anda buat saat instalasi:</p>',
                        input: 'password',
                        inputPlaceholder: 'Secret Key',
                        inputAttributes: {
                            autocomplete: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Uninstall',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        showLoaderOnConfirm: true,
                        preConfirm: (secretKey) => {
                            if (!secretKey) {
                                Swal.showValidationMessage('Secret key tidak boleh kosong!');
                                return false;
                            }
                            
                            var data = { secret_key: secretKey };
                            data[csrfName] = csrfHash;
                            
                            return $.ajax({
                                url: '<?= base_url('superadmin/uninstall'); ?>',
                                type: 'POST',
                                data: data,
                                dataType: 'json'
                            }).then(response => {
                                if (!response.status) {
                                    throw new Error(response.message);
                                }
                                return response;
                            }).catch(error => {
                                Swal.showValidationMessage(
                                    error.message || 'Terjadi kesalahan saat uninstall'
                                );
                            });
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Berhasil!',
                                text: result.value.message,
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href = '<?= base_url('install'); ?>';
                            });
                        }
                    });
                }
            });
        });

        const flashData = $('.flash-data').data('flashdata');
        if (flashData) {
            Swal.fire({
                title: flashData + ' Sukses',
                text: "",
                icon: 'success'
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
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Hapus'
        }).then((result) => {
            if (result.value) {
                document.location.href = href;
            }
        })
    });
</script>

<!-- Session Timeout Script -->
<script>
    $(document).ready(function() {
        // Get timeout from server (in seconds) or default to 5 minutes
        var timeoutSeconds = <?php echo isset($session_timeout) ? $session_timeout : 300; ?>;
        var warningSeconds = 30; // Show warning 30 seconds before logout
        
        var idleTimer;
        var warningTimer;
        
        function resetTimer() {
            clearTimeout(idleTimer);
            clearTimeout(warningTimer);
            
            // Only restart if user is logged in (id_user exists in session)
            // Since this footer is used in authenticated pages usually, we assume user is logged in.
            // But we can check if body has a specific class or check PHP session.
            <?php if($this->session->userdata('id_user')): ?>
                idleTimer = setTimeout(showTimeoutWarning, (timeoutSeconds - warningSeconds) * 1000);
            <?php endif; ?>
        }
        
        function showTimeoutWarning() {
            let timerInterval;
            Swal.fire({
                title: 'Sesi Akan Berakhir!',
                html: 'Sesi Anda akan berakhir dalam <b></b> detik.<br/>Apakah Anda ingin tetap masuk?',
                icon: 'warning',
                timer: warningSeconds * 1000,
                timerProgressBar: true,
                showCancelButton: true,
                confirmButtonText: 'Tetap Masuk',
                cancelButtonText: 'Keluar',
                confirmButtonColor: '#1ab394',
                cancelButtonColor: '#d33',
                didOpen: () => {
                    Swal.showLoading();
                    const b = Swal.getHtmlContainer().querySelector('b');
                    timerInterval = setInterval(() => {
                        b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
                    }, 100);
                },
                willClose: () => {
                    clearInterval(timerInterval);
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh session via AJAX
                    $.get('<?php echo base_url("auth/refresh_session"); ?>'); // Ensure this endpoint exists or just reload
                    resetTimer();
                } else if (result.dismiss === Swal.DismissReason.timer || result.dismiss === Swal.DismissReason.cancel) {
                    // Timeout or Click Logout
                    window.location.href = '<?php echo base_url("auth/logout"); ?>';
                }
            });
        }
        
        // Monitor events to reset timer
        $(document).on('mousemove keydown click scroll touchstart', function() {
            // We use a throttle/debounce mechanism implicitly by clearing and setting timeout
            // To prevent too many resets, we could add a throttle here if needed, 
            // but for simple idle timer, clearing timeout is fine.
            
            // However, to avoid clearing on EVERY mouse move which is expensive,
            // we can check if timer is running.
            // Actually, standard practice is just reset on activity.
            // Optimization: Throttling reset
            if (!resetTimer.throttled) {
                resetTimer();
                resetTimer.throttled = true;
                setTimeout(() => { resetTimer.throttled = false; }, 1000); // Throttle for 1 sec
            }
        });
        
        // Initial start
        resetTimer();
    });
</script>

<!-- Skin Switcher Script -->
<script src="<?php echo base_url('assets/'); ?>template/js/plugins/bootstrapTour/bootstrap-tour.min.js"></script>

<script>
    $(document).ready(function() {
        
        // Dynamic Step Generation
        var tourSteps = [];
        
        // Find all visible sidebar items with tour data
        // We select only visible items so maintenance mode or hidden items don't break the flow
        $('[data-tour-title]').each(function() {
            var $this = $(this);
            // Ensure ID exists, if not generate one (fallback)
            if (!$this.attr('id')) {
                $this.attr('id', 'tour-step-' + Math.floor(Math.random() * 1000));
            }
            
            tourSteps.push({
                element: "#" + $this.attr('id'),
                title: $this.data('tour-title'),
                content: $this.data('tour-content'),
                placement: "right"
            });
        });

        // Instance the tour
        var tour = new Tour({
            steps: tourSteps,
            backdrop: true,
            storage: window.localStorage,
            // Custom Template to match our new styles
            template: "<div class='popover tour'><div class='arrow'></div><h3 class='popover-header popover-title'></h3><div class='popover-body popover-content'></div><div class='popover-navigation'><div class='btn-group'><button class='btn btn-sm btn-default' data-role='prev'>« Prev</button><button class='btn btn-sm btn-default' data-role='next'>Next »</button></div><button class='btn btn-sm btn-default' data-role='end'>End tour</button></div></div>",
            onShown: function(tour) {
                // Force trigger the animation by adding the 'in' class if not present (bootstrap usually does `fade in`)
                $('.popover.tour').addClass('in');
            },
            onEnd: function (tour) {
                // Optional: Action when tour ends
            }
        });

        // Initialize the tour
        tour.init();

        // Start the tour
        tour.start();

        // Make startTour available globally so it can be called from button
        window.startTour = function() {
            tour.restart();
        };
    });
</script>

<script>
$(document).ready(function() {
    var skinToggle = $('#skinSwitcherToggle');
    var skinPanel = $('#skinSwitcherPanel');
    var skinOverlay = $('#skinOverlay');
    var skinClose = $('#skinPanelClose');
    
    // Open panel
    skinToggle.on('click', function() {
        skinPanel.addClass('open');
        skinOverlay.addClass('show');
        skinToggle.fadeOut(300);
    });
    
    // Close panel
    function closePanel() {
        skinPanel.removeClass('open');
        skinOverlay.removeClass('show');
        setTimeout(function() {
            skinToggle.fadeIn(300);
        }, 300);
    }
    
    skinClose.on('click', closePanel);
    skinOverlay.on('click', closePanel);
    
    // ESC key to close
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && skinPanel.hasClass('open')) {
            closePanel();
        }
    });
    
    // Change skin
    $('.skin-option').on('click', function() {
        var selectedSkin = $(this).data('skin');
        var $this = $(this);
        
        // Show loading
        Swal.fire({
            title: 'Mengubah Tema...',
            text: 'Mohon tunggu sebentar',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Save to database via AJAX
        var data = { skin: selectedSkin };
        data[csrfName] = csrfHash;
        
        $.ajax({
            url: '<?php echo base_url("superadmin/change_skin"); ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if(response.csrfHash) {
                    csrfHash = response.csrfHash;
                }
                
                if (response.status) {
                    // Update body class immediately
                    $('body').removeClass('skin-1 skin-2 skin-3 skin-4 md-skin').addClass(selectedSkin);
                    
                    // Update active state
                    $('.skin-option').removeClass('active');
                    $this.addClass('active');
                    
                    Swal.fire({
                        title: 'Berhasil!',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Close panel after success
                    setTimeout(closePanel, 1000);
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: response.message,
                        icon: 'error'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi kesalahan. Silakan coba lagi.',
                    icon: 'error'
                });
            }
        });
    });
});
</script>

</body>

</html>