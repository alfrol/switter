<?php

/**
 * Validate input data obtained from the form.
 *
 * @param string $data - Data to be validated.
 * @return string - Validated data.
 */
function validate_input(string $data) {
    $data_trim = trim($data);
    $data_slashes = stripcslashes($data_trim);
    return htmlspecialchars($data_slashes);
}

/**
 * Return random file obtained from the specified path.
 *
 * @return string - Random file path.
 */
function get_random_file() {
    $pictures = array_diff(scandir(__DIR__ . '/../uploads/default_icons'), array('.', '..'));
    return PATH_PREFIX . 'uploads/default_icons/' . $pictures[array_rand($pictures)];
}

/**
 * Define whether the input is valid.
 *
 * @param bool $no_error - true if valid, false otherwise.
 * @return string - According HTML classname.
 */
function get_input_validity(bool $no_error) {
    if ($no_error) {
        return '';
    }
    return 'is-invalid';
}

/**
 * Get the id of the user whose profile is currently opened.
 * Id is obtained from the query parameter and
 * saved to the _SESSION superglobal variable.
 *
 * @return string - The id of the user.
 */
function get_observable_user_id() {
    return $_SESSION['observable_user_id'];
}

/**
 * Get the id of the user whose session is currently opened.
 *
 * @return string - The id of the user.
 */
function get_user_id() {
    return $_SESSION['id'];
}



/**
 * Get the information about the user from the db.
 *
 * @param int $type - Type of information to query from the db.
 * @param string id - Optional user id.
 * @return string|array - Result.
 * @see UserInfo for information constants.
 */
function get_info(int $type, string $id = '') {
    global $db;
    global $ui;

    if (empty($id)) $id = get_observable_user_id();

    switch ($type) {
        case $ui::NAME:
            return $db->retrieve_user_information($id, $db::NAME);
        case $ui::USERNAME:
            return $db->retrieve_user_information($id, $db::USERNAME);
        case $ui::AGE:
            return $db->retrieve_user_information($id, $db::AGE);
        case $ui::BIRTH_DATE:
            return $db->retrieve_user_information($id, $db::BIRTH_DATE);
        case $ui::BIO:
            return $db->retrieve_user_information($id, $db::BIO);
        case $ui::PROFILE_IMAGE:
            return $db->retrieve_user_information($id, $db::PROFILE_IMAGE);
        case $ui::EMAIL:
            return $db->retrieve_user_information($id, $db::EMAIL);
        case $ui::FOLLOWERS:
            return $db->get_user_followers($id);
        case $ui::FOLLOWING:
            return $db->get_following_users($id);
        default:
            return '';
    }
}

function update_info(int $type, $new_value) {
    global $db;
    global $ui;
    switch ($type) {
        case $ui::NAME:
            $db->update_user_information(get_user_id(), $db::NAME, $new_value);
            break;
        case $ui::USERNAME:
            $db->update_user_information(get_user_id(), $db::USERNAME, $new_value);
            break;
        case $ui::AGE:
            $db->update_user_information(get_user_id(), $db::AGE, $new_value);
            break;
        case $ui::BIRTH_DATE:
            $db->update_user_information(get_user_id(), $db::BIRTH_DATE, $new_value);
            break;
        case $ui::BIO:
            $db->update_user_information(get_user_id(), $db::BIO, $new_value);
            break;
        case $ui::PROFILE_IMAGE:
            $db->update_user_information(get_user_id(), $db::PROFILE_IMAGE, $new_value);
            break;
        case $ui::EMAIL:
            $db->update_user_information(get_user_id(), $db::EMAIL, $new_value);
            break;
        case $ui::PASSWORD:
            $db->update_user_information(get_user_id(), $db::PASSWORD, $new_value);
    }
}
