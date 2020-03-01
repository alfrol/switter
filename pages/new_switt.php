<?php

define('SWITT_EMPTY', 'Nothing to post here!');
define('SWITT_TOO_LONG', 'Your Switt is too long!');

$switt_correct = true;
$switt_error_message = '';

if (isset($_POST['new_switt'])) {

    $switt_text = validate_input($_POST['new_switt']);
    $switt_correct = !empty($switt_text) && strlen($switt_text) <= 450;

    if (empty($switt_text)) {
        $switt_error_message =  SWITT_EMPTY;
    } else if (strlen($switt_text) > 450) {
        $switt_error_message = SWITT_TOO_LONG;
    }

    if ($switt_correct) {
        global $db;
        $db->create_new_switt($_SESSION['name'], $_SESSION['id'], $switt_text);
        header('Location: profile?id=' . $_SESSION['id']);
    }
}

?>


<!doctype html>
<html lang="en" style="height: 100%;">
    <?php

    $page_description = 'Make new switt here';
    $page_name = 'New Switt • Switter';

    require __DIR__ . '/../templates/header.php';

    ?>
<body class="bg-light h-100">
    <?php require __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container bg-white h-100 w-75">
        <form action="<?php echo htmlspecialchars(PATH_PREFIX . 'new-switt'); ?>" method="POST" autocomplete="off" novalidate>
            <div class="form-group row h-100">
                <div class="col-lg mx-auto text-center">
                    <label for="new-switt" class="display-4 mt-lg-3 mb-lg-5 control-label">What is on your mind?</label>
                    <textarea class="form-control text-center border-dark mx-auto <?php echo get_input_validity($switt_correct); ?>"
                              name="new_switt" id="new-switt" cols="30" rows="8"
                              style="width: 80%;font-size: 20px;resize: none;overflow: hidden;border-width: 2px;"
                              aria-describedby="switt-help" maxlength="450" required></textarea>

                    <small id="new-switt" class="form-text text-muted">
                        Switt must be 1-450 characters long.
                    </small>

                    <?php if (!$switt_correct) : ?>
                        <div class="invalid-feedback">
                            <?php echo SWITT_EMPTY; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-row justify-content-center">
                <input type="submit" value="Post Switt" class="btn btn-outline-dark btn-lg my-lg-5" style="width: 15%;border-width: 2px;">
            </div>
        </form>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
