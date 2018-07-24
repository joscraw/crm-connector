<div class="row">

    <?php
    $errors = get_transient('errors');
    ?>

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left js-left-nav">
            <li class="active"><a href="#create_list" data-toggle="pill">Create List</a></li>
            <li><a href="#lists" data-toggle="pill">Lists</a></li>
            <li style="display:none"><a href="#edit_list" data-toggle="pill">Edit List</a></li>
            <li><a href="#create_template" data-toggle="pill">Create Template</a></li>
            <li style="display:none"><a href="#edit_template" data-toggle="pill">Edit Template</a></li>
            <li><a href="#templates" data-toggle="pill">Templates</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane active" id="create_list">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/create_list.php'); ?>
            </div>

            <div class="tab-pane" id="lists">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/lists.php'); ?>
            </div>

            <div class="tab-pane" id="edit_list">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/edit_list.php'); ?>
            </div>

            <div class="tab-pane" id="create_template">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/create_template.php'); ?>
            </div>

            <div class="tab-pane js-edit-template-container" id="edit_template">
                <!-- dynamically filled content with ajax -->
            </div>

            <div class="tab-pane" id="templates">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/templates.php'); ?>
            </div>

        </div>
    </div>
</div>
