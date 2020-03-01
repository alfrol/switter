<?php

$name_error_message = $username_error_message = $email_error_message = $password_error_message = $password_confirmation_error_message = "";
$name_correct = $username_correct = $email_correct = $password_correct = $password_verified = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    global $db;

    $name = validate_input($_POST['name']);
    $username = validate_input($_POST['username']);
    $email = validate_input($_POST['email']);
    $password = validate_input($_POST['password']);
    $hash = password_hash($password,PASSWORD_BCRYPT, ['cost' => 12]);
    $password_confirmation = validate_input($_POST['password_confirmation']);

    if (empty($name)) {
        $name_error_message = "Name is required!";
        $name_correct = false;
    }

    if (empty($username)) {
        $username_error_message = "Username is required!";
        $username_correct = false;
    } else if ($db->exists($username, 'username')) {
        $username_error_message = "This username already exists!";
        $username_correct = false;
    }

    if (empty($email)) {
        $email_error_message = "Email is required!";
        $email_correct = false;
    } else if ($db->exists($email, 'email')) {
        $email_error_message = "This email is already registered!";
        $email_correct = false;
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $email_error_message = "Please provide the correct email!";
        $email_correct = false;
    }

    if (empty($password)) {
        $password_error_message = "Password is required!";
        $password_correct = false;
    } else if (strlen($password) < 8) {
        $password_error_message = "Password is too short!";
        $password_correct = false;
    } else if (strlen($password) > 25) {
        $password_error_message = "Password is too long!";
        $password_correct = false;
    } else if (!ctype_upper($password[0])) {
        $password_error_message = "Password must start with uppercase letter!";
        $password_correct = false;
    } else if (!preg_match('/\d+/', $password)) {
        $password_error_message = "Password must contain at least one digit!";
        $password_correct = false;
    } else if (!preg_match('/_/', $password)) {
        $password_error_message = "Password must contain at least one underscore (_)!";
        $password_correct = false;
    }

    if (empty($password_confirmation)) {
        $password_confirmation_error_message = "Password confirmation is required!";
        $password_verified = false;
    } else if (!password_verify($password_confirmation, $hash)) {
        $password_confirmation_error_message = "Passwords don't match!";
        $password_verified = false;
    }

    if ($name_correct === true && $username_correct && $email_correct && $password_correct && $password_verified) {
        $db->perform_register_query($name, $username, $email, $hash);
        $_SESSION['registered'] = true;
        header('Location: login');
    }
}

?>


<!doctype html>
<html lang="en">
    <?php

    $page_description = 'Switter register page';
    $page_name = 'Register • Switter';

    require __DIR__ . '/../templates/header.php';

    ?>
<body style="background-position-y: 0;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg mb-lg-5 text-center text-dark" style="background-color: #dcac7b;">
                <h1 class="display-3">
                    Register on <span class="text-light">Switter<img src="<?php echo PATH_PREFIX; ?>Switter.png" alt="Logo" class="pt-lg-4"></span>
                </h1>
            </div>
        </div>
    </div>

    <div class="container">
        <form class="text-center" method="POST" action="<?php echo htmlspecialchars(PATH_PREFIX . 'register'); ?>"
              name="registration_form" autocomplete="off" novalidate>
            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label for="name" class="control-label text-light"><b>Name</b></label>
                    <input class="form-control text-center <?php echo get_input_validity($name_correct); ?>"
                           type="text" name="name" id="name" placeholder="Captain America"
                           value="<?php if (!empty($name)) echo $name; ?>"
                           required>

                    <?php if (!$name_correct) : ?>
                        <div class="invalid-feedback" style="color: red;">
                            <?php echo $name_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label class="control-label text-light" for="username"><b>Username</b></label>
                    <input class="form-control text-center <?php echo get_input_validity($username_correct); ?>"
                           type="text" name="username" id="username" placeholder="Cap_of_America"
                           value="<?php if (!empty($username)) echo $username; ?>"
                           required>

                    <?php if (!$username_correct) : ?>
                        <div class="invalid-feedback" style="color: red;">
                            <?php echo $username_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label class="control-label text-light" for="email"><b>Email</b></label>
                    <input class="form-control text-center <?php echo get_input_validity($email_correct); ?>"
                           type="email" name="email" id="email" placeholder="captain@america.com"
                           value="<?php if (!empty($email)) echo $email; ?>"
                           required>

                    <?php if (!$email_correct) : ?>
                        <div class="invalid-feedback" style="color: red;">
                            <?php echo $email_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="form-group col-lg-4 ml-auto">
                    <label class="control-label text-light" for="password"><b>Password</b></label>
                    <input class="form-control text-center <?php echo get_input_validity($password_correct); ?>"
                           type="password" name="password" id="password" aria-describedby="password-help"
                           value="<?php if (!empty($password)) echo $password; ?>"
                           required>
                    <small id="password-help" class="form-text text-light">
                        Password must be 8-25 characters long and contain only upper and lower case letters,
                        digits and underscores.
                    </small>

                    <?php if (!$password_correct) : ?>
                        <div class="invalid-feedback" style="color: red;">
                            <?php echo $password_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="form-group col-lg-4 mr-auto">
                    <label class="control-label text-light" for="password-confirmation"><b>Confirm Password</b></label>
                    <input class="form-control text-center <?php echo get_input_validity($password_verified); ?>"
                           type="password" name="password_confirmation" id="password-confirmation"
                           value="<?php if (!empty($password_confirmation)) echo $password_confirmation; ?>"
                           required>

                    <?php if (!$password_verified) : ?>
                        <div class="invalid-feedback" style="color: red;">
                            <?php echo $password_confirmation_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-2 mx-auto">
                    <input class="btn btn-block btn-outline-light btn-lg" type="submit" value="Register" style="border-width: 2px;">
                </div>
            </div>
        </form>

        <div class="text-center">
            <p class="text-white">Already have an account? <a href="<?php echo PATH_PREFIX . 'login'; ?>" class="text-warning">Login</a></p>
        </div>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
