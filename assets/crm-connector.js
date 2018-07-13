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
                html += "<div class='alert alert-danger" + (temp === false ? "" : " temp") + " role='alert'>";
                $.each(response.errors, function(index, message) {
                    html += "<p>"+message+"</p>";
                });
                html += "</div>";
                break;

            case 'success':
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
        debugger;
        var $parent;
        // display the loading spinner on th parent container if nothing is is defined
        $parent = parent ? $(parent) : $('.js-container');
        $parent.prepend("<div class='loader'></div>");
    };

    /**
     * @param parent
     */
    window.ajaxRemoveLoadingSpinnerFromContainer = function(parent) {
        debugger;
        var $parent;
        // attempt to remove the loading spinner from the parent container if nothing is is defined
        $parent = parent ? $(parent) : $('.js-container');
        $parent.find('.loader').remove();
    };

})(jQuery);