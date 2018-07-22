<div class="row">

    <?php
    $errors = get_transient('errors');
    ?>

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left">
            <li class="active"><a href="#instant_search" data-toggle="pill">Instant Search</a></li>
            <li><a href="#advanced_search" data-toggle="pill">Advanced Search</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane active" id="instant_search">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/instant_search.php'); ?>
            </div>
            <div class="tab-pane active" id="advanced_search">
                advanced
            </div>
        </div>
    </div>

</div>