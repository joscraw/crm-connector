<div class="row">

    <?php
    $errors = get_transient('errors');
    ?>

    <div class="col-md-3">
        <ul class="nav nav-tabs nav-stacked navbar-left">
            <li class="active"><a href="#create_list" data-toggle="pill">Create List</a></li>
            <li><a href="#lists" data-toggle="pill">Lists</a></li>
            <li><a href="#templates" data-toggle="pill">Templates</a></li>
        </ul>
    </div>

    <div class="col-md-9">
        <div class="tab-content">
            <div class="tab-pane active" id="create_list">
                <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post">
                    <input type="hidden" name="action" value="crmc_add_list">
                    <input type="hidden" name="crmc_add_list_nonce" value="<?php echo wp_create_nonce( 'crmc_add_list_nonce' ); ?>" />
                    <div class="form-group required">
                        <label for="name">List Name</label>
                        <small class="text-muted">Choose a creative name for your list!</small>

                        <?php
                        echo renderErrors($errors['list_name']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['name']) ? $_GET['name'] : ''; ?>" class="form-control" id="name" name="list_name" placeholder="List Name">
                    </div>

                    <div class="form-group required">
                        <label for="company">Company</label>

                        <?php
                        echo renderErrors($errors['company']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['company']) ? $_GET['contact']['company'] : ''; ?>" class="form-control" id="company" name="company" placeholder="Company">
                    </div>

                    <div class="form-group required">
                        <label for="address1">Address One</label>

                        <?php
                        echo renderErrors($errors['address1']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['address1']) ? $_GET['contact']['address1'] : ''; ?>" class="form-control" id="address1" name="address1" placeholder="Address One">
                    </div>

                    <div class="form-group">
                        <label for="address2">Address Two</label>

                        <input type="text" value="<?php echo isset($_GET['contact']['address2']) ? $_GET['contact']['address2'] : ''; ?>" class="form-control" id="address2" name="address2" placeholder="Address Two">
                    </div>

                    <div class="form-group required">
                        <label for="city">City</label>

                        <?php
                        echo renderErrors($errors['city']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['city']) ? $_GET['contact']['city'] : ''; ?>" class="form-control" id="city" name="city" placeholder="City">
                    </div>

                    <div class="form-group required">
                        <label for="state">State</label>

                        <?php
                        echo renderErrors($errors['state']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['state']) ? $_GET['contact']['state'] : ''; ?>" class="form-control" id="state" name="state" placeholder="State">
                    </div>

                    <div class="form-group required">
                        <label for="zip">Zip</label>

                        <?php
                        echo renderErrors($errors['zip']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['zip']) ? $_GET['contact']['zip'] : ''; ?>" class="form-control" id="zip" name="zip" placeholder="Zip">
                    </div>

                    <div class="form-group required">
                        <label for="country">County</label>

                        <?php
                        echo renderErrors($errors['country']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['contact']['country']) ? $_GET['contact']['country'] : ''; ?>" class="form-control" id="country" name="country" placeholder="Country">
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" value="<?php echo isset($_GET['contact']['phone']) ? $_GET['contact']['phone'] : ''; ?>" class="form-control" id="phone" name="phone" placeholder="Phone">
                    </div>

                    <div class="form-group required">
                        <label for="permission_reminder">Permission Reminder</label>

                        <?php
                        echo renderErrors($errors['permission_reminder']);
                        ?>

                        <small class="text-muted"><a href="https://mailchimp.com/help/edit-the-permission-reminder/?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs&_ga=2.266426702.1157657947.1531760872-769419101.1517934903">Click here</a> for more info on this field</small>
                        <input type="text" value="<?php echo isset($_GET['permission_reminder']) ? $_GET['permission_reminder'] : ''; ?>" class="form-control" id="permission_reminder" name="permission_reminder" placeholder="Permission Reminder">
                    </div>

                    <div class="form-group required">
                        <label for="from_name">From Name</label>

                        <?php
                        echo renderErrors($errors['from_name']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['campaign_defaults']['from_name']) ? $_GET['campaign_defaults']['from_name'] : ''; ?>" class="form-control" id="from_name" name="from_name" placeholder="From Name">
                    </div>

                    <div class="form-group required">
                        <label for="from_email">From Email</label>

                        <?php
                        echo renderErrors($errors['from_email']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['campaign_defaults']['from_email']) ? $_GET['campaign_defaults']['from_email'] : ''; ?>" class="form-control" id="from_email" name="from_email" placeholder="From Email">
                    </div>

                    <div class="form-group required">
                        <label for="subject">Subject</label>

                        <?php
                        echo renderErrors($errors['subject']);
                        ?>

                        <input type="text" value="<?php echo isset($_GET['campaign_defaults']['subject']) ? $_GET['campaign_defaults']['subject'] : ''; ?>" class="form-control" id="subject" name="subject" placeholder="Subject">
                    </div>

                    <button type="submit" class="btn btn-default">Submit</button>
                </form>
            </div>

            <div class="tab-pane" id="lists">

              lists
            </div>

            <div class="tab-pane" id="templates">
                templates
            </div>

        </div>
    </div>
</div>
