<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Edit Column: <?= $field_name; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('admin/index'); ?>">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager'); ?>">Database Manager</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('DatabaseManager/detail/' . $table_name); ?>">Table Detail</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Edit Column</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox ">
                <div class="ibox-title">
                    <h5>Edit Column Configuration</h5>
                </div>
                <div class="ibox-content">
                    <?= $this->session->flashdata('message'); ?>
                    <form action="<?= base_url('DatabaseManager/update_column/' . $table_name . '/' . $field_name); ?>" method="post">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Length/Values</th>
                                    <th>Default</th>
                                    <th class="text-center">Null</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $type = isset($current_field->type) ? strtoupper($current_field->type) : 'VARCHAR';
                                    $max_length = isset($current_field->max_length) ? $current_field->max_length : '';
                                    if($type == 'INT' && empty($max_length)) $max_length = 11; // Default assumption if not returned
                                ?>
                                <tr>
                                    <td><input type="text" name="column[name]" class="form-control" value="<?= $field_name; ?>" required></td>
                                    <td>
                                        <select name="column[type]" class="form-control" id="col_type">
                                            <option value="INT" <?= $type == 'INT' ? 'selected' : ''; ?>>INT</option>
                                            <option value="VARCHAR" <?= $type == 'VARCHAR' ? 'selected' : ''; ?>>VARCHAR</option>
                                            <option value="TEXT" <?= $type == 'TEXT' ? 'selected' : ''; ?>>TEXT</option>
                                            <option value="DATE" <?= $type == 'DATE' ? 'selected' : ''; ?>>DATE</option>
                                            <option value="DATETIME" <?= $type == 'DATETIME' ? 'selected' : ''; ?>>DATETIME</option>
                                            <option value="TIMESTAMP" <?= $type == 'TIMESTAMP' ? 'selected' : ''; ?>>TIMESTAMP</option>
                                            <option value="FLOAT" <?= $type == 'FLOAT' ? 'selected' : ''; ?>>FLOAT</option>
                                            <option value="DOUBLE" <?= $type == 'DOUBLE' ? 'selected' : ''; ?>>DOUBLE</option>
                                            <option value="DECIMAL" <?= $type == 'DECIMAL' ? 'selected' : ''; ?>>DECIMAL</option>
                                            <option value="ENUM" <?= $type == 'ENUM' ? 'selected' : ''; ?>>ENUM</option>
                                        </select>
                                    </td>
                                    <td><input type="text" name="column[length]" class="form-control" id="col_length" value="<?= $max_length; ?>"></td>
                                    <td><input type="text" name="column[default]" class="form-control" value="<?= isset($current_field->default) ? $current_field->default : ''; ?>"></td>
                                    <td class="text-center"><input type="checkbox" name="column[null]" value="1" <?= (isset($current_field->primary_key) && $current_field->primary_key) ? 'disabled' : ''; ?>></td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <div class="hr-line-dashed"></div>
                        <div class="form-group row">
                            <div class="col-sm-4 col-sm-offset-2">
                                <a href="<?= base_url('DatabaseManager/detail/' . $table_name); ?>" class="btn btn-white btn-sm">Cancel</a>
                                <button class="btn btn-primary btn-sm" type="submit">Update Column</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
