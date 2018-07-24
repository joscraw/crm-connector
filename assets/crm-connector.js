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

})(jQuery);