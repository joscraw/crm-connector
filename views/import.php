<div class="container">

    <?php
    include('partials/nav.php');
    ?>

    <div class="row">
        <?php
        echo renderSuccessMessage(get_transient('successMessage'));
        echo renderGenericErrorMessage(get_transient('errors')['main']);
        ?>
        <div class="col-md-12">

            <?php
            // Generate a custom nonce value.
            $crmc_import_contacts_nonce = wp_create_nonce( 'crmc_import_contacts_nonce' );

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
                <input type="hidden" name="crmc_import_contacts_nonce" value="<?php echo $crmc_import_contacts_nonce ?>" />
                <input type="hidden" class="js-chapter-id" name="chapter_id" value="<?php echo isset($_GET['chapter_id']) ? $_GET['chapter_id'] : '';?>">
                <div class="form-group">
                    <label for="studentExcelFile">Student Excel File</label>
                    <?php
                    echo renderErrors(get_transient('errors')['studentFile']);
                    ?>
                    <input data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_import_file_nonce' ); ?>" type="file" name="studentFile" class="js-student-file">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>

        </div>
    </div>
</div>