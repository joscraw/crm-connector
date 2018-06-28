<div class="row">
    <div class="col-md-12">
        <ul class="nav nav-tabs">
            <?php
            $slug = 'import';
            $path = "admin.php?page=$slug";
            $url = admin_url($path);
            ?>
            <li class="nav-item <?php echo ($_GET['page'] === $slug ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo $url; ?>">Import Contacts</a>
            </li>
            <?php
            $slug = 'contacts';
            $path = "admin.php?page=$slug";
            $url = admin_url($path);
            ?>
            <li class="nav-item <?php echo ($_GET['page'] === $slug ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo $url; ?>">Contacts</a>
            </li>
            <?php
            $slug = 'chapters';
            $path = "admin.php?page=$slug&pill=list";
            $url = admin_url($path);
            ?>
            <li class="nav-item <?php echo ($_GET['page'] === $slug ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo $url; ?>">Chapters</a>
            </li>
            <?php
            $slug = 'advanced-settings';
            $path = "admin.php?page=$slug&pill=hubspot";
            $url = admin_url($path);
            ?>
            <li class="nav-item <?php echo ($_GET['page'] === $slug ? 'active' : ''); ?>">
                <a class="nav-link" href="<?php echo $url; ?>">Advanced Settings</a>
            </li>
        </ul>
    </div>
</div>