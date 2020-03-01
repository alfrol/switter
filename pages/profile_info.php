<?php

$name_error_message = $email_error_message = $birth_date_error_message = $password_error_message = $password_confirmation_error_message = '';
$name_correct = $email_correct = $birth_date_correct = $password_correct = $password_verified = true;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    global $db;

    // Change the name of the user.
    if (isset($_POST['name'])) {
        $name = validate_input($_POST['name']);
        if (empty($name)) {
            $name_error_message = 'Name cannot be empty!';
            $name_correct = false;
        }

        if ($name_correct) {
            update_info($ui::NAME, $_POST['name']);
            $_SESSION['profile_modified'] = 1;
        }
    }

    // Change the email of the user
    if (isset($_POST['email'])) {
        $email = validate_input($_POST['email']);
        if (empty($email)) {
            $email_error_message = 'Email cannot be empty!';
            $email_correct = false;
        } else if ($db->exists($email, 'email') && $email !== $db->retrieve_user_information(get_observable_user_id(), $db::EMAIL)) {
            $email_error_message = "This email is already registered!";
            $email_correct = false;
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error_message = "Invalid email!";
            $email_correct = false;
        }

        if ($email_correct) {
            update_info($ui::EMAIL, $email);
            $_SESSION['profile_modified'] = 1;
        }
    }

    // Change the birth date and age of the user.
    if (isset($_POST['birth_date_check'])) {
        if (!empty($_POST['birth_year'])) {
            $year = intval($_POST['birth_year']);
        } else {
            $year = '';
        }

        if (!empty($_POST['birth_month'])) {
            $month = intval($_POST['birth_month']);
        } else {
            $month = '';
        }

        if (!empty($_POST['birth_day'])) {
            $day = intval($_POST['birth_day']);
        } else {
            $day = '';
        }

        $birth_date_correct = empty($year) && empty($month) && empty($day) || !empty($year) && !empty($month) && !empty($day) && checkdate(intval($month), intval($day), intval($year));

        if (!$birth_date_correct) {
            if (!checkdate(intval($month), intval($day), intval($year))) {
                $birth_date_error_message = 'Provided date is incorrect!';
            } else {
                $birth_date_error_message = 'Please fill out all fields or leave them all empty!';
            }
        } else {
            if (empty($year) && empty($month) && empty($day)) {
                update_info($ui::BIRTH_DATE,null);
                update_info($ui::AGE,null);
            } else {
                $date = date_create_from_format('Y-n-j H:i:s', "$year-$month-$day 00:00:00");
                update_info($ui::BIRTH_DATE, $date->format("Y-m-d H:i:s"));

                $age = intval(date('Y')) - $year;
                update_info($ui::AGE, $age);
                $_SESSION['profile_modified'] = 1;
            }
        }
    }

    // Change user bio.
    if (isset($_POST['bio'])) {
        update_info($ui::BIO, validate_input($_POST['bio']));
        $_SESSION['profile_modified'] = 1;
    }

    // Change the user password.
    if (isset($_POST['password'], $_POST['password_confirmation'])) {
        $password = validate_input($_POST['password']);
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $password_confirmation = validate_input($_POST['password_confirmation']);

        if (!empty($password)) {
            if (strlen($password) < 8) {
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
        }

        if (empty($password_confirmation) && !empty($password)) {
            $password_confirmation_error_message = "Password confirmation is required!";
            $password_verified = false;
        } else if (!password_verify($password_confirmation, $hash)) {
            $password_confirmation_error_message = "Passwords don't match!";
            $password_verified = false;
        }

        if ($password_correct === true && $password_verified) {
            update_info($ui::PASSWORD, $hash);
            $_SESSION['profile_modified'] = 1;
        }
    }

    // DELETE the account!
    if (isset($_POST['delete_account_check'])) {
        $db->delete_account(get_user_id());
        header('Location: login?logout=true');
    }
}

/** Get formatted birth date. */
function get_formatted_date() {
    global $db;

    $birth_date = $db->retrieve_user_information(get_user_id(), $db::BIRTH_DATE);
    if (empty($birth_date)) {
        return 0;
    }
    return strtotime(date_format(date_create_from_format('Y-m-d', $birth_date), 'Y-n-j'));
}

?>


<!doctype html>
<html lang="en">
    <?php

    $page_description = 'Profile Info';
    $page_name = 'Profile Info • Switter';

    require __DIR__ . '/../templates/header.php';

    ?>
<body class="bg-light">
    <?php require __DIR__ . '/../templates/navbar.php'; ?>

    <div class="container bg-white text-center py-lg-3 w-75">
        <div class="row">
            <div class="col-lg-1">
                <a href="<?php echo PATH_PREFIX . 'profile?id=' . $_SESSION['id']; ?>" class="btn btn-outline-dark btn-block btn-lg" role="button"
                   data-toggle="tooltip" data-placement="bottom" title="Back to profile" style="border: none;">
                    <i class="fas fa-arrow-left"></i>
                </a>
            </div>

            <div class="col-lg-11">
                <h1 class="display-4 mb-lg-5 pr-lg-5">Information About You</h1>
            </div>
        </div>

        <?php if (isset($_SESSION['profile_modified'])): ?>
            <div class="row">
                <div class="alert alert-success alert-dismissible fade show mx-auto" role="alert">
                    <strong>Success!</strong> Profile has been modified!
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        <?php unset($_SESSION['profile_modified']); endif; ?>

        <form action="<?php echo htmlspecialchars(PATH_PREFIX . 'profile-info'); ?>" method="POST" autocomplete="off" novalidate>
            <h2 class="text-dark">Main Info</h2>
            <hr class="border border-dark w-50">

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label for="name" class="control-label">Name</label>
                    <input type="text" class="form-control text-center <?php echo get_input_validity($name_correct); ?>"
                           name="name" id="name" value="<?php echo get_info($ui::NAME); ?>" required>

                    <?php if (!$name_correct) : ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?php echo $name_correct, $name_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label for="username" class="control-label">Username</label>
                    <input type="text" class="form-control text-center"
                           data-toggle="tooltip" data-placement="bottom" title="The username cannot be changed"
                           name="username" id="username" value="<?php echo get_info($ui::USERNAME); ?>" disabled>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label for="email" class="control-label">Email</label>
                    <input type="text" class="form-control text-center <?php echo get_input_validity($email_correct); ?>"
                           name="email" id="email" value="<?php echo get_info($ui::EMAIL); ?>" required>

                    <?php if (!$email_correct) : ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?php echo $email_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <p>Birth date</p>
                    <input type="text" name="birth_date_check" hidden>

                    <div class="row">
                        <div class="col-lg-4">
                            <select name="birth_year" id="birth_year"
                                    class="custom-select <?php echo get_input_validity($birth_date_correct); ?>" required>
                                <option value="" <?php if (get_formatted_date() === 0) echo 'selected' ?>></option>
                                <?php

                                for ($y = 1900; $y <= intval(date('Y')); $y++) {
                                    echo "<option value='$y'";
                                    if (get_formatted_date() !== 0 && date('Y', get_formatted_date()) == $y) {
                                        echo " selected>$y</option>";
                                    } else {
                                        echo ">$y</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <select name="birth_month" id="birth_month"
                                    class="custom-select <?php echo get_input_validity($birth_date_correct); ?>" required>
                                <option value="" <?php if (get_formatted_date() === 0) echo 'selected' ?>></option>
                                <?php

                                for ($m = 1; $m <= 12; $m++) {
                                    echo "<option value='$m'";
                                    if (get_formatted_date() !== 0 && date('n', get_formatted_date()) == $m) {
                                        echo " selected>$m</option>";
                                    } else {
                                        echo ">$m</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>

                        <div class="col-lg-4">
                            <select name="birth_day" id="birth_day"
                                    class="custom-select <?php echo get_input_validity($birth_date_correct); ?>" required>
                                <option value="" <?php if (get_formatted_date() === 0) echo 'selected' ?>></option>
                                <?php

                                for ($d = 1; $d <= 31; $d++) {
                                    echo "<option value='$d'";
                                    if (get_formatted_date() !== 0 && date('j', get_formatted_date()) == $d) {
                                        echo " selected>$d</option>";
                                    } else {
                                        echo ">$d</option>";
                                    }
                                }

                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="row justify-content-between px-lg-3">
                        <div class="col-lg-4">
                            <label for="birth_year" class="control-label text-muted"><small>Year</small></label>
                        </div>

                        <div class="col-lg-4">
                            <label for="birth_month" class="control-label text-muted"><small>Month</small></label>
                        </div>

                        <div class="col-lg-4">
                            <label for="birth_day" class="control-label text-muted"><small>Day</small></label>
                        </div>
                    </div>

                    <?php if (!$birth_date_correct) : ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?php echo $birth_date_correct, $birth_date_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg mx-auto">
                    <label for="bio" class="control-label">Biography</label>
                    <textarea class="form-control mx-auto text-center"
                              name="bio" id="bio" cols="30" rows="8" style="width: 80%;font-size: 20px;resize: none;overflow: hidden" aria-describedby="bio-help"
                              maxlength="450"><?php echo empty(get_info($ui::BIO)) ? null : get_info($ui::BIO); ?></textarea>

                    <small id="bio-help" class="form-text text-muted">
                        Biography can be maximum 450 characters long.
                    </small>
                </div>
            </div>

            <div class="form-group ">
                <div class="mt-lg-5 col-lg-2 mx-auto">
                    <input class="btn btn-block btn-outline-dark btn-lg" type="submit" value="Save" style="border-width: 2px;">
                </div>
            </div>
        </form>

        <form action="<?php echo htmlspecialchars(PATH_PREFIX . 'profile-info') ?>" method="POST" autocomplete="off" novalidate>
            <h2 class="mt-lg-5 text-dark">Change Password</h2>
            <hr class="border border-dark w-50">

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label class="control-label" for="password">Password</label>
                    <input class="form-control text-center <?php echo get_input_validity($password_correct); ?>"
                           type="password" name="password" id="password" aria-describedby="password-help">
                    <small id="password-help" class="form-text text-muted">
                        Password must be 8-25 characters long and contain only upper and lower case letters,
                        digits and underscores.
                    </small>

                    <?php if (!$password_correct) : ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?php echo $password_correct, $password_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group row">
                <div class="col-lg-4 mx-auto">
                    <label class="control-label" for="password-confirmation">Confirm Password</label>
                    <input class="form-control text-center <?php echo get_input_validity($password_verified); ?>"
                           type="password" name="password_confirmation" id="password-confirmation">

                    <?php if (!$password_verified) : ?>
                        <div class="invalid-feedback" style="display: block;">
                            <?php echo $password_verified, $password_confirmation_error_message; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group ">
                <div class="mt-lg-5 col-lg-2 mx-auto">
                    <input class="btn btn-block btn-outline-dark btn-lg" type="submit" value="Change" name="change_password" style="border-width: 2px;">
                </div>
            </div>
        </form>

        <form action="<?php echo htmlspecialchars(PATH_PREFIX . 'profile-info'); ?>" method="POST">
            <h2 class="text-danger mt-lg-5">Delete Account</h2>
            <hr class="border border-dark w-50">

            <div class="form-group row">
                <div class="col-lg-5 mx-auto">
                    <p>
                        Deleting your account will permanently remove all data.
                        Think carefully before pressing the button, there is no way back!
                    </p>
                </div>
            </div>

            <div class="from-group row">
                <div class="col-lg-2 mx-auto">
                    <input type="text" name="delete_account_check" hidden>
                    <input class="btn btn-block btn-danger btn-lg" type="submit" value="Delete"
                           onclick="return confirm('This action is irreversible! Are you sure?')">
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>
