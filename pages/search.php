<?php require __DIR__ . '/../includes/follow.php'?>


<!doctype html>
<html lang="en">
<?php

$page_description = 'Search users';
$page_name = 'Search users • Switter';
require  __DIR__ . '/../templates/header.php';

?>
<body>
    <?php require __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container w-50 bg-transparent pt-lg-5">
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search_value']) && !empty(validate_input($_POST['search_value']))): ?>
            <?php
            $search_value = validate_input($_POST['search_value']);
            $found_users = $db->get_all_users_by_username($search_value);

            // Remove current user from the array if present.
            $current_user = null;
            foreach ($found_users as $index=>$user) {
                if ($user['id'] === $_SESSION['id']) {
                    $current_user = $index;
                    break;
                }
            }
            if ($current_user !== null) unset($found_users[$current_user]);

            if (!empty($found_users)): ?>
                <form action="<?php echo PATH_PREFIX . 'search'; ?>" method="POST" id="follow_form">
                    <?php foreach ($found_users as $index=>$user): ?>
                        <div class="row mb-lg-2 bg-light mb-lg-3 py-lg-3 rounded">
                            <div class="col-lg">
                                <a href="<?php echo PATH_PREFIX . 'profile?id=' . $user['id']; ?>" class="btn btn-block px-lg-5">
                                    <div class="row">
                                        <div class="col-lg-2 px-0">
                                            <img src="<?php echo $user['profile_image_path']; ?>" alt="Profile image"
                                                 class="img-fluid rounded-circle" style="width: 75px;height: 75px;">
                                        </div>

                                        <div class="col-lg-8 pl-lg-5 text-left">
                                            <p class="text-muted mb-lg-1">@<?php echo $user['username']; ?></p>
                                            <h4 class="text-dark"><b><?php echo $user['name']; ?></b></h4>
                                        </div>
                                    </div>
                                </a>
                            </div>

                            <div class="col-lg-2 text-left pt-lg-3 pr-lg-5">
                                <?php
                                $can_follow = true;

                                foreach (get_info($ui::FOLLOWING, $_SESSION['id']) as $following_user):
                                    if (array_search($user['id'], $following_user)): ?>
                                        <input type="submit" class="btn btn-outline-dark" value="Unfollow"
                                               onclick="follow(<?php echo $user['id']; ?>, false, true)">
                                    <?php
                                        $can_follow = false;
                                        break; endif; ?>
                                <?php endforeach; ?>

                                <?php if ($can_follow): ?>
                                    <input type="submit" class="btn btn-outline-dark" value="Follow"
                                           onclick="follow(<?php echo $user['id']; ?>, true, true);">
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </form>
            <?php else: ?>
                <h1 class="display-4 pb-lg-5 text-center text-light fixed-bottom">
                    No results found <i class="fab fa-earlybirds"></i>
                </h1>
            <?php endif; ?>
        <?php else: ?>
            <h1 class="display-4 pb-lg-5 text-center text-light fixed-bottom">
                No results found <i class="fab fa-earlybirds"></i>
            </h1>
        <?php endif; ?>
    </div>
</body>
</html>
