<?php

require __DIR__ . '/../includes/follow.php';

define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

/**
 * Try to upload the new users profile image.
 * Check whether the file is a real image, it does not already exist,
 * its size is in allowed range and its extension is in allowed extensions list.
 * If one requirement is not fulfilled then the image will not be uploaded.
 */
if (isset($_POST['profile_image_submit_check'])) {
    global $db;

    $base_dir = __DIR__ . '/../uploads/user_uploads/';
    $image_name = basename($_FILES['new_profile_image']['name']);
    $image_path = $base_dir . $image_name;
    $image_file_type = strtolower(pathinfo($image_path, PATHINFO_EXTENSION));

    $is_real_image = getimagesize($_FILES['new_profile_image']['tmp_name']);
    $does_not_exist = !file_exists($image_path);
    $size_ok = $_FILES['new_profile_image']['size'] <= 5000000;
    $allowed_extension = in_array($image_file_type, ALLOWED_EXTENSIONS);

    $can_upload = $is_real_image !== false && $does_not_exist && $size_ok && $allowed_extension;
    if ($can_upload) {
        move_uploaded_file($_FILES['new_profile_image']['tmp_name'], $image_path);
        $db->save_profile_image(get_observable_user_id(),'./uploads/user_uploads/' . $image_name);
    } else if (!$does_not_exist) {
        $db->save_profile_image(get_observable_user_id(),'./uploads/user_uploads/' . $image_name);
    }
}

?>


<!doctype html>
<html lang="en" style="height: 100%;">
<?php

$page_description =  $_SESSION['name'] . "'s profile";
$page_name = get_info($ui::NAME) . ' • Switter';
require __DIR__ . '/../templates/header.php';

?>
<body class="bg-light" style="height: 100%;">
    <?php require __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container bg-transparent">
        <div class="row text-center h-100">
            <div class="col-lg-4 pt-5 bg-light rounded-bottom">
                <?php if (get_user_id() == get_observable_user_id()): ?>
                    <form action="<?php echo htmlspecialchars(PATH_PREFIX . 'profile?id=' . $_SESSION['id']); ?>" method="POST"
                          enctype="multipart/form-data" name="profile_image_upload">
                        <div class="form-row pl-5 ml-4">
                            <label for="profile_image_upload" class="btn btn-link p-0 text-dark"
                                   data-toggle="tooltip" data-placement="right" title="Upload new profile image">
                                <input type="text" name="profile_image_submit_check" hidden>
                                <input id="profile_image_upload" type="file" class="d-none" name="new_profile_image"
                                       onchange="document.forms['profile_image_upload'].submit()">
                                <i class="fas fa-camera" style="font-size: 20px;"></i>
                            </label>
                        </div>
                    </form>
                <?php endif; ?>

                <div class="justify-content-center">
                    <img src="<?php echo get_info($ui::PROFILE_IMAGE); ?>" alt="Profile image"
                         class="img-fluid rounded-circle" style="width: 175px;height: 175px;">
                </div>

                <?php if ($_SESSION['id'] == $_SESSION['observable_user_id']): ?>
                    <div class="row pl-5 ml-4">
                        <a href="<?php echo PATH_PREFIX . 'profile-info'?>" data-toggle="tooltip" data-placement="right"
                           title="Change profile info" class="text-dark">
                            <i class="fas fa-pen" style="font-size: 20px;"></i>
                        </a>
                    </div>
                <?php endif; ?>

                <h3 class="mt-lg-3 mb-1"><?php echo get_info($ui::NAME); ?></h3>
                <p class="text-muted">@<?php echo get_info($ui::USERNAME); ?></p>

                <?php if ($_SESSION['id'] != $_SESSION['observable_user_id']): ?>
                    <form action="<?php echo PATH_PREFIX . 'profile?id=' . $_SESSION['observable_user_id']; ?>"
                          method="POST" id="follow_form" class="my-lg-3">
                        <?php
                        $can_follow = true;
                        $observable_user_id = $_SESSION['observable_user_id'];

                        foreach (get_info($ui::FOLLOWING, $_SESSION['id']) as $following_user):
                            if (array_search($observable_user_id, $following_user)): ?>
                                <input type="submit" class="btn btn-outline-dark" value="Unfollow"
                                       onclick="follow(<?php echo $observable_user_id; ?>, false, false);">
                                <?php
                                $can_follow = false;
                                break; endif; ?>
                        <?php endforeach; ?>

                        <?php if ($can_follow): ?>
                            <input type="submit" class="btn btn-outline-dark" value="Follow"
                                   onclick="follow(<?php echo $observable_user_id; ?>, true, false);">
                        <?php endif; ?>
                    </form>
                <?php endif; ?>

                <?php if (!empty(get_info($ui::BIRTH_DATE))) : ?>
                    <div class="row justify-content-center text-muted mb-lg-2">
                        <i class="fas fa-birthday-cake mr-lg-2"></i> Born on
                    </div>
                    <p><?php echo get_info($ui::BIRTH_DATE); ?></p>
                    <hr class="border border-dark">
                <?php endif; ?>

                <?php if (!empty(get_info($ui::BIRTH_DATE))) : ?>
                    <p class="text-muted mb-lg-2">Age</p>
                    <p><?php echo get_info($ui::AGE); ?></p>
                    <hr class="border border-dark">
                <?php endif; ?>

                <?php if (!empty(validate_input(get_info($ui::BIO)))) : ?>
                    <div class="row justify-content-center text-muted mb-lg-2">
                        <i class="fas fa-book mr-lg-2"></i> Bio
                    </div>
                    <p style="word-wrap: break-word"><?php echo get_info($ui::BIO); ?></p>
                <?php endif; ?>
            </div>

            <div class="col-lg-8 border-left">
                <div class="nav nav-pills justify-content-center bg-light mb-lg-5 py-lg-3 rounded-bottom">
                    <a href="#switts" class="nav-link active" data-toggle="pill">Switts</a>

                    <a href="#switts-and-replies" class="nav-link" data-toggle="pill">Switts and Replies</a>

                    <a href="#photos-and-videos" class="nav-link" data-toggle="pill">Photos and Videos</a>
                </div>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="switts">
                        <!-- Print out the switts. -->
                        <?php
                        $switts = array_reverse($db->get_all_user_switts($_SESSION['observable_user_id']));

                        if (empty($switts)): ?>
                            <h1 class="display-4 text-center text-dark bg-light py-lg-3 rounded">No switts yet</h1>
                        <?php else: for ($i = 0; $i < count($switts); $i++): ?>
                            <div class="media bg-light mb-lg-3 py-lg-3 px-lg-2 rounded">
                                <img src="<?php echo get_info($ui::PROFILE_IMAGE); ?>" alt="Profile image"
                                     class="rounded-circle ml-lg-3 mr-lg-4" style="width: 50px;height: 50px;">
                                <div class="media-body text-left">
                                    <p>
                                        <b>@<?php echo $switts[$i]['author']; ?></b>
                                        <small class="text-muted"> on <?php echo $switts[$i]['post_date']; ?></small>
                                    </p>

                                    <p><?php echo $switts[$i]['content']; ?></p>
                                </div>
                            </div>
                        <?php endfor; endif; ?>
                    </div>

                    <div class="tab-pane fade" id="switts-and-replies">

                    </div>

                    <div class="tab-pane fade" id="photos-and-videos"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
