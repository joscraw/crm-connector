<div class="container">

    <?php
    global $wpdb;
    $e = get_transient('errors');
    include('partials/nav.php');
    ?>

    <div class="row">

        <?php
        $successMessage = get_transient('successMessage');
        echo renderSuccessMessage($successMessage);
        $e = get_transient('errors');
        echo renderGenericErrorMessage(get_transient('errors')['main']);
        ?>


        <div class="col-md-2">
            <ul class="nav nav-pills nav-stacked">
                <?php
                $name = get_transient('errors');
                ?>
                <li role="presentation" class="<?php echo ($_GET['pill'] === 'list' ? 'active' : ''); ?>"><a href="<?php echo renderSubMenuURL('list'); ?>">Chapters</a></li>
                <li role="presentation" class="<?php echo ($_GET['pill'] === 'add' ? 'active' : ''); ?>"><a href="<?php echo renderSubMenuURL('add'); ?>">Add Chapter</a></li>
                <li role="presentation" class="<?php echo ($_GET['pill'] === 'mapping' ? 'active' : ''); ?>"><a href="<?php echo renderSubMenuURL('mapping'); ?>">Chapter Mapping</a></li>
            </ul>
        </div>


        <div class="col-md-10">
            <h1>Chapters <?php echo isset($_GET['pill']) ? '/ ' . str_replace('-', ' ', ucfirst($_GET['pill'])) : ''; ?></small></h1>

            <?php
            $name = get_transient('errors');
            if($_GET['pill'] === 'list'):
                // Generate a custom nonce value.
                $crmc_add_chapter_nonce = wp_create_nonce( 'crmc_add_chapter_nonce' );

                // this adds the prefix which is set by the user upon instillation of wordpress
                $table_name = $wpdb->prefix."chapters";
                // this will get the data from your table
                $results = $wpdb->get_results("SELECT * FROM $table_name");

                ?>

                <table class="table table-hover">
                    <tr>
                        <th>Chapter</th>
                    </tr>
                <?php
                    foreach ($results as $instance) :
                ?>
                    <tr>
                        <td><?php echo $instance->chapter_name; ?> | <a href="">Add Students</a></td>
                    </tr>
                <?php
                    endforeach;
                ?>
                </table>
            <?php
            elseif ($_GET['pill'] === 'add'):
                // Generate a custom nonce value.
                $crmc_add_chapter_nonce = wp_create_nonce( 'crmc_add_chapter_nonce' );
                $errors = get_transient('errors');
                ?>
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="crmc_add_chapter">
                    <input type="hidden" name="crmc_add_chapter_nonce" value="<?php echo $crmc_add_chapter_nonce ?>" />
                    <div class="form-group">

                        <?php
                        $errors = get_transient('errors');
                        ?>

                        <label for="hubspotAPIKey">Chapter Name</label>
                        <?php
                        echo renderErrors($errors['crmc_chapter_name']);
                        ?>
                        <input type="text" value="<?php echo get_option('crmc_chapter_name'); ?>" class="form-control" id="chapter" name="crmc_chapter_name" placeholder="Chapter Name">
                        <p class="help-block">Name of the school you would like to create</p>
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
                <?php
            elseif ($_GET['pill'] === 'mapping'):

                ob_start();
                ?>
                <div class="form-group js-group">
                    <label for="group">Group</label>
                    <input type="text" class="form-control" name="groups[group_index][group]" placeholder="Group Name">
                    <button class="btn btn-default js-add-property" data-group="{{group_id}}" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_property_nonce' ); ?>" type="button">Add Property</button>
                </div>
                <?php
                $prototype_group_form = ob_get_clean();
                ob_start();
                ?>
            <div class="row">
                <div class="col-md-10 col-md-offset-2">
                    <div class="form-group js-property">
                        <label for="group">Property</label>
                        <input type="text" class="form-control" name="groups[group_index][properties][property_index][property_name]" placeholder="Property Name">
                        <input type="text" class="form-control" name="groups[group_index][properties][property_index][property_value]" placeholder="Property Value">
                    </div>
                </div>
            </div>
                <?php
                $prototype_property_form = ob_get_clean();
                ?>


                <div class="row">
                        <div class="col-md-10">
                            <button data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_group_nonce' ); ?>" class="btn btn-default js-add-property-group" type="button">Add Property Group</button>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary pull-right" type="submit">Create</button>
                        </div>
                </div>


                <div class="row">
                    <div class="col-md-10 js-form-container" data-prototype-group-form="<?php echo htmlspecialchars($prototype_group_form);?>" data-prototype-property-form="<?php echo htmlspecialchars($prototype_property_form);?>">

                        <?php
                        global $wpdb;
                        $table_name = $wpdb->prefix."groups";
                        // this will get the data from your table
                        $groups = $wpdb->get_results("SELECT * FROM $table_name");
                        $i = 0;
                        foreach ($groups as $group) :
                            ?>
                            <div class="form-group js-group">
                                <label for="group">Group</label>
                                <input type="text" class="form-control" value="<?php echo $group->group_name;?>" name="groups[<?php echo $i; ?>][group]" placeholder="Group Name">
                                <button class="btn btn-default js-add-property" data-group="<?php echo $group->id;?>" data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_property_nonce' ); ?>" type="button">Add Property</button>


                                <?php
                                $table_name = $wpdb->prefix."properties";
                                // this will get the data from your table
                                $properties = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE group_id = %d", $group->id));
                                $j = 0;
                                foreach ($properties as $property) :
                                    ?>
                                    <div class="row">
                                        <div class="col-md-10 col-md-offset-2">
                                            <div class="form-group js-property">
                                                <label for="group">Property</label>
                                                <input type="text" class="form-control" value="<?php echo $property->property_name; ?>" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_name]" placeholder="Property Name">
                                                <input type="text" class="form-control" value="<?php echo $property->property_value; ?>" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_value]" placeholder="Property Value">
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                $j++;
                                endforeach;
                                ?>

                            </div>
                            <?php
                        $i++;
                        endforeach;
                        ?>


                    </div>
                    <div class="col-md-2">
                    </div>
                </div>



            <?php
            else:
            endif;
            ?>
        </div>
    </div>
</div>