<?php

$users_followed_by = get_info($ui::FOLLOWING, $_SESSION['id']);
$switts = array();

foreach ($users_followed_by as $user_id) {
    $user_switts = $db->get_all_user_switts($user_id['following']);

    foreach ($user_switts as $switt) {
        array_push($switts, $switt);
    }
}

usort($switts, 'date_sort');
$sorted_switts = array_reverse($switts);

/**
 * Sort array of switts by their post date.
 *
 * @param $a - First switt to compare with.
 * @param $b - Second switt to compare to.
 * @return false|int - Which switt was posted earlier.
 */
function date_sort($a, $b) {
    return strtotime($a['post_date']) - strtotime($b['post_date']);
}

?>


<!doctype html>
<html lang="en">
<?php

$page_description = 'Switter homepage';
$page_name = 'Home • Switter';

require __DIR__ . '/../templates/header.php';

?>
<body>
    <?php require __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container w-50 bg-transparent pt-lg-5">
        <?php if (empty($users_followed_by)): ?>
            <h1 class="display-4 pb-lg-5 text-center text-light fixed-bottom">
                You are not following anyone yet <i class="fab fa-earlybirds"></i>
            </h1>
        <?php else: ?>
            <?php foreach ($sorted_switts as $index=>$switt): ?>
                <div class="media bg-light mb-lg-3 py-lg-3 px-lg-2 rounded">
                    <a href="<?php echo PATH_PREFIX . 'profile?id=' . $switt['author_id']; ?>"
                       class="btn mr-lg-4">
                        <img src="<?php echo get_info($ui::PROFILE_IMAGE, $switt['author_id']); ?>"
                             alt="Profile image" class="img-fluid rounded-circle" style="width: 60px;height: 60px;">
                    </a>
                    <div class="media-body">
                        <p>
                            <b>@<?php echo get_info($ui::USERNAME, $switt['author_id']);  ?></b>
                            <small class="text-muted"> on <?php echo $switt['post_date']; ?></small>
                        </p>

                        <p><?php echo $switt['content']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
