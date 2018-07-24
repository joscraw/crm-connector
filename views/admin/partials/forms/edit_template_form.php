<form class="js-edit-template-form" data-url="<?php echo admin_url('admin-ajax.php'); ?>">
    <input type="hidden" name="action" value="crmc_post_edit_template_form">
    <input type="hidden" name="template_id" value="<?php echo $template['id']; ?>">

    <input type="hidden" name="crmc_edit_template_nonce" value="<?php echo wp_create_nonce( 'crmc_edit_template_nonce' ); ?>" />
    <div class="form-group required">
        <label for="name">Template Name</label>
        <small class="text-muted">Choose a creative name for your template!</small>

        <?php
        echo renderErrors($errors['template_name']);
        ?>

        <input type="text" value="<?php echo isset($template['name']) ? $template['name'] : ''; ?>" class="form-control" id="template_name" name="template_name" placeholder="Template Name">
    </div>

    <div class="form-group required">
        <label for="name">HTML</label>
        <small class="text-muted">HTML</small>

        <?php
        echo renderErrors($errors['template_html']);
        ?>
        <textarea name="template_html" class="form-control" rows="40"></textarea>
    </div>

    <button type="button" class="btn btn-warning js-edit-template-submit-form-button">Submit</button>
</form>

<script>

    (function($) {

        $(document).on('click', '.js-edit-template-submit-form-button', function() {

            var $form = $('.js-edit-template-form'),
                url = $form.attr('data-url'),
                formData = new FormData($form.get(0)),
                $view = $('.js-edit-template');

            // perform ajax request
            jQuery.ajax({
                type : "post",
                url : url,
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                beforeSend: function() {
                    window.ajaxAttachLoadingSpinnerToContainer('.js-container');
                }
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            }).done(function(response,status,jqXHR) {

                response = JSON.parse(response);
                var $container = $('.js-container').find('.tab-content').find('.js-edit-template-container');
                if(response.html_form)
                    $container.html(response.html_form);

                window.ajaxRemoveLoadingSpinnerFromContainer('.js-container');
                window.ajaxRenderNoticesAndErrors(response, '.js-container .tab-content');

                window.scrollTo(0, 0);

            });

        });

    })(jQuery);

</script>
