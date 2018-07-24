<!-- Modals -->
<?php include(\CRMConnector\Utils\CRMCFunctions::plugin_dir() . '/views/admin/partials/modals/_batch_subscribe.php'); ?>

<table class="table table-hover">
    <thead>
    <tr>
        <th scope="col">List Name</th>
        <th scope="col">Date Created</th>
        <th scope="col">Member Count</th>
        <th scope="col">Unsubscribe Count</th>
        <th>Subscribe Members</th>
        <th>Edit List</th>
        <th>Remove List</th>
    </tr>
    </thead>

    <?php
    $total_items =  $lists_response['total_items'];
    $lists =    $lists_response['lists'];
    foreach($lists as $list) :
        ?>

        <tbody>
        <tr>
            <td><?php echo $list['name']; ?></td>
            <td><?php echo date('Y-m-d', strtotime($list['date_created']));  ?></td>
            <td><?php echo $list['stats']['member_count']; ?></td>
            <td><?php echo $list['stats']['unsubscribe_count']; ?></td>
            <td>
                <button type="submit" data-list-id="<?php echo $list['id']; ?>" class="btn btn-primary js-show-batch-subscribe-modal">Batch Sub/Unsub Tool</button>
            </td>
            <td>
                <form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="crmc_edit_list">
                    <input type="hidden" name="crmc_edit_list_nonce" value="<?php echo wp_create_nonce( 'crmc_edit_list_nonce' ); ?>" />
                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                    <button type="submit" class="btn btn-warning">Edit List</button>
                </form>
            <td>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="crmc_remove_list">
                    <input type="hidden" name="crmc_remove_list_nonce" value="<?php echo wp_create_nonce( 'crmc_remove_list_nonce' ); ?>" />
                    <input type="hidden" name="list_id" value="<?php echo $list['id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete List</button>
                </form>
            </td>
        </tr>
        </tbody>

        <?php
    endforeach;
    ?>

</table>

<script>

    (function($){

        $(document).on('click', '.js-show-batch-subscribe-modal', function(event) {
            window.listId = $(event.target).data('list-id');
            var $modal = $('.js-batch-subscribe-modal');
            $modal.modal('show');
        }.bind(this));

    })(jQuery);


</script>