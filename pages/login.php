<?php

define('PASSWORD_ERROR', 'Incorrect password!');
define('USERNAME_ERROR', 'Incorrect username!');

$username_error_message = $password_error_message = "";
$username_correct = $password_correct = true;

if (isset($_POST['username'], $_POST['password'])) {
    $username = validate_input($_POST['username']);
    $password = validate_input($_POST['password']);

    $username_correct = !empty($username);
    $password_correct = !empty($password);

    if ($username_correct && $password_correct) {
        global $db;

        $credentials = $db->perform_login_query($username)[0];
        if (!$credentials) {
            $username_correct = false;
        } else {
            login($credentials, $password);
        }
    }
}

/**
 * Try to log the user in.
 *
 * If the password hash obtained from the database
 * coincides with the provided password hash then
 * log the user in by creating a new session.
 *
 * If the password was incorrect, inform the user.
 *
 * @param array $credentials - Array with credentials obtained from the db.
 * @param string $provided_password - Provided password.
 */
function login(array $credentials, string $provided_password): void {
    global $password_correct;

    if (password_verify($provided_password, $credentials['password'])) {
        session_regenerate_id();
        $_SESSION['loggedin'] = true;
        $_SESSION['id'] = $credentials['id'];
        $_SESSION['name'] = $credentials['username'];
        header("Location: home");
        die();
    }
    $password_correct = false;
}

?>


<!doctype html>
<html lang="en">
    <?php

    $page_description = 'Switter login page';
    $page_name = 'Login • Switter';

    require __DIR__ . '/../templates/header.php';

    ?>
<body style="background-position-y: 0;">
    <div class="container-fluid sticky-top">
        <div class="row">
            <div class="col-lg text-center text-dark" style="background-color: #dcac7b;">
                <h1 class="display-3">
                    Welcome to <span class="text-light">Switter<img src="<?php echo PATH_PREFIX; ?>Switter.png" alt="Logo" class="pt-lg-4"></span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container">
        <form class="pt-lg-5 text-center" method="POST" action="<?php echo htmlspecialchars(PATH_PREFIX . 'login'); ?>"
              name="login_form" autocomplete="off" novalidate style="margin-top: 150px;">
            <?php if (isset($_SESSION['registered'])) : ?>
                <div class="row">
                    <div class="alert alert-success alert-dismissible fade show col-lg-4 mx-auto" role="alert">
                        <strong>Success!</strong> You have been registered!
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
            <?php unset($_SESSION['registered']); endif; ?>

            <div class="form-group row">
                <div class="input-group col-lg-4 mx-auto">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user-alt" id="usernameIcon"></i></span>
                    </div>
                    <input type="text" class="form-control <?php echo get_input_validity($username_correct); ?>"
                           id="username-field" name="username" placeholder="Username"
                           aria-label="Username" aria-describedby="usernameIcon">
                </div>

                <?php if (!$username_correct) : ?>
                    <div class="invalid-feedback" style="display: block;color: red;">
                        <?php echo USERNAME_ERROR; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group row">
                <div class="input-group col-lg-4 mx-auto">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock" id="passwordIcon"></i></span>
                    </div>
                    <input type="password" class="form-control <?php echo get_input_validity($password_correct); ?>"
                           id="password-field" name="password" placeholder="Password"
                           aria-label="Password" aria-describedby="passwordIcon">
                </div>

                <?php if (!$password_correct) : ?>
                    <div class="invalid-feedback" style="display: block;color: red;">
                        <?php echo PASSWORD_ERROR; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group row">
                <div class="col-lg-2 mx-auto">
                    <input type="submit" value="Login" class="btn btn-block btn-outline-light btn-lg" style="border-width: 2px;">
                </div>
            </div>
        </form>

        <div class="text-center">
            <p class="text-white">Don't have an account yet? <a href="<?php echo PATH_PREFIX . 'register'?>" class="text-warning">Register</a></p>
        </div>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
