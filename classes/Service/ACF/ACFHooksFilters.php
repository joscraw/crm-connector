<?php

namespace CRMConnector\Service\ACF;

/**
 * Class Hooks_Filters
 * @package CRMConnector\Service\ACF
 */
class ACFHooksFilters
{

    public static function admin_head()
    {
        ?>
        <script type="text/javascript">
            (function($){

                $(document).ready(function(){

                    $('.layout').addClass('-collapsed');
                    $('.acf-postbox').addClass('closed');

                });

            })(jQuery);
        </script>
        <?php
    }

}