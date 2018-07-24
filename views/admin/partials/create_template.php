<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
    <input type="hidden" name="action" value="crmc_create_template">
    <input type="hidden" name="crmc_create_template_nonce" value="<?php echo wp_create_nonce( 'crmc_create_template_nonce' ); ?>" />
    <div class="form-group required">
        <label for="name">Template Name</label>
        <small class="text-muted">Choose a creative name for your template!</small>

        <?php
        echo renderErrors($errors['template_name']);
        ?>

        <input type="text" value="<?php echo isset($_GET['template_name']) ? $_GET['template_name'] : ''; ?>" class="form-control" id="template_name" name="template_name" placeholder="Template Name">
    </div>

    <div class="form-group required">
        <label for="name">HTML</label>
        <small class="text-muted">HTML</small>

        <?php
        echo renderErrors($errors['template_html']);
        ?>
        <textarea name="template_html" class="form-control" rows="40"><?php echo isset($_GET['html']) ? $_GET['html'] : ''; ?></textarea>
    </div>

    <button type="submit" class="btn btn-default">Submit</button>
</form>