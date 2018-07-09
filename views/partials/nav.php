<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <li class="nav-item <?php echo ($_GET['page'] === 'contacts' ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo renderMenuURL('contacts') ?>">Contacts</a>
            </li>
            <li class="nav-item <?php echo ($_GET['page'] === 'chapters' ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo renderMenuURL('chapters'); ?>">Chapters</a>
            </li>
            <li class="nav-item <?php echo ($_GET['page'] === 'advanced-settings' ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo renderMenuURL('advanced-settings'); ?>">Advanced Settings</a>
            </li>
        </ul>
    </div>
</div>