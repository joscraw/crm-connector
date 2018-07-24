<table class="table table-hover">
    <thead>
    <tr>
        <th scope="col">Template Name</th>
        <th scope="col">Created at</th>
        <th scope="col">Thumbnail</th>
        <th scope="col">Edit Template</th>
        <th scope="col">Delete Template</th>
    </tr>
    </thead>

    <?php
    $total_items =  $templates_response['total_items'];
    $templates =    $templates_response['templates'];
    foreach($templates as $template) :
        ?>

        <tbody>
        <tr>
            <td><?php echo $template['name']; ?></td>
            <td><?php echo date('Y-m-d', strtotime($template['date_created']));  ?></td>
            <td><img src="<?php echo $template['thumbnail'];?>" width="200" height="200"></td>
            <td>
                <form method="get" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="crmc_edit_template">
                    <input type="hidden" name="crmc_edit_template_nonce" value="<?php echo wp_create_nonce( 'crmc_edit_template_nonce' ); ?>" />
                    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                    <button type="submit" class="btn btn-warning">Edit Template</button>
                </form>
            <td>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="crmc_remove_template">
                    <input type="hidden" name="crmc_remove_template_nonce" value="<?php echo wp_create_nonce( 'crmc_remove_template_nonce' ); ?>" />
                    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                    <button type="submit" class="btn btn-danger">Delete Template</button>
                </form>
            </td>
        </tr>
        </tbody>

        <?php
    endforeach;
    ?>

</table>


<?php render_pagination($total_items, 'crmc_settings', 'invitation_settings', 'templates', 'templates_offset', 'templates_count'); ?>