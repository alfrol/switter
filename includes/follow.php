<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id_to_follow'], $_POST['to_follow'])) {
    $user_id_to_follow = validate_input($_POST['user_id_to_follow']);
    $to_follow = validate_input($_POST['to_follow']);

    if ($to_follow === 'true') {
        $db->follow_user($_SESSION['id'], $user_id_to_follow);
    } else {
        $db->unfollow_user($_SESSION['id'], $user_id_to_follow);
    }
}
