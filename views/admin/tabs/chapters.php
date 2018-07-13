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
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="crmc_add_chapter">
                    <input type="hidden" name="crmc_add_chapter_nonce" value="<?php echo wp_create_nonce( 'crmc_add_chapter_nonce' ); ?>" />
                    <div class="form-group">

                        <label for="hubspotAPIKey">Chapter Name</label>

                        <?php
                        $errors = get_transient('errors');
                        echo renderErrors($errors['crmc_chapter_name']);
                        ?>

                        <input type="text" value="<?php echo get_option('crmc_chapter_name'); ?>" class="form-control" id="chapter" name="crmc_chapter_name" placeholder="Chapter Name">
                        <p class="help-block">Name of the school you would like to create</p>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>

            <div class="tab-pane" id="all_chapters">
                <!-- Modal -->
                <div class="modal fade js-import-modal" id="importModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header js-modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Student Import Tool</h5>
                                <div id="spinner" class="js-spinner"></div>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body js-modal-body">
                                <?php
                                ob_start();
                                ?>
                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <div class="form-group js-import-mapping">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="fileColumnName">File Column Name</label>
                                                    <input type="text" id="fileColumnName" class="form-control js-file-column-name" value="{{file_column_name}}" name="file_column_names[]" placeholder="File Column Name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="databaseColumnName">Database Column Name</label>
                                                    <select id="databaseColumnName" class="form-control js-database-column-name" name="database_column_name[]">{{database_column_names}}</select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $prototype_import_mapping_form = ob_get_clean();
                                ?>

                                <form data-prototype-import-mapping-form="<?php echo htmlspecialchars($prototype_import_mapping_form);?>" class="js-import-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="action" value="crmc_import_contacts">
                                    <input type="hidden" name="crmc_import_contacts_nonce" value="<?php echo wp_create_nonce( 'crmc_import_contacts_nonce' ); ?>" />
                                    <input type="hidden" class="js-chapter-id" name="chapter_id" value="<?php echo isset($_GET['chapter_id']) ? $_GET['chapter_id'] : '';?>">
                                    <div class="form-group">
                                        <label for="studentExcelFile">Student Excel File</label>

                                        <?php
                                        echo renderErrors(get_transient('errors')['studentFile']);
                                        ?>

                                        <input data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_get_column_names_nonce' ); ?>" type="file" name="studentFile" class="js-student-file">
                                    </div>
                                    <div class="js-column-name-container"></div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button type="button" class="btn btn-primary js-import-students-button" data-nonce="<?php echo wp_create_nonce( 'crmc_import_file_nonce' ); ?>">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                ob_start();
                ?>
                <table class="table table-hover">
                    <tr>
                        <th>Chapter</th>
                    </tr>

                    <?php
                    global $wpdb;
                    $results = $wpdb->get_results(sprintf("SELECT * FROM %s%s", $wpdb->prefix, "chapters"));
                    foreach ($results as $instance) :
                    ?>

                    <tr>
                        <td><?php echo $instance->chapter_name; ?> <button data-chapter="<?php echo $instance->id;?>" type="button" class="btn btn-primary js-show-import-modal-button" data-toggle="modal">Import Students</button></td>
                    </tr>

                    <?php
                    endforeach;
                    $content = ob_get_clean();
                    if(empty($results)) :
                        echo renderGenericErrorMessage(["Zero results... Don't worry! You just haven't created any chapters yet. :)"], true);
                    else:
                        echo $content;
                    endif;
                    ?>

                </table>
            </div>

            <div class="tab-pane" id="chapter_mapping">

                <?php
                ob_start();
                ?>
                <div class="form-group js-group">
                    <label for="group">Group</label>
                    <input type="text" class="form-control" name="groups[group_index][group]" placeholder="Group Name">
                    <button class="btn btn-default js-add-property" data-group="{{group_id}}" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_property_nonce' ); ?>" type="button">Add Property</button>
                </div>

                <?php
                $prototype_group_form = ob_get_clean();
                ob_start();
                ?>

                <div class="row">
                    <div class="col-md-10 col-md-offset-2">
                        <div class="form-group js-property" data-property="{{property_id}}">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="group">Property Name</label>
                                    <input type="text" class="form-control js-property-name" name="groups[group_index][properties][property_index][property_name]" placeholder="Property Name">
                                </div>
                                <div class="col-md-6">
                                    <label for="group">Property Value</label>
                                    <input type="text" class="form-control js-property-value" name="groups[group_index][properties][property_index][property_value]" placeholder="Property Value">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $prototype_property_form = ob_get_clean();
                ?>

                <div class="row">
                    <div class="col-md-10">
                        <button data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_group_nonce' ); ?>" class="btn btn-success js-add-property-group" type="button">Add Property Group</button>
                        <button class="btn btn-primary js-sync-mapping-button" type="button">Sync Mapping To CRM</button>
                    </div>
                    <div class="col-md-2">
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-10 js-form-container" data-prototype-group-form="<?php echo htmlspecialchars($prototype_group_form);?>" data-prototype-property-form="<?php echo htmlspecialchars($prototype_property_form);?>">

                        <form class="js-chapter-mapping-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                            <input type="hidden" name="action" value="crmc_sync_mapping_to_hubspot">
                            <input type="hidden" name="crmc_sync_mapping_to_hubspot_nonce" value="<?php echo wp_create_nonce( 'crmc_sync_mapping_to_hubspot_nonce' ); ?>" />

                            <?php
                            global $wpdb;
                            $table_name = $wpdb->prefix."groups";
                            // this will get the data from your table
                            $groups = $wpdb->get_results("SELECT * FROM $table_name");
                            $i = 0;
                            foreach ($groups as $group) :
                            ?>

                            <div class="form-group js-group">
                                <label for="group">Group</label>
                                <input type="text" class="form-control" value="<?php echo $group->group_name;?>" name="groups[<?php echo $i; ?>][group]" placeholder="Group Name">
                                <button class="btn btn-default js-add-property" data-group="<?php echo $group->id;?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_property_nonce' ); ?>" type="button">Add Property</button>

                                <?php
                                $table_name = $wpdb->prefix."properties";
                                // this will get the data from your table
                                $properties = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE group_id = %d", $group->id));
                                $j = 0;
                                foreach ($properties as $property) :
                                ?>

                                <div class="row">
                                    <div class="col-md-10 col-md-offset-2">
                                        <div class="form-group js-property" data-property="<?php echo $property->id; ?>">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label for="group">Property Name</label>
                                                    <input type="text" class="form-control js-property-name" value="<?php echo $property->property_name; ?>" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_name]" placeholder="Property Name">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="group">Property Value</label>
                                                    <input type="text" class="form-control js-property-value" value="<?php echo $property->property_value; ?>" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_value]" placeholder="Property Value">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $j++;
                                endforeach;
                                ?>

                            </div>

                        <?php
                        $i++;
                        endforeach;
                        ?>
                        </form>


                    </div>
                </div>

            </div>

        </div>
    </div>
</div>


<script>

    (function($){

        $(document).on('click', '.js-add-property-group', function() {

            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                container;

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_create_group", nonce: nonce}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    var prototype =  (container = $('.js-form-container')).data('prototype-group-form');
                    var groupIndex = $('.js-group').length;
                    prototype = prototype.replace(/group_index/g, groupIndex);
                    prototype = prototype.replace(/\{\{group_id\}\}/g, r.group_id);
                    container.append(prototype);
                });

        });

        $(document).on('click', '.js-add-property', function() {

            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                group = $(this).attr("data-group"),
                container,
                $div;

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_create_property", nonce: nonce, group: group}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(event, r,status,jqXHR) {

                    var prototype = (container = $('.js-form-container')).data('prototype-property-form');
                    $div = $(event.target).closest('.js-group');
                    var groupIndex = $('.js-group').index($div);
                    var propertyIndex = $div.find('.js-property').length;
                    prototype = prototype.replace(/group_index/g, groupIndex);
                    prototype = prototype.replace(/property_index/g, propertyIndex);
                    prototype = prototype.replace(/\{\{property_id\}\}/g, r.property_id);
                    $div.append(prototype);
                }.bind(this, event));

        });


        $(document).on('change', '.js-group > input[type="text"]', function() {

            // the add property button inside each group has all the data attributes you need to modify the group name

            var $button = $(event.target).parent('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                group_name = $(event.target).val();

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_group_name", nonce: nonce, group: group, group_name: group_name}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });


        $(document).on('change', '.js-property-name', function() {

            // the add property button inside each group has all the data attributes you need to modify the group name
            var $button = $(event.target).closest('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                property_name = $(event.target).val(),
                property_id = $(event.target).closest('.js-property').data('property');


            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_property_name", nonce: nonce, group: group, property_name: property_name, property_id: property_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });

        $(document).on('change', '.js-property-value', function() {

            // the add property button inside each group has all the data attributes you need to modify the group name
            var $button = $(event.target).closest('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                property_value = $(event.target).val(),
                property_id = $(event.target).closest('.js-property').data('property');

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_property_value", nonce: nonce, group: group, property_value: property_value, property_id: property_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });

        $(document).on('click', '.js-show-import-modal-button', function(event) {
            var $modal = $('.js-import-modal');
            $modal.modal('show');
            this.chapterId = $(event.target).data('chapter');

        }.bind(this));

        $(document).on('change', '.js-student-file', function(event) {

            debugger;
            var $form = $('.js-import-form'),
                e = event.target;

            event.preventDefault();

            var file = e.files[0];

            // Create a new FormData object.
            var formData = new FormData();

            var nonce = $(e).attr("data-nonce"),
                url = $(e).attr("data-url");

            // Add the file to the request.
            formData.append('studentFile', file);
            formData.append('nonce', nonce);
            formData.append('action', 'crmc_get_column_names');
            formData.append('chapter_id', this.chapterId);

            // perform ajax request
            jQuery.ajax({
                url : url,
                data : formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST', // For jQuery < 1.9
                beforeSend: function() {
                    debugger;
                    this.ajaxAttachLoadingSpinnerToContainer(".js-import-modal .js-modal-body");
                }.bind(this)
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }.bind(this))
                .done(function(r,status,jqXHR) {

                    debugger;
                    this.ajaxRemoveLoadingSpinnerFromContainer(".js-import-modal .js-modal-body");
                    $form.find('.js-column-name-container').html();
                    var response = JSON.parse(r);
                    $(response['columns']).each(function(index, column) {
                        var prototype =  $form.data('prototype-import-mapping-form');
                        prototype = prototype.replace(/column_index/g, index);
                        prototype = prototype.replace(/\{\{file_column_name\}\}/g, column);

                        var mapping = response['student_import_file_mapping'];
                        mapping = mapping.map(function(map){
                            return "<option value='"+map+"'>"+map+"</option>";
                        });

                        prototype = prototype.replace(/\{\{database_column_names\}\}/g, mapping.join(''));

                        $form.append(prototype);

                    });

                    debugger;
                    this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

                }.bind(this));

        }.bind(this));

        $(document).on('click', '.js-import-students-button', function(event) {

            event.preventDefault();

            debugger;
            var $form = $('.js-import-form'),
                file = $form.find('.js-student-file').get(0).files[0],
                nonce = $(event.target).attr("data-nonce"),
                url = $form.find('.js-student-file').attr("data-url");
                /*formData = new FormData();*/

            var formData = new FormData($form.get(0));
            formData.append('nonce', nonce);
            formData.append('action', 'crmc_import_contacts');
            formData.append('chapter_id', this.chapterId);

            // perform ajax request
            jQuery.ajax({
                url : url,
                data : formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST', // For jQuery < 1.9
                beforeSend: function() {
                    debugger;
                    this.ajaxAttachLoadingSpinnerToContainer(".js-import-modal .js-modal-body");
                }.bind(this)
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {

                    debugger;

                    var response = JSON.parse(r);

                    this.ajaxRemoveLoadingSpinnerFromContainer(".js-import-modal .js-modal-body");

                    this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

                }.bind(this));

        }.bind(this));

        $(document).on('click', '.js-sync-mapping-button', function(event) {
            debugger;
            event.preventDefault();

            var $form = $('.js-chapter-mapping-form');
            $form.submit();
        }.bind(this));

    })(jQuery);



</script>



