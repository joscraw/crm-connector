<div class="row">

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left">
            <li class="active"><a href="#create_chapter" data-toggle="pill">Create Chapter</a></li>
            <li><a href="#all_chapters" data-toggle="pill">All Chapters</a></li>
            <li><a href="#chapter_mapping" data-toggle="pill">Chapter Mapping</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane active" id="create_chapter">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/create_chapter.php'); ?>
            </div>

            <div class="tab-pane" id="all_chapters">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/all_chapters.php'); ?>
            </div>

            <div class="tab-pane" id="chapter_mapping">
                <?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/chapter_mapping.php'); ?>
            </div>
        </div>
    </div>
</div>