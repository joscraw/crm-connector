<div class="modal fade js-batch-subscribe-modal" id="batchSubscribeModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header js-modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Batch Subscribe Tool</h5>
                <div id="spinner" class="js-spinner"></div>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body js-modal-body">

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="active"><a href="#imports_list" data-toggle="pill">Imports</a></li>
                    <li><a href="#exports_list" data-toggle="pill">Custom Exports</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="imports_list">
                        <table class="table">
                            <tr>
                                <td>Number Of Students</td>
                                <td>Creation Date</td>
                                <td>Subscribe/Unsubscribe</td>
                                <td>Status</td>
                                <td>Recent Log File</td>
                            </tr>
                            <?php
                            global $wpdb;

                            $results = $wpdb->get_results(sprintf("select *, (SELECT COUNT(*) FROM %s%s WHERE status = 'IN_PROGRESS' AND %s%s.import_id = %s%s.id) as batch_subsriptions_in_progress, (SELECT log_file FROM %s%s WHERE %s%s.import_id = %s%s.id ORDER BY created_at DESC LIMIT 1) as log_file from %s%s",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "imports",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "imports",
                                $wpdb->prefix,
                                "imports"));

                            foreach ($results as $instance) :
                                $object_ids = unserialize($instance->algolia_object_ids);
                                ?>

                                <tr>
                                    <td><?php echo count($object_ids);?></td>
                                    <td><?php echo date('Y-m-d', strtotime($instance->created_at));  ?></td>
                                    <td>
                                        <?php if($instance->in_mandrill): ?>
                                            <button type="button" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-type="unsubscribed" data-import-id="<?php echo $instance->id; ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_batch_subscribe_nonce' ); ?>" class="btn btn-warning js-batch-unsubscribe-contacts-button" data-toggle="modal">Unsubscribe Contacts</button>
                                        <?php else: ?>
                                            <button type="button" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-type="subscribed" data-import-id="<?php echo $instance->id; ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_batch_subscribe_nonce' ); ?>" class="btn btn-primary js-batch-subscribe-contacts-button" data-toggle="modal">Subscribe Contacts</button>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo ($instance->batch_subsriptions_in_progress > 0) ? "Syncing" : "Not Syncing"?></td>
                                    <td><?php echo $instance->log_file ? '<a target="_blank" href="' . $this->data['plugin_url'] . 'logs/' . $instance->log_file . '">Log File</a>': 'N/A'; ?></td>
                                </tr>

                                <?php
                            endforeach;
                            ?>
                        </table>
                    </div>
                    <div class="tab-pane" id="exports_list">
                        <table class="table">
                            <tr>
                                <td>Number Of Students</td>
                                <td>Creation Date</td>
                                <td>Subscribe/Unsubscribe</td>
                                <td>Status</td>
                                <td>Recent Log File</td>
                            </tr>
                            <?php
                            global $wpdb;
                            $results = $wpdb->get_results(sprintf("select *, (SELECT COUNT(*) FROM %s%s WHERE status = 'IN_PROGRESS' AND %s%s.export_id = %s%s.id) as batch_subsriptions_in_progress, (SELECT log_file FROM %s%s WHERE %s%s.export_id = %s%s.id ORDER BY created_at DESC LIMIT 1) as log_file from %s%s",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "exports",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "batch_subscription_crons",
                                $wpdb->prefix,
                                "exports",
                                $wpdb->prefix,
                                "exports"
                            ));


                            foreach ($results as $instance) :
                                $object_ids = unserialize($instance->algolia_object_ids);
                                ?>

                                <tr>
                                    <td><?php echo count($object_ids);?></td>
                                    <td><?php echo date('Y-m-d', strtotime($instance->created_at));  ?></td>
                                    <td>
                                        <?php if($instance->in_mandrill): ?>
                                            <button type="button" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-type="unsubscribed" data-export-id="<?php echo $instance->id; ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_batch_subscribe_nonce' ); ?>" class="btn btn-warning js-batch-unsubscribe-contacts-button" data-toggle="modal">Unsubscribe Contacts</button>
                                        <?php else: ?>
                                            <button type="button" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-type="subscribed" data-export-id="<?php echo $instance->id; ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_batch_subscribe_nonce' ); ?>" class="btn btn-primary js-batch-subscribe-contacts-button" data-toggle="modal">Subscribe Contacts</button>
                                        <?php endif; ?>

                                    </td>
                                    <td><?php echo ($instance->batch_subsriptions_in_progress > 0) ? "Syncing" : "Not Syncing"?></td>
                                    <td><?php echo $instance->log_file ? '<a target="_blank" href="' . $this->data['plugin_url'] . 'logs/' . $instance->log_file . '">Log File</a>': 'N/A'; ?></td>
                                </tr>

                                <?php
                            endforeach;
                            ?>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>

    (function($) {

        $(document).on('click', '.js-batch-subscribe-contacts-button, .js-batch-unsubscribe-contacts-button', function() {

            debugger;
            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                importId = $(this).attr("data-import-id"),
                exportId = $(this).attr("data-export-id"),
                type = $(this).attr("data-type"),
                data,
                listId = window.listId;

                data = {action: "crmc_batch_subscribe_contacts", nonce: nonce, type: type, export_id: exportId, list_id: listId};

                if(importId)
                {
                    data = {action: "crmc_batch_subscribe_contacts", nonce: nonce, type: type, import_id: importId, list_id: listId};
                }

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                beforeSend: function() {

                    debugger;
                    window.ajaxAttachLoadingDots(".js-batch-subscribe-contacts-button");
                },
                data: data
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }).done(function(response,status,jqXHR) {
                debugger;
                window.ajaxRenderNoticesAndErrors(response, '.js-batch-subscribe-modal .js-modal-header');
                window.ajaxRemoveLoadingDots(".js-batch-subscribe-contacts-button");
            });

        });

    })(jQuery);

</script>