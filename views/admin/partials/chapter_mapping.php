<?php
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
        <div class="form-group js-property" data-property="{{property_id}}">
            <div class="row">
                <div class="col-md-5">
                    <label for="group">Label</label>
                    <input type="text" class="form-control js-property-label" name="groups[group_index][properties][property_index][property_name]" placeholder="Property Label">
                </div>
                <div class="col-md-4">
                    <label for="group">Description</label>
                    <textarea class="form-control js-property-description" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_value]"><?php echo $property->description; ?></textarea>
                </div>
                <div class="col-md-3">
                    <label for="group">Data Type</label>
                    <select class="js-data-type form-control">
                        <option value="" disabled selected>Select an Option</option>
                        <option value="string">String</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="datetime">Datetime</option>
                        <option value="enumeration">enumeration</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$prototype_property_form = ob_get_clean();
?>

<div class="row">
    <div class="col-md-10">
        <button data-url="<?php echo admin_url('admin-ajax.php'); ?>" data-nonce="<?php echo wp_create_nonce( 'crmc_add_group_nonce' ); ?>" class="btn btn-success js-add-property-group" type="button">Add Property Group</button>
        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" style="display: inline-block">
            <button class="btn btn-primary js-sync-mapping-button" type="submit">Sync Mapping To CRM</button>
            <input type="hidden" name="action" value="crmc_sync_mapping_to_hubspot">
            <input type="hidden" name="crmc_sync_mapping_to_hubspot_nonce" value="<?php echo wp_create_nonce( 'crmc_sync_mapping_to_hubspot_nonce' ); ?>" />
        </form>
    </div>
    <div class="col-md-2">
    </div>
</div>

<div class="row">
    <div class="col-md-10 js-form-container" data-prototype-group-form="<?php echo htmlspecialchars($prototype_group_form);?>" data-prototype-property-form="<?php echo htmlspecialchars($prototype_property_form);?>">
        <form>

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
                    <input type="text" class="form-control" value="<?php echo $group->displayName;?>" name="groups[<?php echo $i; ?>][group]" placeholder="Group Name">
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
                                <div class="form-group js-property" data-property="<?php echo $property->id; ?>">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label for="group">Label</label>
                                            <input type="text" class="form-control js-property-label" value="<?php echo $property->label; ?>" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_name]" placeholder="Property Label">
                                        </div>
                                        <div class="col-md-4">
                                            <label for="group">Description</label>
                                            <textarea class="form-control js-property-description" name="groups[<?php echo $i; ?>][properties][<?php echo $j; ?>][property_value]"><?php echo $property->description; ?></textarea>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="group">Data Type</label>
                                            <select class="js-data-type form-control">
                                                <option value="" disabled <?php echo ($property->type === null) ? "selected" : "";?>>Select an Option</option>
                                                <option value="string" <?php echo ($property->type === "string") ? "selected" : "";?>>String</option>
                                                <option value="number" <?php echo ($property->type === "number") ? "selected" : "";?>>Number</option>
                                                <option value="date" <?php echo ($property->type === "date") ? "selected" : "";?>>Date</option>
                                                <option value="datetime" <?php echo ($property->type === "datetime") ? "selected" : "";?>>Datetime</option>
                                                <option value="enumeration" <?php echo ($property->type === "enumeration") ? "selected" : "";?>>enumeration</option>
                                            </select>
                                        </div>
                                    </div>
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

        </form>
    </div>
</div>


<script>
    (function($){

        $(document).on('click', '.js-add-property-group', function() {

            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                container;

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_create_group", nonce: nonce}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    var prototype =  (container = $('.js-form-container')).data('prototype-group-form');
                    var groupIndex = $('.js-group').length;
                    prototype = prototype.replace(/group_index/g, groupIndex);
                    prototype = prototype.replace(/\{\{group_id\}\}/g, r.group_id);
                    container.append(prototype);
                });

        });

        $(document).on('click', '.js-add-property', function() {

            var nonce = $(this).attr("data-nonce"),
                url = $(this).attr("data-url"),
                group = $(this).attr("data-group"),
                container,
                $div;

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_create_property", nonce: nonce, group: group}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(event, r,status,jqXHR) {

                    var prototype = (container = $('.js-form-container')).data('prototype-property-form');
                    $div = $(event.target).closest('.js-group');
                    var groupIndex = $('.js-group').index($div);
                    var propertyIndex = $div.find('.js-property').length;
                    prototype = prototype.replace(/group_index/g, groupIndex);
                    prototype = prototype.replace(/property_index/g, propertyIndex);
                    prototype = prototype.replace(/\{\{property_id\}\}/g, r.property_id);
                    $div.append(prototype);
                }.bind(this, event));

        });


        $(document).on('change', '.js-group > input[type="text"]', function() {

            // the add property button inside each group has all the data attributes you need to modify the group name

            var $button = $(event.target).parent('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                group_name = $(event.target).val();

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_group_name", nonce: nonce, group: group, group_name: group_name}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });


        $(document).on('change', '.js-property-label', function() {

            debugger;
            // the add property button inside each group has all the data attributes you need to modify the group name
            var $button = $(event.target).closest('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                label = $(event.target).val(),
                property_id = $(event.target).closest('.js-property').data('property');


            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_property_name", nonce: nonce, group: group, label: label, property_id: property_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });

        $(document).on('change', '.js-property-description', function() {

            // the add property button inside each group has all the data attributes you need to modify the group name
            var $button = $(event.target).closest('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                property_value = $(event.target).val(),
                property_id = $(event.target).closest('.js-property').data('property');

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_property_value", nonce: nonce, group: group, property_value: property_value, property_id: property_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });

        $(document).on('change', '.js-data-type', function() {

            debugger;
            // the add property button inside each group has all the data attributes you need to modify the group name
            var $button = $(event.target).closest('.js-group').find('.js-add-property'),
                nonce = $button.attr("data-nonce"),
                url = $button.attr("data-url"),
                group = $button.attr("data-group"),
                type = $(event.target).val(),
                property_id = $(event.target).closest('.js-property').data('property');

            // perform ajax request
            jQuery.ajax({
                type : "post",
                dataType : "json",
                url : url,
                data : {action: "crmc_set_data_type", nonce: nonce, group: group, type: type, property_id: property_id}
            }).fail(function(r,status,jqXHR) {
                console.log('failed');
            })
                .done(function(r,status,jqXHR) {
                    console.log('success');
                });

        });

    })(jQuery);

</script>