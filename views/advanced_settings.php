<div class="container">


    <?php
    include_once('partials/nav.php');
    ?>

    <div class="row">

        <?php
        $successMessage = get_transient('successMessage');
        echo renderSuccessMessage($successMessage);
        echo renderGenericErrorMessage(get_transient('errors')['main']);
        ?>

        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <?php
                $path = "admin.php?page={$_GET['page']}&pill=hubspot";
                $hubspot_url = admin_url($path);
                ?>
                <li role="presentation" class="<?php echo ($_GET['pill'] === 'hubspot' ? 'active' : ''); ?>"><a href="<?php echo $hubspot_url; ?>">HubSpot</a></li>
                <?php
                $path = "admin.php?page={$_GET['page']}&pill=algolia";
                $algolia_url = admin_url($path);
                ?>
                <li role="presentation" class="<?php echo ($_GET['pill'] === 'algolia' ? 'active' : ''); ?>"><a href="<?php echo $algolia_url; ?>">Algolia</a></li>
            </ul>
        </div>

        <div class="col-md-10">
            <h1>Advanced Settings <small><?php echo isset($_GET['pill']) ? '/ ' . ucfirst($_GET['pill']) : ''; ?></small></h1>

            <?php
            $name = get_transient('errors');
            if($_GET['pill'] === 'hubspot'):
            // Generate a custom nonce value.
            $crmc_add_hubspot_api_key_nonce = wp_create_nonce( 'crmc_add_hubspot_api_key_nonce' );
            ?>

            <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                <input type="hidden" name="action" value="crmc_add_hubspot_api_key">
                <input type="hidden" name="crmc_add_hubspot_api_key_nonce" value="<?php echo $crmc_add_hubspot_api_key_nonce ?>" />
                <div class="form-group">

                    <?php
                    $errors = get_transient('errors');
                    ?>

                    <label for="hubspotAPIKey">Hubspot API Key</label>
                    <?php
                     echo renderErrors($errors['crmc_hubspot_api_key']);
                    ?>
                    <input type="text" value="<?php echo get_option('crmc_hubspot_api_key'); ?>" class="form-control" id="hubspotAPIKey" name="crmc_hubspot_api_key" placeholder="Hubspot API Key">
                    <p class="help-block"><a href="https://knowledge.hubspot.com/articles/kcs_article/integrations/how-do-i-get-my-hubspot-api-key">Here</a> for more info on how to obtain your API key</p>
                </div>
                <button type="submit" class="btn btn-default">Submit</button>
            </form>
            <?php
            elseif ($_GET['pill'] === 'algolia'):
                $name = get_transient('errors');
                // Generate a custom nonce value.
                $crmc_add_algolia_api_keys_nonce = wp_create_nonce( 'crmc_add_algolia_api_keys_nonce' );
                $errors = get_transient('errors');
            ?>
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="crmc_add_algolia_api_keys">
                    <input type="hidden" name="crmc_add_algolia_api_keys_nonce" value="<?php echo $crmc_add_algolia_api_keys_nonce ?>" />
                    <div class="form-group">
                        <label for="algoliaApplicationId">Algolia Application Id</label>

                        <?php
                        echo renderErrors($errors['crmc_algolia_application_id']);
                        ?>
                        <input type="text" value="<?php echo get_option('crmc_algolia_application_id'); ?>" class="form-control" id="algoliaApplicationId" name="crmc_algolia_application_id" placeholder="Algolia Application Id">
                    </div>
                    <div class="form-group">
                        <label for="algoliaAPIKey">Algolia API Key</label>

                        <?php
                        echo renderErrors($errors['crmc_algolia_api_key']);
                        ?>

                        <input type="text" value="<?php echo get_option('crmc_algolia_api_key'); ?>" class="form-control" id="algoliaAPIKey" name="crmc_algolia_api_key" placeholder="Algolia API Key">
                    </div>
                    <p class="help-block"><a href="https://www.algolia.com/api-keys">Here</a> to obtain your Application Id and API key</p>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            <?php
            else:
            endif;
            ?>



        </div>
    </div>
</div>