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
            debugger;
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
                $div.append(prototype);
            }.bind(this, event));


    });



});