<div class="row">

    <?php
    $errors = get_transient('errors');
    ?>

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left">
            <li class="active"><a href="#create_list" data-toggle="pill">Create List</a></li>
            <li><a href="#lists" data-toggle="pill">Lists</a></li>
            <li style="display:none"><a href="#edit_list" data-toggle="pill">Edit List</a></li>
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

            <div class="tab-pane" id="templates">
                templates
            </div>

        </div>
    </div>
</div>
