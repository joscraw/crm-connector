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
                                    <input type="text" id="fileColumnName" class="form-control js-file-column-name" value="{{file_column_name}}" name="file_column_names[]" placeholder="File Column Name" disabled>
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