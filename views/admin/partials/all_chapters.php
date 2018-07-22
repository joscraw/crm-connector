<!-- Modals -->
<?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/modals/_import_modal.php'); ?>
<?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/modals/_rollback_import_modal.php'); ?>


<?php
ob_start();
?>
<table class="table table-hover">
    <tr>
        <th>Chapter</th>
        <th>Import Students From Excel Spreadsheet</th>
        <th>Rollback to a Previous Import</th>
    </tr>

    <?php
    global $wpdb;
    $results = $wpdb->get_results(sprintf("SELECT * FROM %s%s", $wpdb->prefix, "chapters"));
    foreach ($results as $instance) :
        ?>

        <tr>
            <td><?php echo $instance->chapter_name; ?></td>
            <td><button data-chapter="<?php echo $instance->id;?>" type="button" class="btn btn-primary js-show-import-modal-button" data-toggle="modal">Import Students</button></td>
            <td><button data-chapter="<?php echo $instance->id;?>" type="button" class="btn btn-warning js-show-rollback-modal-button" data-toggle="modal">View Rollback Options</button></td>
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

<script>

    (function($){

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
                        mapping.unshift('<option value="" disabled selected>Select your option</option>');
                        prototype = prototype.replace(/\{\{database_column_names\}\}/g, mapping.join(''));

                        $form.append(prototype);

                    });

                    debugger;
                    this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

                }.bind(this));

        }.bind(this));

        $(document).on('click', '.js-import-students-button', function(event) {

            var selected_database_columns = [];
            $selects = $('.js-database-column-name');
            $selects.each(function(index, select){
                $select = $(select);
                if($select.val())
                {
                    selected_database_columns.push(index);
                }
            });

            debugger;
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
            formData.append('selected_database_columns', JSON.stringify(selected_database_columns));

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
                    this.ajaxAttachLoadingDots(".js-import-students-button");
                }.bind(this)
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {

                    $('.js-import-modal').animate({
                        scrollTop: $('.js-import-modal').offset().top
                    }, 200);

                    var response = JSON.parse(r);
                    this.ajaxRemoveLoadingSpinnerFromContainer(".js-import-modal .js-modal-body");
                    this.ajaxRemoveLoadingDots(".js-import-students-button");
                    this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

                }.bind(this));

        }.bind(this));

        $(document).on('click', '.js-show-rollback-modal-button', function(event) {
            var $modal = $('.js-rollback-modal');
            $modal.modal('show');
            this.chapterId = $(event.target).data('chapter');
            debugger;

        }.bind(this));

    })(jQuery);


</script>