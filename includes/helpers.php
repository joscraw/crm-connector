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


function renderGenericErrorMessage($errors)
{
    if (!is_array($errors)) return;
    $html = "";
    $html .= "<div class='alert alert-danger' role='alert'>";
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
function renderSuccessMessage($successMessage) {
    if (!$successMessage) return;
    $html = "<div class='alert alert-success' role='alert'>$successMessage</div>";
    return $html;
}