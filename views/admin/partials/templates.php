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
                <button type="button" data-template-id="<?php echo $template['id']; ?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_edit_template_nonce' ); ?>" class="btn btn-warning js-edit-template-button">Edit Template</button>
            <td>
                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <input type="hidden" name="action" value="crmc_remove_template">
                    <input type="hidden" name="crmc_remove_template_nonce" value="<?php echo wp_create_nonce( 'crmc_remove_template_nonce' ); ?>" />
                    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">
                    <button type="button" data-template-id="<?php echo $template['id']; ?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_delete_template_nonce' ); ?>" class="btn btn-danger js-delete-template-button">Delete Template</button>
                </form>
            </td>
        </tr>
        </tbody>

        <?php
    endforeach;
    ?>

</table>


<?php render_pagination($total_items, 'crmc_settings', 'invitation_settings', 'templates', 'templates_offset', 'templates_count'); ?>


<script>

    (function($) {

        $(document).on('click', '.js-edit-template-button', function() {

            $view = $('.js-edit-template');

            debugger;
            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                template_id = $(this).attr("data-template-id");

            // perform ajax request
            jQuery.ajax({
                type : "get",
                dataType : "json",
                url : url,
                beforeSend: function() {
                    debugger;
                    window.ajaxAttachLoadingSpinnerToContainer('.js-container');
                },
                data : {action: "crmc_get_edit_template_form", nonce: nonce, template_id: template_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }).done(function(response,status,jqXHR) {


                debugger;
                if(response.type === 'success')
                {
                    var $container = $('.js-container').find('.tab-content').find('.js-edit-template-container');

                    $('.js-container').find('.tab-content').find('.tab-pane').removeClass('active');
                    $('.js-container').find('.js-left-nav').find('li').removeClass('active');
                    $container.addClass('active');

                    $container.html(response.html_form);
                }

                window.ajaxRemoveLoadingSpinnerFromContainer('.js-container');
                window.ajaxRenderNoticesAndErrors(response, '.js-container .tab-content');

            });

        });

        $(document).on('click', '.js-delete-template-button', function() {

            debugger;
            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                template_id = $(this).attr("data-template-id");

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                beforeSend: function() {
                    debugger;
                    window.ajaxAttachLoadingSpinnerToContainer('.js-container');
                },
                data : {action: "crmc_delete_template", nonce: nonce, template_id: template_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }).done(function(response,status,jqXHR) {

                if(response.type === 'success')
                {
                    debugger;
                    $(this).closest('tr').remove();
                }

                window.ajaxRemoveLoadingSpinnerFromContainer('.js-container');
                window.ajaxRenderNoticesAndErrors(response, '.js-container .tab-content');

            }.bind(this));

        });

    })(jQuery);

</script>



