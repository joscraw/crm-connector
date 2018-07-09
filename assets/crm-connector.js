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




    $(document).on('change', '.js-student-file', function(event) {

        debugger;
        var $form = $('.js-import-form'),
            e = event.target,
            chapterId = $form.find('.js-chapter-id').val();

        event.preventDefault();

        var file = e.files[0];

        // Create a new FormData object.
        var formData = new FormData();

        var nonce = $(e).attr("data-nonce"),
            url = $(e).attr("data-url");

        // Add the file to the request.
        formData.append('studentFile', file);
        formData.append('nonce', nonce);
        formData.append('action', 'crmc_get_column_names');
        formData.append('chapter_id', chapterId);

        // perform ajax request
        jQuery.ajax({
            url : url,
            data : formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST' // For jQuery < 1.9
        }).fail(function(r,status,jqXHR) {
            console.log('failed');
        })
            .done(function(r,status,jqXHR) {
                debugger;

                var response = JSON.parse(r);
                $(response['columns']).each(function(index, column) {
                    debugger;
                    var prototype =  $form.data('prototype-import-mapping-form');
                    prototype = prototype.replace(/column_index/g, index);
                    prototype = prototype.replace(/\{\{file_column_name\}\}/g, column);

                    var mapping = response['student_import_file_mapping'];
                    mapping = mapping.map(function(map){
                        return "<option value='"+map+"'>"+map+"</option>";
                    });

                    prototype = prototype.replace(/\{\{database_column_names\}\}/g, mapping.join(''));

                    $form.append(prototype);
                });


                console.log('success');
            });

    });




    /**
     * Controller for
     * @constructor
     */
    var AlgoliaInstantSearch = function($element) {

        this.$element = $element;

        this.searchOnlyApiKey = this.$element.data('search-only-api-key');

        this.appId = this.$element.data('app-id');

        this.indexName = this.$element.data('index-name');

        this.init();
    };

    AlgoliaInstantSearch.prototype.init = function() {


            var search = instantsearch({
                // Replace with your own values
                appId: this.appId,
                apiKey: this.searchOnlyApiKey, // search only API key, no ADMIN key
                indexName: this.indexName,
                urlSync: false,
                searchParameters: {
                    hitsPerPage: 10
                }
            });

        search.on('error', function(error) {
            var errorMessage = error.message.replace(/<\/?[^>]+(>|$)/g, "");
            errorMessage += " - Check to make sure the the Algolia API Settings are correct";
            this.$element.prepend(this.getError(errorMessage));
        }.bind(this));


        search.addWidget(
            instantsearch.widgets.searchBox({
                container: '#search-input',
                wrapInput: true,
                autofocus: true,
                cssClasses: {
                    root: 'form-group',
                    input: 'form-control'
                }

            })
        );

        // Add this after the previous JavaScript code
        search.addWidget(
            instantsearch.widgets.hits({
                container: '#hits',
                templates: {
                    item: document.getElementById('hit-template').innerHTML,
                    empty: "We didn't find any results for the search <em>\"{{query}}\"</em>"
                }
            })
        );


        search.addWidget(
            instantsearch.widgets.pagination({
                container: '#pagination'
            })
        );

        var s = search.start();


    };

    /**
     * Gets an html formated error message
     *
     * @param message
     * @return {string}
     */
    AlgoliaInstantSearch.prototype.getError = function(message) {
        var html = "<div class='alert alert-danger' role='alert'>"
        html += message;
        html += "</div>";
        return html;
    };

    var $contollers = $('[data-controller]');
    if ($contollers.length > 0) {
        $contollers.each(function () {
        var controllerName = $(this).data('controller') || undefined;
        if (controllerName === 'undefined') {
            console.log('ERROR: NO CONTROLLER NAME FOUND ON ELEMENT');
            // Returning non-false is the same as a continue statement in a for loop, it will skip immediately to the next iteration.
            return true;
        }

        createController(controllerName, $(this));
        });
    }



    /**
     * @param controllerName string
     */
    function createController(controllerName, $element) {

        var controller;

        switch (controllerName) {
            case 'AlgoliaInstantSearch':
                controller = AlgoliaInstantSearch;
                break;
        }

        var a = new controller($element);
    }



        $(document).on('click', '.js-panel-heading', function() {
            debugger;
            $(this).next('.js-panel-body').slideToggle( "slow", function() {
                // Animation complete.
            });
        });



});