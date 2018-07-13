

<div class="container js-container">

    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-tabs">
                <li class="nav-item <?php echo ($_GET['tab'] === 'contacts' ? 'active' : ''); ?>">
                    <a class="nav-link" href="<?php echo admin_url() . "admin.php?page=crmc_settings&tab=contacts"; ?>">Contacts</a>
                </li>
                <li class="nav-item <?php echo ($_GET['tab'] === 'chapters' ? 'active' : ''); ?>">
                    <a class="nav-link" href="<?php echo admin_url() . "admin.php?page=crmc_settings&tab=chapters"; ?>">Chapters</a>
                </li>
                <li class="nav-item <?php echo ($_GET['tab'] === 'advanced_settings' ? 'active' : ''); ?>">
                    <a class="nav-link" href="<?php echo admin_url() . "admin.php?page=crmc_settings&tab=advanced_settings"; ?>">Advanced Settings</a>
                </li>
                <li class="nav-item <?php echo ($_GET['tab'] === 'invitation_settings' ? 'active' : ''); ?>">
                    <a class="nav-link" href="<?php echo admin_url() . "admin.php?page=crmc_settings&tab=invitation_settings"; ?>">Invitation Settings</a>
                </li>
            </ul>
        </div>
    </div>

    <?php
    $successMessage = get_transient('successMessage');
    echo renderSuccessMessage($successMessage);
    echo renderGenericErrorMessage(get_transient('errors')['main']);
    ?>


<?php
echo $tab_contents;
?>

</div>

<script>

    (function($){

        // change the tab based upon the routing in the URL on page load
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });

        if($('.alert.temp').length) {
            setTimeout(function() {
                $('.alert.temp').remove();
            }, 3000);
        }

    })(jQuery);

</script>