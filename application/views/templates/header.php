<!DOCTYPE html>
<html>
<?php 
$identitas = $this->db->get('tbl_aplikasi')->row(); 
$skin_class = isset($identitas->skin_theme) && !empty($identitas->skin_theme) ? $identitas->skin_theme : 'md-skin';
?>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo $title; ?></title>

    <?php 
        $favicon = isset($identitas->title_icon) && !empty($identitas->title_icon) ? base_url('assets/images/' . $identitas->title_icon) : base_url('assets/images/default-icon.png');
    ?>
    <link rel="shortcut icon" href="<?php echo $favicon; ?>">

    <link href="<?php echo base_url('assets/'); ?>template/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/dataTables/datatables.min.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/animate.css" rel="stylesheet">
    <link href="<?php echo base_url('assets/'); ?>template/css/style.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <!-- Bootstrap Tour -->
    <link href="<?php echo base_url('assets/'); ?>template/css/plugins/bootstrapTour/bootstrap-tour.min.css" rel="stylesheet">
    
    <!-- Custom Tour Aesthetics -->
    <style>
        /* Modern Tour Popover Variables */
        :root {
            --tour-bg: rgba(255, 255, 255, 0.95);
            --tour-backdrop: rgba(0, 0, 0, 0.6);
            --tour-primary: #1ab394;
            --tour-text: #2f4050;
            --tour-shadow: 0 10px 30px rgba(0,0,0,0.15);
            --tour-radius: 12px;
        }

        /* Backdrop Animation */
        .tour-backdrop {
            background-color: var(--tour-backdrop);
            opacity: 0;
            animation: fadeIn 0.4s ease forwards;
        }

        /* Popover Styling */
        .popover.tour {
            background: var(--tour-bg);
            border: none;
            border-radius: var(--tour-radius);
            box-shadow: var(--tour-shadow);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            padding: 0;
            min-width: 320px;
            z-index: 11000 !important;
            opacity: 0;
            /* Default hidden state for animation */
        }
        
        /* State-based animations applied by JS or modifier classes */
        .popover.tour.fade.in {
            opacity: 1;
            animation: bounceIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        /* Header Styling */
        .popover.tour .popover-title {
            background: linear-gradient(135deg, var(--tour-primary), #0d8a70);
            color: white;
            border-radius: var(--tour-radius) var(--tour-radius) 0 0;
            border: none;
            padding: 15px 20px;
            font-weight: 600;
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        /* Content Styling */
        .popover.tour .popover-content {
            padding: 20px;
            color: var(--tour-text);
            font-size: 14px;
            line-height: 1.6;
        }

        /* Navigation Buttons */
        .popover.tour .popover-navigation {
            padding: 10px 20px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            overflow: hidden;
            border-radius: 0 0 var(--tour-radius) var(--tour-radius);
        }

        .popover.tour .btn {
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            padding: 6px 15px;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .popover.tour .btn-default {
            background: #f0f0f0;
            color: #666;
        }
        
        .popover.tour .btn-default:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }

        /* Specific Nav Buttons */
        .popover.tour button[data-role='prev'] {
            margin-right: 5px;
        }

        .popover.tour button[data-role='next'] {
            background: var(--tour-primary);
            color: white;
            box-shadow: 0 4px 10px rgba(26, 179, 148, 0.3);
        }

        .popover.tour button[data-role='next']:hover {
            box-shadow: 0 6px 15px rgba(26, 179, 148, 0.4);
            transform: translateY(-2px);
        }
        
        .popover.tour button[data-role='end'] {
            background: transparent;
            color: #999;
            border: 1px solid transparent;
        }
        
        .popover.tour button[data-role='end']:hover {
            color: #d9534f;
            background: rgba(217, 83, 79, 0.1);
            transform: none;
        }

        /* Arrow Customization */
        .popover.tour .arrow {
            /* Adjust arrow if needed, usually Bootstrap default is okay but color might need sync */
        }
        
        .popover.tour.right .arrow:after {
            border-right-color: var(--tour-bg);
        }
        .popover.tour.bottom .arrow:after {
            border-bottom-color: var(--tour-bg);
        }
        .popover.tour.left .arrow:after {
            border-left-color: var(--tour-bg);
        }
        .popover.tour.top .arrow:after {
            border-top-color: var(--tour-bg);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.8) translateY(20px);
            }
            60% {
                opacity: 1;
                transform: scale(1.05) translateY(-5px);
            }
            100% {
                transform: scale(1) translateY(0);
            }
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(26, 179, 148, 0.7); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(26, 179, 148, 0); }
            100% { transform: scale(0.8); box-shadow: 0 0 0 0 rgba(26, 179, 148, 0); }
        }

        /* Highlighting target */
        .tour-step-backdrop {
            position: relative;
            z-index: 11001; /* Above backdrop */
        }
        
        /* Optional: Add a pulsing ring to the target element if feasible with CSS only? 
           Bootstrap tour highlights by z-index. We can add a pseudo element via JS class if desired,
           but for now the z-index lift is standard. 
        */

    </style>

    <!-- Skin Switcher Styles -->
    <style>
        /* Skin Switcher Toggle Button */
        .skin-switcher-toggle {
            position: fixed;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            z-index: 9999;
            background: linear-gradient(135deg, #1ab394, #0d8a70);
            color: white;
            padding: 15px 12px;
            border-radius: 8px 0 0 8px;
            cursor: pointer;
            box-shadow: -3px 0 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
        .skin-switcher-toggle:hover {
            padding-right: 18px;
            background: linear-gradient(135deg, #0d8a70, #1ab394);
        }
        .skin-switcher-toggle i {
            font-size: 20px;
            animation: spin 3s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Skin Switcher Panel */
        .skin-switcher-panel {
            position: fixed;
            right: -320px;
            top: 0;
            width: 320px;
            height: 100%;
            background: #fff;
            z-index: 10000;
            box-shadow: -5px 0 30px rgba(0,0,0,0.2);
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }
        .skin-switcher-panel.open {
            right: 0;
        }
        
        /* Panel Header */
        .skin-panel-header {
            background: linear-gradient(135deg, #1ab394, #0d8a70);
            color: white;
            padding: 20px;
            position: relative;
        }
        .skin-panel-header h4 {
            margin: 0;
            font-weight: 600;
        }
        .skin-panel-header p {
            margin: 5px 0 0;
            opacity: 0.9;
            font-size: 13px;
        }
        .skin-panel-close {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .skin-panel-close:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-50%) rotate(90deg);
        }
        
        /* Skin Options */
        .skin-options {
            padding: 20px;
        }
        .skin-option {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid #e9ecef;
        }
        .skin-option:hover {
            border-color: #1ab394;
            transform: translateX(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .skin-option.active {
            border-color: #1ab394;
            background: linear-gradient(135deg, rgba(26,179,148,0.1), rgba(13,138,112,0.1));
        }
        .skin-option.active::after {
            content: '✓';
            position: absolute;
            right: 25px;
            color: #1ab394;
            font-size: 20px;
            font-weight: bold;
        }
        .skin-option {
            position: relative;
        }
        
        /* Skin Preview Colors */
        .skin-preview {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            margin-right: 15px;
            position: relative;
            overflow: hidden;
        }
        .skin-preview .nav-preview {
            height: 60%;
            width: 100%;
        }
        .skin-preview .content-preview {
            height: 40%;
            width: 100%;
            background: #f5f5f5;
        }
        
        /* Skin Specific Colors */
        .skin-1 .nav-preview { background: linear-gradient(180deg, #0d47a1, #1565c0); } /* Blue */
        .skin-2 .nav-preview { background: linear-gradient(180deg, #2F4050, #1ab394); } /* Half Navy with Teal accent */
        .skin-3 .nav-preview { background: linear-gradient(180deg, #db4c3c, #e74c3c); } /* Red/Orange */
        .skin-4 .nav-preview { background: linear-gradient(180deg, #1a242f, #2F4050); } /* Dark Navy */
        .md-skin-preview .nav-preview { background: linear-gradient(180deg, #2F4050, #3e5060); } /* Default */
        
        .skin-info h5 {
            margin: 0 0 5px;
            font-weight: 600;
            color: #2F4050;
        }
        .skin-info span {
            font-size: 12px;
            color: #999;
        }
        
        /* Overlay */
        .skin-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9998;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        .skin-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        /* Save Button */
        .skin-save-info {
            padding: 15px 20px;
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            text-align: center;
        }
        .skin-save-info small {
            color: #6c757d;
        }
    </style>

</head>

<body class="<?php echo $skin_class; ?>">

<!-- Skin Switcher Toggle Button - Only for Super Admin -->
<?php if ($this->session->userdata('level') == 'Super Admin'): ?>
<div class="skin-switcher-toggle" id="skinSwitcherToggle" title="Change Theme">
    <i class="fa fa-cog"></i>
</div>

<!-- Skin Overlay -->
<div class="skin-overlay" id="skinOverlay"></div>
<?php endif; ?>

<!-- Skin Switcher Panel - Only for Super Admin -->
<?php if ($this->session->userdata('level') == 'Super Admin'): ?>
<div class="skin-switcher-panel" id="skinSwitcherPanel">
    <div class="skin-panel-header">
        <h4><i class="fa fa-paint-brush"></i> Theme Switcher</h4>
        <p>Pilih tema yang kamu suka</p>
        <button class="skin-panel-close" id="skinPanelClose">
            <i class="fa fa-times"></i>
        </button>
    </div>
    
    <div class="skin-options">
        <!-- 1. Blue Theme -->
        <div class="skin-option <?php echo ($skin_class == 'skin-1') ? 'active' : ''; ?>" data-skin="skin-1">
            <div class="skin-preview skin-1">
                <div class="nav-preview"></div>
                <div class="content-preview"></div>
            </div>
            <div class="skin-info">
                <h5>Blue Theme</h5>
                <span>Clean & Professional</span>
            </div>
        </div>
        
        <!-- 2. Half Navy Theme -->
        <div class="skin-option <?php echo ($skin_class == 'skin-2') ? 'active' : ''; ?>" data-skin="skin-2">
            <div class="skin-preview skin-2">
                <div class="nav-preview"></div>
                <div class="content-preview"></div>
            </div>
            <div class="skin-info">
                <h5>Half Navy Theme</h5>
                <span>Navy & Teal Accent</span>
            </div>
        </div>
        
        <!-- 3. Orange Theme -->
        <div class="skin-option <?php echo ($skin_class == 'skin-3') ? 'active' : ''; ?>" data-skin="skin-3">
            <div class="skin-preview skin-3">
                <div class="nav-preview"></div>
                <div class="content-preview"></div>
            </div>
            <div class="skin-info">
                <h5>Orange Theme</h5>
                <span>Warm & Energetic</span>
            </div>
        </div>
        
        <!-- 4. Dark Navy Theme -->
        <div class="skin-option <?php echo ($skin_class == 'skin-4') ? 'active' : ''; ?>" data-skin="skin-4">
            <div class="skin-preview skin-4">
                <div class="nav-preview"></div>
                <div class="content-preview"></div>
            </div>
            <div class="skin-info">
                <h5>Dark Navy Theme</h5>
                <span>Deep & Elegant</span>
            </div>
        </div>
        
        <!-- 5. Default Theme (md-skin) -->
        <div class="skin-option <?php echo ($skin_class == 'md-skin') ? 'active' : ''; ?>" data-skin="md-skin">
            <div class="skin-preview md-skin-preview">
                <div class="nav-preview"></div>
                <div class="content-preview"></div>
            </div>
            <div class="skin-info">
                <h5>Default Theme</h5>
                <span>Classic & Minimalist</span>
            </div>
        </div>
    </div>
    
    <div class="skin-save-info">
        <small><i class="fa fa-info-circle"></i> Klik tema untuk mengubah secara permanen</small>
    </div>
</div>
<?php endif; ?>

<div id="wrapper">

