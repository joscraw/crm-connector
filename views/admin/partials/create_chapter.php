<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
    <input type="hidden" name="action" value="crmc_add_chapter">
    <input type="hidden" name="crmc_add_chapter_nonce" value="<?php echo wp_create_nonce( 'crmc_add_chapter_nonce' ); ?>" />
    <div class="form-group">

        <label for="hubspotAPIKey">Chapter Name</label>

        <?php
        $errors = get_transient('errors');
        echo renderErrors($errors['crmc_chapter_name']);
        ?>

        <input type="text" value="<?php echo get_option('crmc_chapter_name'); ?>" class="form-control" id="chapter" name="crmc_chapter_name" placeholder="Chapter Name">
        <p class="help-block">Name of the school you would like to create</p>
    </div>
    <button type="submit" class="btn btn-default">Submit</button>
</form>