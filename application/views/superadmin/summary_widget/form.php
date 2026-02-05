<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2><?= $title ?></h2>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-7">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Configuration</h5>
                </div>
                <div class="ibox-content">
                    <form id="widgetForm" action="<?= site_url('summary_widget/save') ?>" method="post">
                        <input type="hidden" name="id" value="<?= isset($widget) ? $widget->id : '' ?>">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" id="csrf_token_input" value="<?= $this->security->get_csrf_hash(); ?>">
                        
                        <div class="form-group">
                            <label>Widget Title</label>
                            <input type="text" name="title" class="form-control" required value="<?= isset($widget) ? $widget->title : '' ?>" id="inputTitle">
                        </div>
                        
                        <div class="form-group">
                            <label>Select Table</label>
                            <select name="table_name" class="form-control" id="tableSelect" required>
                                <option value="">-- Select Table --</option>
                                <?php foreach($tables as $t): ?>
                                    <option value="<?= $t ?>" <?= (isset($widget) && $widget->table_name == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Select Column</label>
                            <select name="column_name" class="form-control" id="columnSelect" required>
                                <option value="">-- Select Column --</option>
                                <?php if(isset($widget)): ?>
                                    <option value="<?= $widget->column_name ?>" selected><?= $widget->column_name ?></option>
                                <?php endif; ?>
                            </select>
                            <span class="help-block m-b-none">Select '*' for COUNT aggregate if not counting specific column.</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Aggregate Function</label>
                            <select name="aggregate_func" class="form-control" id="aggSelect" required>
                                <?php $aggs = ['SUM', 'AVG', 'MIN', 'MAX', 'COUNT']; ?>
                                <?php foreach($aggs as $a): ?>
                                    <option value="<?= $a ?>" <?= (isset($widget) && $widget->aggregate_func == $a) ? 'selected' : '' ?>><?= $a ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Formatting</label>
                            <select name="formatting" class="form-control" id="formatSelect">
                                <option value="number" <?= (isset($widget) && $widget->formatting == 'number') ? 'selected' : '' ?>>Number/Text (Default)</option>
                                <option value="rupiah" <?= (isset($widget) && $widget->formatting == 'rupiah') ? 'selected' : '' ?>>Rupiah (Rp)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Placement</label>
                            <select name="placement" class="form-control" id="placementSelect">
                                <option value="dashboard" <?= (isset($widget) && $widget->placement == 'dashboard') ? 'selected' : '' ?>>Dashboard Main</option>
                                <option value="report_page" <?= (isset($widget) && $widget->placement == 'report_page') ? 'selected' : '' ?>>Report Page</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Background Color</label>
                            <div class="row">
                                <?php $colors = ['navy-bg', 'lazur-bg', 'yellow-bg', 'red-bg', 'blue-bg']; ?>
                                <?php foreach($colors as $c): ?>
                                <div class="col-md-2">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="bg_color_class" value="<?= $c ?>" id="bg_<?= $c ?>" <?= (isset($widget) && $widget->bg_color_class == $c) ? 'checked' : ($c=='navy-bg'?'checked':'') ?>>
                                            <span class="badge <?= $c ?> p-2"><?= $c ?></span>
                                        </label>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Visible to Roles</label>
                            <select name="allowed_roles[]" class="form-control" multiple required style="height: 100px">
                                <?php 
                                $selected_roles = isset($widget) ? json_decode($widget->allowed_roles) : [];
                                if(!is_array($selected_roles)) $selected_roles = [];
                                foreach($roles as $r): 
                                ?>
                                    <option value="<?= $r->id_role ?>" <?= in_array($r->id_role, $selected_roles) ? 'selected' : '' ?>>
                                        <?= $r->role_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="is_active" value="1" <?= (!isset($widget) || $widget->is_active) ? 'checked' : '' ?>> Active
                            </label>
                        </div>
                        
                        <hr>
                        <button type="submit" class="btn btn-primary">Save Widget</button>
                        <a href="<?= site_url('summary_widget') ?>" class="btn btn-white">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-5">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>Live Preview</h5>
                    <button type="button" class="btn btn-xs btn-info pull-right" id="btnPreview"><i class="fa fa-refresh"></i> Refresh</button>
                </div>
                <div class="ibox-content">
                    <div id="previewContainer">
                        <!-- Preview Markup -->
                        <div class="widget style1 navy-bg" id="previewWidget">
                            <div class="row">
                                <div class="col-4">
                                    <i class="fa fa-cloud fa-5x"></i>
                                </div>
                                <div class="col-8 text-right">
                                    <span id="previewTitle"> Title </span>
                                    <h2 class="font-bold" id="previewValue">0</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info m-t-md">
                        This is how the widget will appear on the dashboard.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


