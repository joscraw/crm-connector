<div class="row">

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left">
            <li class="active"><a href="#algolia" data-toggle="pill">Algolia</a></li>
            <li><a href="#hubspot" data-toggle="pill">Hubspot</a></li>
            <li><a href="#mailchimp" data-toggle="pill">MailChimp</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">

                <div class="tab-pane active" id="algolia">

                    <?php
                    // Generate a custom nonce value.
                    $crmc_add_algolia_api_keys_nonce = wp_create_nonce( 'crmc_add_algolia_api_keys_nonce' );
                    $errors = get_transient('errors');
                    ?>

                    <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                        <input type="hidden" name="action" value="crmc_add_algolia_api_keys">
                        <input type="hidden" name="crmc_add_algolia_api_keys_nonce" value="<?php echo $crmc_add_algolia_api_keys_nonce ?>" />
                        <div class="form-group">
                            <label for="algoliaApplicationId">Algolia Application Id</label>

                            <?php echo renderErrors($errors['crmc_algolia_application_id']); ?>

                            <input type="text" value="<?php echo get_option('crmc_algolia_application_id'); ?>" class="form-control" id="algoliaApplicationId" name="crmc_algolia_application_id" placeholder="Algolia Application Id">
                        </div>
                        <div class="form-group">
                            <label for="algoliaAPIKey">Algolia API Key</label>

                            <?php echo renderErrors($errors['crmc_algolia_api_key']); ?>

                            <input type="text" value="<?php echo get_option('crmc_algolia_api_key'); ?>" class="form-control" id="algoliaAPIKey" name="crmc_algolia_api_key" placeholder="Algolia API Key">
                        </div>
                        <div class="form-group">
                            <label for="algoliaAPIKey">Algolia Index Name <small>(Don't change this value unless you know exactly what you are doing.)</small></label>

                            <?php echo renderErrors($errors['crmc_algolia_index']); ?>

                            <input type="text" value="<?php echo get_option('crmc_algolia_index'); ?>" class="form-control" id="algoliaIndex" name="crmc_algolia_index" placeholder="Algolia Index Name">
                        </div>
                        <div class="form-group">
                            <label for="searchOnlyAPIKey">Algolia Search Only API Key</label>

                            <?php echo renderErrors($errors['crmc_algolia_search_only_api_key']); ?>

                            <input type="text" value="<?php echo get_option('crmc_algolia_search_only_api_key'); ?>" class="form-control" id="searchOnlyAPIKey" name="crmc_algolia_search_only_api_key" placeholder="Algolia Search Only API Key">
                        </div>
                        <p class="help-block"><a href="https://www.algolia.com/api-keys">Here</a> to obtain your Algolia API Creds</p>
                        <button type="submit" class="btn btn-default">Submit</button>
                    </form>
                </div>

                <div class="tab-pane" id="hubspot">

                    <?php
                    $name = get_transient('errors');
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
                </div>

                <div class="tab-pane" id="mailchimp">MailChimp</div>

        </div>
    </div>
 </div>



