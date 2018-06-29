jQuery(document).ready(function($){


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

        debugger;
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
                debugger;
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


    $(document).on('change', '.js-property-name', function() {

        debugger;
        // the add property button inside each group has all the data attributes you need to modify the group name
        var $button = $(event.target).closest('.js-group').find('.js-add-property'),
            nonce = $button.attr("data-nonce"),
            url = $button.attr("data-url"),
            group = $button.attr("data-group"),
            property_name = $(event.target).val(),
            property_id = $(event.target).closest('.js-property').data('property');


        // perform ajax request
        jQuery.ajax({
            type : "post",
            dataType : "json",
            url : url,
            data : {action: "crmc_set_property_name", nonce: nonce, group: group, property_name: property_name, property_id: property_id}
        }).fail(function(r,status,jqXHR) {
            console.log('failed');
        })
            .done(function(r,status,jqXHR) {
                console.log('success');
            });

    });

    $(document).on('change', '.js-property-value', function() {

        debugger;
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
                debugger;
                console.log('success');
            });

    });


});