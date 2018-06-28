<div class="container">

    <?php
    include('partials/nav.php');
    ?>

    <div class="row">
        <div class="col-md-12">

            <?php
            // Generate a custom nonce value.
            $crmc_import_contacts_nonce = wp_create_nonce( 'crmc_import_contacts_nonce' );
            $errors = get_transient('errors');
            ?>
            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                <input type="hidden" name="action" value="crmc_import_contacts">
                <input type="hidden" name="crmc_import_contacts_nonce" value="<?php echo $crmc_import_contacts_nonce ?>" />
                <div class="form-group">
                    <label for="studentImportFile">File input</label>
                    <input type="file" id="studentImportFile" name="studentImportFile">
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>

        </div>
    </div>
</div>