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


/**
 * @param $pill
 * @return string
 */
function renderSubMenuURL($pill)
{
    $path = "admin.php?page={$_GET['page']}&pill=$pill";
    return admin_url($path);
}


/**
 * @param $pill
 * @return string
 */
function renderMenuURL($page)
{
    $path = "admin.php?page=$page";
    return admin_url($path);
}



function redirectToPage($params = array(), $tab = null)
{
    $query = http_build_query($params);
    $path = "admin.php?$query";
    $url = admin_url($path) . (($tab === null) ? '' : "#$tab");
    wp_redirect( $url );
    exit();
}

