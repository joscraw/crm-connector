/**
 * Define global functions attached to the window object
 * that are shared by all views
 */
(function($) {

    /**
     * This method renders notices/errors from ajax response
     *
     * @param response the response from the server
     * @param parent the element to prepend the error to
     * @param temp
     */
    window.ajaxRenderNoticesAndErrors = function(response, parent, temp) {
        debugger;
        var $parent;
        // display the erorr message on the parent container if nothing is is defined
        $parent = parent ? $(parent) : $('.js-container');
        temp = temp || false;
        var message;
        var html = "";
        $parent.find('.alert').remove();

        switch(response.type)
        {
            case 'error':
                if(response.errors.length === 0)
                    break;
                html += "<div class='alert alert-danger" + (temp === false ? "" : " temp") + " role='alert'>";
                $.each(response.errors, function(index, message) {
                    html += "<p>"+message+"</p>";
                });
                html += "</div>";
                break;

            case 'success':
                if(response.notices.length === 0)
                    break;
                html += "<div class='alert alert-success" + (temp === false ? "" : " temp") + " role='alert'>";
                $.each(response.notices, function(index, message) {
                    html += "<p>"+message+"</p>";
                });
                html += "</div>";
                break;
        }

        $parent.prepend(html);

    };

    /**
     * @param parent
     */
    window.ajaxAttachLoadingSpinnerToContainer = function(parent) {
        var $parent;
        // display the loading spinner on th parent container if nothing is is defined
        $parent = parent ? $(parent) : $('.js-container');
        $parent.prepend("<div class='loader'></div>");
    };

    /**
     * @param parent
     */
    window.ajaxRemoveLoadingSpinnerFromContainer = function(parent) {
        var $parent;
        // attempt to remove the loading spinner from the parent container if nothing is is defined
        $parent = parent ? $(parent) : $('.js-container');
        $parent.find('.loader').remove();
    };

    /**
     * Add ajax loading dots
     * @param parent
     */
    window.ajaxAttachLoadingDots = function(parent) {
        var $parent = $(parent);
        $parent.append("<span style='position:absolute' class='wait'></span>")

        window.dots = window.setInterval( function() {
            var wait = $parent.find('.wait').get(0);
            if ( wait.innerHTML.length > 3 )
                wait.innerHTML = "";
            else
                wait.innerHTML += ".";
        }, 100);
    };

    /**
     * Remove ajax loading dots
     */
    window.ajaxRemoveLoadingDots = function(parent) {
        var $parent = $(parent);
        clearInterval(window.dots);
        $parent.find('.wait').remove();
    };



    $(document).on('click', '.js-show-import-modal-button', function(event) {
        debugger;
        var $modal = $('.js-import-modal');
        $modal.modal('show');
        this.chapterId = $(event.target).data('chapter');

    }.bind(this));

    $(document).on('change', '.js-student-file', function(event) {

        debugger;
        var $form = $('.js-import-form'),
            e = event.target;

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
        formData.append('chapter_id', this.chapterId);

        // perform ajax request
        jQuery.ajax({
            url : url,
            data : formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            beforeSend: function() {
                debugger;
                this.ajaxAttachLoadingSpinnerToContainer(".js-import-modal .js-modal-body");
            }.bind(this)
        }).fail(function(r,status,jqXHR) {
            console.log('failed');
        }.bind(this))
            .done(function(r,status,jqXHR) {


                // . js-column-name-container
                debugger;
                this.ajaxRemoveLoadingSpinnerFromContainer(".js-import-modal .js-modal-body");
                $form.find('.js-column-name-container').html("");
                var response = JSON.parse(r);
                $(response['columns']).each(function(index, column) {
                    var prototype =  $form.data('prototype-import-mapping-form');
                    prototype = prototype.replace(/column_index/g, index);
                    prototype = prototype.replace(/\{\{file_column_name\}\}/g, column);

                    var mapping = response['student_import_file_mapping'];
                    mapping = mapping.map(function(map){
                        return "<option value='"+map+"'>"+map+"</option>";
                    });
                    mapping.unshift('<option value="" disabled selected>Select your option</option>');
                    prototype = prototype.replace(/\{\{database_column_names\}\}/g, mapping.join(''));

                    $form.find('.js-column-name-container').append(prototype);

                });

                debugger;
                this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

            }.bind(this));

    }.bind(this));

    $(document).on('click', '.js-import-students-button', function(event) {

        debugger;
        var selected_file_columns = [];
        $selects = $('.js-database-column-name');
        $selects.each(function(index, select){
            $select = $(select);
            if($select.val())
            {
                selected_file_columns.push(index);
            }
        });

        debugger;
        event.preventDefault();

        debugger;
        var $form = $('.js-import-form'),
            file = $form.find('.js-student-file').get(0).files[0],
            nonce = $(event.target).attr("data-nonce"),
            url = $form.find('.js-student-file').attr("data-url");

        var formData = new FormData($form.get(0));
        formData.append('nonce', nonce);
        formData.append('action', 'crmc_import_contacts');
        formData.append('selected_file_columns', JSON.stringify(selected_file_columns));

        // perform ajax request
        jQuery.ajax({
            url : url,
            data : formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST', // For jQuery < 1.9
            beforeSend: function() {
                debugger;
                this.ajaxAttachLoadingSpinnerToContainer(".js-import-modal .js-modal-body");
                this.ajaxAttachLoadingDots(".js-import-students-button");
            }.bind(this)
        }).fail(function(r,status,jqXHR) {
            console.log('failed');
        })
            .done(function(r,status,jqXHR) {

                $('.js-import-modal').animate({
                    scrollTop: $('.js-import-modal').offset().top
                }, 200);

                var response = JSON.parse(r);

                this.ajaxRemoveLoadingSpinnerFromContainer(".js-import-modal .js-modal-body");
                this.ajaxRemoveLoadingDots(".js-import-students-button");
                this.ajaxRenderNoticesAndErrors(response, ".js-import-modal .js-modal-body");

            }.bind(this));

    }.bind(this));

    $(document).on('click', '.js-show-rollback-modal-button', function(event) {
        var $modal = $('.js-rollback-modal');
        $modal.modal('show');
        this.chapterId = $(event.target).data('chapter');
        debugger;

    }.bind(this));

})(jQuery);