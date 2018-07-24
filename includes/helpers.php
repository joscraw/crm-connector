<?php


/**
 * Loop through errors array created by
 * setTransient('errors' $errors)
 *
 * @param $errors array
 * @return string
 */
function renderErrors($errors) {
    if(!is_array($errors)) return;
    $html = "";
    foreach($errors as $error) {
        $html .= "<p class='form-error'>$error</p>";
    }
    return $html;
}


function renderGenericErrorMessage($errors, $temp = false)
{
    if (!is_array($errors)) return;
    $html = "";
    $html .= "<div class='alert alert-danger" . ($temp === false ? '' : ' temp'). " role='alert'>";
    foreach ($errors as $error) {
        $html .= "<p>$error</p>";
    }
    $html .= "</div>";
    return $html;
}

/**
 * Loop through errors array created by
 * setTransient('errors' $errors)
 *
 * @param $successMessage
 * @return string
 */
function renderSuccessMessage($successMessage)
{
    if (!$successMessage) return;
    $html = "<div class='alert alert-success' role='alert'>$successMessage</div>";
    return $html;
}


/**
 * Delete all transients so you don't get any message overlaps
 */
function deleteTransients()
{
    delete_transient('errors');
    delete_transient('successMessage');
}

function redirectToPage($params = array(), $tab = null)
{
    $query = http_build_query($params);
    $path = "admin.php?$query";
    $url = admin_url($path) . (($tab === null) ? '' : "#$tab");
    wp_redirect( $url );
    exit();
}


function get_page_url($params = array(), $tab = null)
{
    $query = http_build_query($params);
    $path = "admin.php?$query";
    return admin_url($path) . (($tab === null) ? '' : "#$tab");
}

/**
 * @param $total_items int This is the total number of items in the result set
 * @param $page int This is the name of the query param "page"
 * @param $tab int This is the name of the query param "tab"
 * @param string $hash This is the hash used to render the side nav tabs
 * @param string $offset_name This needs to be unique per nav-hash because there can be more then one pagination per hash being loaded on each request
 * @param string $count_name
 * @param int $items_per_page This is how many items to show per page
 */
function render_pagination($total_items, $page, $tab, $hash = "#", $offset_name = 'offset', $count_name = 'count', $items_per_page = 10)
{
    $num_of_pages = ceil($total_items / $items_per_page);

    ob_start();
    ?>
    <nav aria-label="Page navigation">
    <ul class="pagination">



        <li class="<?php echo ($num_of_pages === 1 || !isset($_GET[$offset_name]) || isset($_GET[$offset_name]) && (int)$_GET[$offset_name] <= 0) ? 'disabled' : '';?>">

            <?php
            $starting_offset = 0;
            if(isset($_GET[$offset_name]) && $num_of_pages !== 1)
            {
                $starting_offset = ((int) $_GET[$offset_name] - $items_per_page);
            }
            ?>

            <a href="<?php echo get_page_url(array('page' => $page, 'tab' => $tab, $count_name => $items_per_page, $offset_name => $starting_offset), $hash); ?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>

        <?php
        for($i = 0; $i < $num_of_pages; $i++):
            $template_offset = $items_per_page * $i;
        ?>
            <li class="<?php echo ($num_of_pages === 1 || (!isset($_GET[$offset_name]) && $i == 0) || (isset($_GET[$offset_name]) && (int)$_GET[$offset_name] === $template_offset)) ? 'active' : '';?>">
                <a href="<?php echo get_page_url(array('page' => $page, 'tab' => $tab, $count_name => $items_per_page, $offset_name => $template_offset), $hash); ?>"><?php echo ($i + 1); ?></a>
            </li>
        <?php
        endfor;
        ?>

        <li class="<?php echo ($num_of_pages == 1 || isset($_GET[$offset_name]) && ((int)$_GET[$offset_name] + $items_per_page) >= $total_items) ? 'disabled' : '';?>">

            <?php
            $closing_offset = 0;
            if($num_of_pages !== 1)
            {
                if(!isset($_GET[$offset_name]))
                    $closing_offset = $items_per_page;

                if(isset($_GET[$offset_name]))
                    $closing_offset = ((int)$_GET[$offset_name] + $items_per_page);
            }
            ?>

            <a href="<?php echo get_page_url(array('page' => $page, 'tab' => $tab, $count_name => $items_per_page, $offset_name => $closing_offset), $hash); ?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>

<?php
    $html = ob_get_clean();

    echo $html;
}

