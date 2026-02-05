<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
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
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Global CSRF Protection -->
            <script>
            var csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
            var csrfHash = '<?= $this->security->get_csrf_hash(); ?>';
            
            function updateCsrf(newHash) {
                if(!newHash) return;
                csrfHash = newHash;
                var inputs = document.getElementsByName(csrfName);
                for(var i=0; i<inputs.length; i++) {
                    inputs[i].value = newHash;
                }
            }
            </script>
            <div class="flash-data" data-flashdata="<?= $this->session->flashdata('message'); ?>"></div>
            <?php echo $this->session->flashdata('msg'); ?>
            <?php if (validation_errors()) { ?>
                <div class="alert alert-danger">
                    <a class="close" data-dismiss="alert">x</a>
                    <strong><?php echo strip_tags(validation_errors()); ?></strong>
                </div>
            <?php } ?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="widget-head-color-box navy-bg p-lg text-center">
                        <div class="m-b-md">
                            <h2 class="font-bold no-margins">
                                Selamat Datang Di <?= isset($identitas->nama_aplikasi) ? $identitas->nama_aplikasi : 'Aplikasi'; ?>
                            </h2>
                            <strong><?php echo $user['nama']; ?></strong>
                        </div>

                        <?php if (!empty($identitas->logo)): ?>
                            <img src="<?php echo base_url('assets/images/') . $identitas->logo; ?>" 
                                 alt="<?php echo isset($identitas->nama_aplikasi) ? $identitas->nama_aplikasi : 'Logo'; ?>" 
                                 class="img-circle elevation-3" 
                                 style="height:100px; width:100px; object-fit:contain; background: white; padding: 10px; border: 3px solid #1ab394;">
                        <?php else: ?>
                            <h1 class="logo-name" style="color:#fff;">IN+</h1>
                        <?php endif; ?>
                    </div>
                    <div class="widget-text-box">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-info btn-sm float-right ml-1" data-toggle="modal" data-target="#ubah-pass">Ubah Password</button> 
                                <button type="button" class="btn btn-info btn-sm float-right" data-toggle="modal" data-target="#profile">Ubah Profil</button> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Summary Widgets -->
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
            
            <!-- Dynamic Charts -->
            <?php $this->load->view('templates/chart_renderer', ['charts' => isset($charts) ? $charts : []]); ?>
            
            <!-- Maintenance Mode & System Information Row -->
            <div class="row mt-4">
                <!-- Maintenance Widget (Left) -->
                <div class="col-lg-6">
                     <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-cogs"></i> System & Maintenance</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h3 class="font-bold no-margins">Maintenance Mode</h3>
                                    <p class="text-muted mb-0">
                                        Aktifkan mode maintenance untuk membatasi akses user selain Super Admin.
                                        <br>
                                        <small class="text-warning"><i class="fa fa-warning"></i> Saat aktif, hanya Super Admin yang dapat mengakses dashboard.</small>
                                    </p>
                                </div>
                                <div class="col-md-4 text-right">
                                    <style>
                                        /* Custom Toggle Switch */
                                        .switch-toggle {
                                            position: relative;
                                            display: inline-block;
                                            width: 60px;
                                            height: 34px;
                                        }
                                        .switch-toggle input { 
                                            opacity: 0;
                                            width: 0;
                                            height: 0;
                                        }
                                        .slider-toggle {
                                            position: absolute;
                                            cursor: pointer;
                                            top: 0;
                                            left: 0;
                                            right: 0;
                                            bottom: 0;
                                            background-color: #ccc;
                                            -webkit-transition: .4s;
                                            transition: .4s;
                                        }
                                        .slider-toggle:before {
                                            position: absolute;
                                            content: "";
                                            height: 26px;
                                            width: 26px;
                                            left: 4px;
                                            bottom: 4px;
                                            background-color: white;
                                            -webkit-transition: .4s;
                                            transition: .4s;
                                        }
                                        input:checked + .slider-toggle {
                                            background-color: #ed5565; /* Red warning color for maintenance */
                                        }
                                        input:focus + .slider-toggle {
                                            box-shadow: 0 0 1px #ed5565;
                                        }
                                        input:checked + .slider-toggle:before {
                                            -webkit-transform: translateX(26px);
                                            -ms-transform: translateX(26px);
                                            transform: translateX(26px);
                                        }
                                        .slider-toggle.round {
                                            border-radius: 34px;
                                        }
                                        .slider-toggle.round:before {
                                            border-radius: 50%;
                                        }
                                    </style>
                                    <label class="switch-toggle">
                                        <input type="checkbox" id="maintenance-toggle" <?php echo (isset($identitas->maintenance_mode) && $identitas->maintenance_mode == 1) ? 'checked' : ''; ?>>
                                        <span class="slider-toggle round"></span>
                                    </label>
                                    <div class="mt-2">
                                        <span id="maintenance-status" class="font-bold <?php echo (isset($identitas->maintenance_mode) && $identitas->maintenance_mode == 1) ? 'text-danger' : 'text-muted'; ?>">
                                            <?php echo (isset($identitas->maintenance_mode) && $identitas->maintenance_mode == 1) ? 'ACTIVE' : 'INACTIVE'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            </div>
                            
                            <hr class="hr-line-dashed">
                            
                            <div class="p-3" style="background: #fdfdfe; border: 1px solid #e7eaec; border-radius: 5px;">
                                <div class="row align-items-center">
                                    <div class="col-md-7">
                                        <h3 class="font-bold no-margins" style="font-size: 16px;">Environment Mode</h3>
                                        <p class="text-muted mb-0" style="font-size: 12px;">
                                            Atur environment aplikasi (Development/Testing/Production).
                                            <br>
                                            <small class="text-info"><i class="fa fa-info-circle"></i> Mempengaruhi pelaporan error.</small>
                                        </p>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="d-flex align-items-center justify-content-end">
                                            <div class="mr-2 flex-grow-1" style="max-width: 150px;">
                                                <select id="env-selector" class="form-control form-control-sm" style="font-weight:600; border-color: #e7eaec; height: 32px;">
                                                    <option value="development" <?php echo ENVIRONMENT == 'development' ? 'selected' : ''; ?>>Development</option>
                                                    <option value="testing" <?php echo ENVIRONMENT == 'testing' ? 'selected' : ''; ?>>Testing</option>
                                                    <option value="production" <?php echo ENVIRONMENT == 'production' ? 'selected' : ''; ?>>Production</option>
                                                </select>
                                            </div>
                                            <span id="env-status" class="badge badge-<?php 
                                                echo ENVIRONMENT == 'production' ? 'primary' : 
                                                    (ENVIRONMENT == 'testing' ? 'warning' : 'danger'); 
                                            ?> p-2" style="min-width: 100px; text-align: center; font-size: 11px;">
                                                <?php echo strtoupper(ENVIRONMENT); ?>
                                            </span>
                                        </div>
                                        
                                        <!-- Script moved inside the same container for organization -->
                                        <script>
                                        (function waitForJQuery() {
                                            if (typeof jQuery === 'undefined') {
                                                setTimeout(waitForJQuery, 50);
                                                return;
                                            }
                                            
                                            $(document).ready(function() {
                                                // Unbind previous events to avoid duplicates if this partial is reloaded
                                                $('#env-selector').off('change').on('change', function() {
                                                    var env = $(this).val();
                                                    var currentEnv = '<?php echo ENVIRONMENT; ?>';
                                                    var $selector = $(this);
                                                    
                                                    Swal.fire({
                                                        title: 'Konfirmasi Perubahan',
                                                        html: "Ubah environment dari <b>" + currentEnv.toUpperCase() + "</b> ke <b>" + env.toUpperCase() + "</b>?",
                                                        icon: 'question',
                                                        showCancelButton: true,
                                                        confirmButtonColor: '#1ab394',
                                                        cancelButtonColor: '#d33',
                                                        confirmButtonText: 'Ya, Ubah!',
                                                        cancelButtonText: 'Batal',
                                                        reverseButtons: true
                                                    }).then((result) => {
                                                        if (result.isConfirmed) {
                                                            var data = {env: env};
                                                            data[csrfName] = csrfHash;
                                                            
                                                            $.ajax({
                                                                url: '<?php echo base_url("superadmin/change_environment"); ?>',
                                                                type: 'POST',
                                                                data: data,
                                                                dataType: 'json',
                                                                beforeSend: function() {
                                                                    Swal.fire({
                                                                        title: 'Memproses...',
                                                                        allowOutsideClick: false,
                                                                        onBeforeOpen: () => {
                                                                            Swal.showLoading()
                                                                        }
                                                                    });
                                                                },
                                                                success: function(response) {
                                                                    updateCsrf(response.csrfHash);
                                                                    if(response.status) {
                                                                        Swal.fire({
                                                                            icon: 'success',
                                                                            title: 'Berhasil',
                                                                            text: response.message,
                                                                            timer: 1500,
                                                                            showConfirmButton: false
                                                                        }).then(() => {
                                                                            location.reload();
                                                                        });
                                                                    } else {
                                                                        Swal.fire('Error', response.message, 'error');
                                                                        $selector.val(currentEnv); // Reset on error
                                                                    }
                                                                },
                                                                error: function() {
                                                                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                                                                    $selector.val(currentEnv); // Reset on error
                                                                }
                                                            });
                                                        } else {
                                                            $selector.val(currentEnv); // Reset selection
                                                        }
                                                    });
                                                });
                                            });
                                        })();
                                        </script>
                                    </div>
                                </div>
                            </div>
                    </div>
                    
                    <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        var toggle = document.getElementById('maintenance-toggle');
                        var statusText = document.getElementById('maintenance-status');

                        toggle.addEventListener('change', function() {
                            var isChecked = this.checked;
                            var status = isChecked ? 1 : 0;
                            
                            // Update UI immediately (optimistic)
                            if (isChecked) {
                                statusText.textContent = 'ACTIVE';
                                statusText.className = 'font-bold text-danger';
                            } else {
                                statusText.textContent = 'INACTIVE';
                                statusText.className = 'font-bold text-muted';
                            }
                            
                            var data = {status: status};
                            data[csrfName] = csrfHash;
                            
                            $.ajax({
                                url: '<?php echo base_url("superadmin/toggle_maintenance"); ?>',
                                type: 'POST',
                                data: data,
                                dataType: 'json',
                                success: function(response) {
                                    updateCsrf(response.csrfHash);
                                    if(response.status) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        // Revert on failure
                                        toggle.checked = !isChecked;
                                        Swal.fire('Error', 'Gagal mengubah status', 'error');
                                    }
                                },
                                error: function() {
                                    toggle.checked = !isChecked;
                                    Swal.fire('Error', 'Terjadi kesalahan server', 'error');
                                }
                            });
                        });
                    });
                    </script>
                </div>

                <!-- System Information Widget (Right) -->
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-info-circle"></i> System Information</h5>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="stat-card text-center p-3" style="background: linear-gradient(145deg, #f8f9fa, #e9ecef); border-radius: 10px;">
                                        <div class="stat-icon mb-2">
                                            <i class="fa fa-globe fa-2x text-primary"></i>
                                        </div>
                                        <h4 class="mb-1" style="font-weight: 600;"><?php echo isset($timezone_info['timezone']) ? $timezone_info['timezone'] : 'Asia/Jakarta'; ?></h4>
                                        <p class="text-muted mb-0">
                                            <small>UTC<?php echo isset($timezone_info['offset']) ? $timezone_info['offset'] : '+07:00'; ?></small>
                                        </p>
                                        <span class="badge badge-primary mt-2">Default Timezone</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card text-center p-3" style="background: linear-gradient(145deg, #f8f9fa, #e9ecef); border-radius: 10px;">
                                        <div class="stat-icon mb-2">
                                            <i class="fa fa-clock-o fa-2x text-success"></i>
                                        </div>
                                        <h4 class="mb-1" style="font-weight: 600;" id="current-time"><?php echo date('H:i:s'); ?></h4>
                                        <p class="text-muted mb-0">
                                            <small id="current-date"><?php echo date('l, d F Y'); ?></small>
                                        </p>
                                        <span class="badge badge-success mt-2">Server Time</span>
                                    </div>
                                </div>
                                
                                <!-- Server Time Data for JavaScript -->
                                <script>
                                    var serverTimeData = {
                                        timezone: '<?php echo isset($timezone_info['timezone']) ? $timezone_info['timezone'] : 'Asia/Jakarta'; ?>',
                                        offset: '<?php echo isset($timezone_info['offset']) ? $timezone_info['offset'] : '+07:00'; ?>',
                                        initialTime: new Date('<?php echo date('c'); ?>') // ISO 8601 format with timezone
                                    };
                                </script>
                                <div class="col-md-4">
                                    <div class="stat-card text-center p-3" style="background: linear-gradient(145deg, #f8f9fa, #e9ecef); border-radius: 10px;">
                                        <div class="stat-icon mb-2">
                                            <i class="fa fa-server fa-2x text-warning"></i>
                                        </div>
                                        <h4 class="mb-1" style="font-weight: 600;">PHP <?php echo phpversion(); ?></h4>
                                        <p class="text-muted mb-0">
                                            <small>CodeIgniter <?php echo CI_VERSION; ?></small>
                                        </p>
                                        <span class="badge badge-warning mt-2">Framework</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Speed Test Widget with Animated Speedometer & Network Tools Widget -->
            <div class="row">
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-tachometer"></i> Internet Speed Test</h5>
                        </div>
                        <div class="ibox-content">
                            <style>
                            .offline-state {
                                text-align: center;
                                padding: 50px 20px;
                                background: #fdfdfe;
                                border-radius: 12px;
                                border: 2px dashed #ed5565;
                                margin-bottom: 25px;
                                position: relative;
                                overflow: hidden;
                            }
                            .offline-icon-container {
                                position: relative;
                                display: inline-block;
                                width: 80px;
                                height: 80px;
                                margin-bottom: 20px;
                            }
                            .offline-icon {
                                font-size: 50px;
                                color: #ed5565;
                                position: relative;
                                z-index: 2;
                                line-height: 80px;
                            }
                            .offline-pulse {
                                position: absolute;
                                top: 50%;
                                left: 50%;
                                transform: translate(-50%, -50%);
                                width: 100%;
                                height: 100%;
                                background: rgba(237, 85, 101, 0.2);
                                border-radius: 50%;
                                animation: radar-pulse 2s infinite;
                                z-index: 1;
                            }
                            @keyframes radar-pulse {
                                0% { width: 0; height: 0; opacity: 0.8; }
                                100% { width: 200%; height: 200%; opacity: 0; }
                            }
                            .offline-message h3 {
                                color: #ed5565;
                                font-weight: 700;
                                margin-bottom: 10px;
                            }
                            .reconnect-spinner {
                                margin-top: 20px;
                                font-size: 14px;
                                color: #888;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                gap: 10px;
                            }
                            </style>

                            <div id="offline-warning" class="offline-state" style="display: none;">
                                <div class="offline-icon-container">
                                    <div class="offline-pulse"></div>
                                    <i class="fa fa-wifi offline-icon"></i>
                                </div>
                                <div class="offline-message">
                                    <h3>Koneksi Terputus!</h3>
                                    <p class="text-muted">Aplikasi tidak dapat menjangkau server speedtest.<br>Silakan periksa koneksi internet Anda.</p>
                                    <div class="reconnect-spinner">
                                        <i class="fa fa-circle-o-notch fa-spin"></i> Mencoba menghubungkan kembali...
                                    </div>
                                </div>
                            </div>
                            
                            <!--OST Widget code start-->
                            <div id="speedtest-widget-container" style="text-align:right;">
                                <div style="min-height:360px;">
                                    <div style="width:100%;height:0;padding-bottom:50%;position:relative;">
                                        <iframe style="border:none;position:absolute;top:0;left:0;width:100%;height:100%;min-height:360px;border:none;overflow:hidden !important;" src="//openspeedtest.com/speedtest"></iframe>
                                    </div>
                                </div>
                                Provided by <a href="https://openspeedtest.com">OpenSpeedtest.com</a>
                            </div>
                            <!-- OST Widget code end -->

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    var offlineWarning = document.getElementById('offline-warning');
                                    var widgetContainer = document.getElementById('speedtest-widget-container');
                                    
                                    function updateOnlineStatus() {
                                        if (navigator.onLine) {
                                            if(offlineWarning) offlineWarning.style.display = 'none';
                                            if(widgetContainer) widgetContainer.style.display = 'block';
                                        } else {
                                            if(offlineWarning) offlineWarning.style.display = 'block';
                                            if(widgetContainer) widgetContainer.style.display = 'none';
                                        }
                                    }

                                    window.addEventListener('online', updateOnlineStatus);
                                    window.addEventListener('offline', updateOnlineStatus);
                                    
                                    updateOnlineStatus();
                                });
                            </script>
</div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ibox">
                        <div class="ibox-title">
                            <h5><i class="fa fa-terminal"></i> Network Tools</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                            </div>
                        </div>
                        <div class="ibox-content">
                            <style>
                                .network-tools-container {
                                    background: #f8f9fa;
                                    border-radius: 10px;
                                    padding: 20px;
                                }
                                .tool-tabs {
                                    display: flex;
                                    gap: 10px;
                                    flex-wrap: wrap;
                                    margin-bottom: 20px;
                                }
                                .tool-tab {
                                    padding: 10px 20px;
                                    border: 2px solid #e0e0e0;
                                    border-radius: 25px;
                                    background: white;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    font-weight: 500;
                                }
                                .tool-tab:hover {
                                    border-color: #1ab394;
                                    background: #f0fff8;
                                }
                                .tool-tab.active {
                                    border-color: #1ab394;
                                    background: #1ab394;
                                    color: white;
                                }
                                .tool-tab i {
                                    margin-right: 8px;
                                }
                                .tool-input-group {
                                    display: flex;
                                    gap: 10px;
                                    margin-bottom: 15px;
                                    flex-wrap: wrap;
                                }
                                .tool-input-group input {
                                    flex: 1;
                                    min-width: 200px;
                                    padding: 12px 15px;
                                    border: 2px solid #e0e0e0;
                                    border-radius: 8px;
                                    font-size: 14px;
                                }
                                .tool-input-group input:focus {
                                    border-color: #1ab394;
                                    outline: none;
                                }
                                .tool-input-group select {
                                    padding: 12px 15px;
                                    border: 2px solid #e0e0e0;
                                    border-radius: 8px;
                                    font-size: 14px;
                                    background: white;
                                }
                                .tool-buttons {
                                    display: flex;
                                    gap: 10px;
                                }
                                .tool-buttons .btn {
                                    padding: 12px 25px;
                                    border-radius: 8px;
                                    font-weight: 600;
                                }
                                .terminal-output {
                                    background: #1e1e1e;
                                    color: #00ff00;
                                    font-family: 'Consolas', 'Monaco', monospace;
                                    font-size: 13px;
                                    padding: 20px;
                                    border-radius: 10px;
                                    min-height: 300px;
                                    max-height: 500px;
                                    overflow-y: auto;
                                    white-space: pre-wrap;
                                    word-wrap: break-word;
                                    line-height: 1.5;
                                    width: 100%;
                                }
                                
                                @media (max-width: 768px) {
                                    .network-tools-container {
                                        padding: 10px;
                                    }
                                    
                                    .terminal-output {
                                        font-size: 11px;
                                        padding: 10px;
                                        min-height: 250px;
                                    }
                                    
                                    .tool-tabs {
                                        gap: 5px;
                                    }
                                    
                                    .tool-tab {
                                        padding: 8px 12px;
                                        font-size: 12px;
                                        flex: 1 1 auto;
                                        text-align: center;
                                    }
                                    
                                    .tool-buttons {
                                        flex-direction: column;
                                    }
                                    
                                    .tool-buttons .btn {
                                        width: 100%;
                                        justify-content: center;
                                    }
                                }
                                .terminal-output .command-line {
                                    color: #00bfff;
                                    margin-bottom: 10px;
                                    border-bottom: 1px solid #333;
                                    padding-bottom: 10px;
                                }
                                .terminal-output .loading {
                                    color: #ffc107;
                                }
                                .terminal-output .error {
                                    color: #ff6b6b;
                                }
                                .tool-description {
                                    font-size: 13px;
                                    color: #666;
                                    margin-bottom: 15px;
                                    padding: 10px 15px;
                                    background: #fff;
                                    border-left: 4px solid #1ab394;
                                    border-radius: 4px;
                                }
                            </style>
                            
                            <div class="network-tools-container">
                                <!-- Public IP Display -->
                                <div class="public-ip-display mb-3" style="background: linear-gradient(135deg, #1ab394, #0d8a70); border-radius: 10px; padding: 12px 15px; color: white;">
                                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                                        <div style="display: flex; align-items: center;">
                                            <i class="fa fa-globe" style="font-size: 24px; margin-right: 12px;"></i>
                                            <div>
                                                <div style="font-size: 11px; opacity: 0.8;">Public IP Address</div>
                                                <div id="public-ip-value" style="font-size: 18px; font-weight: 600;">
                                                    <i class="fa fa-spinner fa-spin"></i> Loading...
                                                </div>
                                            </div>
                                        </div>
                                        <div style="text-align: right;">
                                            <div style="font-size: 11px; opacity: 0.8;">Location</div>
                                            <div id="public-ip-location" style="font-size: 14px; font-weight: 500;">
                                                <i class="fa fa-spinner fa-spin"></i>
                                            </div>
                                        </div>
                                        <button type="button" id="refresh-ip-btn" class="btn btn-sm" style="background: rgba(255,255,255,0.2); border: none; color: white; padding: 5px 10px;" title="Refresh IP">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </div>
                                    <div id="public-ip-isp" style="font-size: 11px; opacity: 0.7; margin-top: 5px;"></div>
                                </div>
                                
                                <!-- Tool Tabs -->
                                <div class="tool-tabs">
                                    <button type="button" class="tool-tab active" data-tool="ping">
                                        <i class="fa fa-exchange"></i> Ping
                                    </button>
                                    <button type="button" class="tool-tab" data-tool="netstat">
                                        <i class="fa fa-sitemap"></i> Netstat
                                    </button>
                                    <button type="button" class="tool-tab" data-tool="traceroute">
                                        <i class="fa fa-road"></i> Traceroute
                                    </button>
                                    <button type="button" class="tool-tab" data-tool="nslookup">
                                        <i class="fa fa-search"></i> NSLookup
                                    </button>
                                    <button type="button" class="tool-tab" data-tool="ipconfig">
                                        <i class="fa fa-info-circle"></i> IPConfig
                                    </button>
                                </div>
                                
                                <!-- Tool Description -->
                                <div class="tool-description" id="tool-description">
                                    <strong>Ping:</strong> Test konektivitas jaringan ke host tujuan dengan mengirimkan paket ICMP.
                                </div>
                                
                                <!-- Input Group -->
                                <div class="tool-input-group" id="tool-input-group">
                                    <input type="text" id="network-host" placeholder="Masukkan hostname atau IP (contoh: google.com)" value="google.com">
                                    <select id="ping-count">
                                        <option value="4">4 Ping</option>
                                        <option value="6">6 Ping</option>
                                        <option value="8">8 Ping</option>
                                        <option value="10">10 Ping</option>
                                    </select>
                                </div>
                                
                                <!-- Buttons -->
                                <div class="tool-buttons">
                                    <button type="button" class="btn btn-primary" id="execute-network-tool">
                                        <i class="fa fa-play"></i> Execute
                                    </button>
                                    <button type="button" class="btn btn-default" id="clear-network-output">
                                        <i class="fa fa-eraser"></i> Clear
                                    </button>
                                </div>
                                
                                <!-- Terminal Output -->
                                <div class="terminal-output" id="terminal-output">
<span style="color: #888;">Network Tools</span>
<span style="color: #888;">=========================================================</span>

Pilih tool dan klik Execute untuk memulai...
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="profile" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Ubah Profil</h5>
            </div>
            <div class="modal-body">
                <?php echo form_open_multipart('superadmin/edit_profile'); ?>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" value="<?php echo $user['email']; ?>" readonly>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Nama</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="nama" value="<?php echo $user['nama']; ?>">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-2 font-weight-bolder">Photo</div>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-3">
                                <img src="<?php echo base_url('assets/dist/img/profile/') . $user['image']; ?>" class="img-thumbnail">
                            </div>
                            <div class="col-sm-9">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="image">
                                    <label class="custom-file-label" for="image">Pilih File</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan </button>
                <button class="btn btn-secondary" type="button" data-dismiss="modal">Tutup</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ubah-pass">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Ubah Password</h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <form action="<?php echo base_url('superadmin/ubah_password'); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <div class="form-group">
                            <label for="current_password">Password Lama</label>
                            <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password1">Password Baru</label>
                            <input type="password" class="form-control" id="new_password1" name="new_password1" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password2">Ulang Password Baru</label>
                            <input type="password" class="form-control" id="new_password2" name="new_password2" placeholder="Ketik ulang password baru" required>
                        </div>
                        <button type="submit" class="btn btn-primary mr-2">Simpan Perubahan </button>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Network Tools Script (Restored) -->
<script>
(function checkJQuery() {
    if (typeof jQuery === 'undefined') {
        setTimeout(checkJQuery, 50);
        return;
    }

    $(document).ready(function() {
        // Tool selection
        $('.tool-tab').click(function() {
            $('.tool-tab').removeClass('active');
            $(this).addClass('active');
            var tool = $(this).data('tool');
            
            // Show/hide specific inputs
            if (tool === 'ipconfig' || tool === 'netstat') {
                $('#tool-input-group').hide();
            } else {
                $('#tool-input-group').show();
                if (tool === 'ping') {
                    $('#ping-count').show();
                } else {
                    $('#ping-count').hide();
                }
            }
            
            // Update description
            var desc = '';
            switch(tool) {
                case 'ping': desc = '<strong>Ping:</strong> Test konektivitas jaringan ke host tujuan dengan mengirimkan paket ICMP.'; break;
                case 'netstat': desc = '<strong>Netstat:</strong> Menampilkan statistik koneksi jaringan, tabel routing, dan statistik interface.'; break;
                case 'traceroute': desc = '<strong>Traceroute:</strong> Melacak jalur paket data ke host tujuan.'; break;
                case 'nslookup': desc = '<strong>NSLookup:</strong> Mencari informasi DNS record dari domain atau IP.'; break;
                case 'ipconfig': desc = '<strong>IPConfig:</strong> Menampilkan konfigurasi IP dari semua interface jaringan server.'; break;
            }
            $('#tool-description').html(desc);
        });
        
        // Clear terminal
        $('#clear-network-output').click(function() {
            $('#terminal-output').html('<span style="color: #888;">Network Tools</span>\n<span style="color: #888;">=========================================================</span>\n\nPilih tool dan klik Execute untuk memulai...');
        });
        
        // Execute tool
        $('#execute-network-tool').click(function() {
            var tool = $('.tool-tab.active').data('tool');
            var host = $('#network-host').val();
            var count = $('#ping-count').val();
            
            if (tool !== 'ipconfig' && tool !== 'netstat' && !host) {
                Swal.fire('Warning', 'Masukkan hostname atau IP address', 'warning');
                return;
            }
            
            var $btn = $(this);
            var originalBtn = $btn.html();
            $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Executing...');
            
            var cmdLine = tool;
            if (tool === 'ping') cmdLine += ' -n ' + count + ' ' + host;
            else if (tool !== 'ipconfig' && tool !== 'netstat') cmdLine += ' ' + host;
            
            $('#terminal-output').append('\n<div class="command-line">root@server:~# ' + cmdLine + '</div>');
            $('#terminal-output').append('<div class="loading">Processing... please wait...</div>');
            
            // Scroll to bottom
            var terminal = document.getElementById('terminal-output');
            terminal.scrollTop = terminal.scrollHeight;
            
            var data = {
                    host: host,
                    count: count
                };
            data[csrfName] = csrfHash;

            $.ajax({
                url: '<?php echo base_url("superadmin/network_"); ?>' + tool,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    updateCsrf(response.csrfHash);
                    $('.loading').remove();
                    if (response.status) {
                        $('#terminal-output').append('<div>' + response.output + '</div>');
                    } else {
                        $('#terminal-output').append('<div class="error">' + response.message + '</div>');
                    }
                },
                error: function() {
                    $('.loading').remove();
                    $('#terminal-output').append('<div class="error">Server Error: Failed to execute command.</div>');
                },
                complete: function() {
                    $btn.prop('disabled', false).html(originalBtn);
                    terminal.scrollTop = terminal.scrollHeight;
                }
            });
        });
        
        // Public IP
        window.fetchPublicIP = function() {
            console.log('Fetching Public IP (Client-Side)...');
            $('#public-ip-value').html('<i class="fa fa-spinner fa-spin"></i> Loading...');
            
            // Use client-side fetch to respect Browser VPNs
            // ip-api.com allows CORS for free endpoint (HTTP only mostly, but works for localhost)
            // For HTTPS support, we might need a different provider or proxy, but ip-api is what backend used.
            $.ajax({
                url: 'http://ip-api.com/json',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#public-ip-value').text(response.query); // ip-api uses 'query' for IP
                        
                        var locationText = '';
                        if (response.city && response.country) {
                            locationText = response.city + ', ' + response.country;
                        } else if (response.country) {
                            locationText = response.country;
                        } else {
                            locationText = 'Unknown';
                        }
                        $('#public-ip-location').html('<i class="fa fa-map-marker"></i> ' + locationText);
                        
                        if (response.isp) {
                            $('#public-ip-isp').html('<i class="fa fa-building"></i> ' + response.isp);
                        }
                    } else {
                        // Fallback or Error
                        $('#public-ip-value').html('<i class="fa fa-times-circle"></i> N/A');
                        $('#public-ip-location').html('<i class="fa fa-times-circle"></i> N/A');
                    }
                },
                error: function() {
                    $('#refresh-ip-btn i').removeClass('fa-spin');
                    $('#public-ip-value').html('<i class="fa fa-times-circle"></i> Connection Error');
                    $('#public-ip-location').html('<i class="fa fa-times-circle"></i> Check HTTPS/Adblock');
                }
            });
        };
        
        // Fetch on page load
        fetchPublicIP();
        
        // Auto-refresh every 30 seconds
        window.ipInterval = setInterval(fetchPublicIP, 30000);
        
        // Manual refresh button
        $(document).on('click', '#refresh-ip-btn', function() {
            $('#refresh-ip-btn i').addClass('fa-spin');
            fetchPublicIP();
        });

        // ==========================================
        // Real-time Server Time
        // ==========================================
        if (typeof serverTimeData !== 'undefined') {
            // Calculate difference between server time and client time
            // serverTimeData.initialTime is a Date object created from server's ISO string
            var clientTime = new Date();
            var timeDiff = serverTimeData.initialTime.getTime() - clientTime.getTime();

            function updateServerTime() {
                var now = new Date();
                var currentServerTime = new Date(now.getTime() + timeDiff);
                
                // Format time: HH:mm:ss
                var optionsTime = { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit', 
                    hour12: false, 
                    timeZone: serverTimeData.timezone 
                };
                var timeString = currentServerTime.toLocaleTimeString('en-GB', optionsTime);
                
                // Format date: Day, DD Month YYYY (Matches PHP: l, d F Y)
                // Note: 'en-GB' usually gives dd/mm/yyyy, so we use 'ent-US' with options or custom format
                // PHP: l => 'Sunday', d => '01', F => 'January', Y => '2025'
                var optionsDate = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric', 
                    timeZone: serverTimeData.timezone 
                };
                var dateString = currentServerTime.toLocaleDateString('en-US', optionsDate); 
                // dateString will be like "Sunday, January 1, 2025" or "Sunday, 1 January 2025" depending on locale
                // Let's try to match PHP 'l, d F Y' => "Sunday, 01 January 2025"
                // JS 'en-GB' => "Sunday, 1 January 2025". PHP 'd' is 2-digit. JS 'day: 2-digit' works.
                
                var optionsDate2 = {
                    weekday: 'long',
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    timeZone: serverTimeData.timezone
                };
                // en-GB puts day before month
                var dateStringGB = currentServerTime.toLocaleDateString('en-GB', optionsDate2);
                
                $('#current-time').text(timeString);
                $('#current-date').text(dateStringGB);
            }

            // Update every second
            setInterval(updateServerTime, 1000);
            updateServerTime(); // Initial call
        }
    });
})();
</script>
