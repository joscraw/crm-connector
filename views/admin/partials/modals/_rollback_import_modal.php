<div class="modal fade js-rollback-modal" id="rollbackModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header js-modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Rollback Tool</h5>
                <div id="spinner" class="js-spinner"></div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body js-modal-body">
                <table class="table">
                    <tr>
                        <td>Number Of Students</td>
                        <td>Rollback Type</td>
                        <td>Creation Date</td>
                        <td>Rollback</td>
                    </tr>
                    <?php
                    global $wpdb;
                    $results = $wpdb->get_results(sprintf("SELECT * FROM %s%s ORDER BY created_at DESC", $wpdb->prefix, "imports"));
                    foreach ($results as $instance) :
                        $object_ids = unserialize($instance->algolia_object_ids);
                        ?>

                        <tr>
                            <td><?php echo count($object_ids);?></td>
                            <td><?php echo $instance->is_import ? 'From Import' : 'From Export'; ?></td>
                            <td><?php echo $instance->created_at;?></td>
                            <td>
                                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                                    <input type="hidden" name="action" value="crmc_rollback_import">
                                    <input type="hidden" name="importId" value="<?php echo $instance->id;?>">
                                    <input type="hidden" name="crmc_rollback_import_nonce" value="<?php echo wp_create_nonce( 'crmc_rollback_import_nonce' ); ?>" />
                                    <button data-chapter="<?php echo $instance->id;?>" type="submit" class="btn btn-danger js-rollback-button" data-toggle="modal">Rollback</button>
                                </form>
                            </td>
                        </tr>

                        <?php
                    endforeach;
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>