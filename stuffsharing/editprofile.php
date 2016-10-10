<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=editprofile');
    die();
}

get_profile();

$new_username = isset($_POST["new_username"]) ? $_POST["new_username"] : "";
$new_email = isset($_POST["new_email"]) ? $_POST["new_email"] : "";
$new_contact = isset($_POST["new_contact"]) ? $_POST["new_contact"] : "";

$password = isset($_POST["password"]) ? $_POST["password"] : "";
$new_password = isset($_POST["new_password"]) ? $_POST["new_password"] : "";
$confirm_new_password = isset($_POST["confirm_new_password"]) ? $_POST["confirm_new_password"] : "";

$change_account = $new_username != $username || $new_email != $email || $new_contact != $contact;
$change_password = !empty($password.$new_password.$confirm_new_password);

$message_account = "";
$message_password = "";

if (!isset($_POST["edit_token"])) {
    $message = "";

} elseif ($_POST["edit_token"] != $_SESSION["edit_token"]) {
    $message = gen_alert("danger", "Invalid form submission");

} else if (!$change_account && !$change_password) {
    $message = gen_alert("info", "No changes detected");

} else {
    $message = "";

    if ($change_account) {
        $is_valid_account_submission = true;

        if (!is_valid_username($new_username)) {
            $message_account .= gen_alert("danger", "Invalid username: must be 4-20 alphanumeric characters");
            $is_valid_account_submission = false;
        }

        if (!is_valid_email($new_email)) {
            $message_account .= gen_alert("danger", "Invalid email");
            $is_valid_account_submission = false;
        }

        if (!empty($new_contact) && !is_valid_contact($new_contact)) {
            $message_account .= gen_alert("danger", "Invalid contact number: must be 8 digits\n");
            $is_valid_account_submission = false;
        }

        if ($is_valid_account_submission) {
            $new_contact = empty($new_contact) ? NULL : $new_contact;

            try {
                $stmt = $db->prepare("UPDATE ss_user SET username = :username, email = :email, contact = :contact
                                      WHERE uid = :uid;");

                $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);
                $stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
                $stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
                $stmt->bindParam(':contact', $new_contact, is_null($new_contact) ? PDO::PARAM_NULL : PDO::PARAM_INT);

                $stmt->execute();

                get_profile();
                $message_account = gen_alert("success", "Account details updated");

            } catch (PDOException $e) {
                if ($e->getCode() == 23505) {
                    $message_account = gen_alert("danger", "Username or email already exists");
                } else {
                    $message_account = gen_alert("danger", "We are unable to process your request. Please try again later.");
                }
            }
        }
    }

    if ($change_password) {
        $is_valid_password_submission = true;

        if (empty($password)) {
            $message_password .= gen_alert("danger", "Your current password is required");
            $is_valid_password_submission = false;

        } elseif (!is_valid_password($password)) {
            $message_password .= gen_alert("danger", "Access denied");
            $is_valid_password_submission = false;

        } else {
            $password = sha1($password);

            try {
                $stmt = $db->prepare("SELECT 1 FROM ss_user WHERE uid = :uid AND password = :password;");

                $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);

                $stmt->execute();
                $result = $stmt->fetch();

                if ($result == false) {
                    $message_password .= gen_alert("danger", "Access denied");
                    $is_valid_password_submission = false;
                }

            } catch (PDOException $e) {
                $message_password .= gen_alert("danger", "We are unable to process your request. Please try again later.");
                $is_valid_password_submission = false;
            }
        }

        if ($new_password != $confirm_new_password) {
            $message_password .= gen_alert("danger", "Passwords do not match");
            $is_valid_password_submission = false;
        }

        if (!is_valid_password($new_password)) {
            $message_password .= gen_alert("danger", "New password must be 4-20 characters");
            $is_valid_password_submission = false;
        }

        if ($is_valid_password_submission) {
            $new_password = sha1($new_password);

            try {
                $stmt = $db->prepare("UPDATE ss_user SET password = :password WHERE uid = :uid;");

                $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);
                $stmt->bindParam(':password', $new_password, PDO::PARAM_STR, 40);

                $stmt->execute();

                $message_password = gen_alert("success", "Password changed");

            } catch (PDOException $e) {
                $message_password = gen_alert("danger", "We are unable to process your request. Please try again later.");
            }
        }
    }
}

$edit_token = md5(uniqid('auth', true));
$_SESSION['edit_token'] = $edit_token;

?>
<!DOCTYPE html>
<html lang="en">

<?php include("partials/head.html") ?>

<body>

<?php include("partials/navigation.php") ?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Edit Profile</h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">

            <form method="POST">
            <div class="col-md-9 col-sm-8">
                <?=$message?>
                <div class="row">
                    <div class="col-md-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-asterisk" aria-hidden="true"></i> Account details</h3>
                            </div>
                            <div class="panel-body">
                                <?=$message_account?>

                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" id="username" name="new_username" value="<?=$username?>" placeholder="4-20 alphanumeric characters" maxlength="20" />
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="new_email" value="<?=$email?>" placeholder="Must be valid" maxlength="255" />
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact number:</label>
                                    <input type="number" class="form-control" id="contact" name="new_contact" value="<?=$contact?>" placeholder="8 digits" maxlength="8" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="panel panel-success">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-key" aria-hidden="true"></i> Change password</h3>
                            </div>
                            <div class="panel-body">
                                <?=$message_password?>

                                <div class="form-group">
                                    <label for="password">Current password:</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Required to change password" maxlength="20" />
                                </div>
                                <div class="form-group">
                                    <label for="new_password">New password:</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" placeholder="4-20 characters" maxlength="20" />
                                </div>
                                <div class="form-group">
                                    <label for="confirm_new_password">Confirm new password:</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" placeholder="Must match above" maxlength="20" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
                        <input type="hidden" name="edit_token" value="<?=$edit_token?>" />
                        <button type="reset" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true"></i> Reset</button>
                        &nbsp;
                        <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
                        </div>
                    </div>
                </div>
                <div class="hidden-lg hidden-md hidden-sm">&nbsp;</div>
            </div>
            </form>

            <div class="col-md-3 col-sm-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-info-circle" aria-hidden="true"></i> Account info</h3>
                            </div>
                            <div class="panel-body">
                                <dl>
                                    <dt>Join date:</dt>
                                    <dd><?=date("D, d M Y g:ia", strtotime($join_date))?></dd><br />
                                    <dt>Account type:</dt>
                                    <dd><?=$is_admin ? "Admin" : "Normal"?></dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <a class="btn btn-block btn-danger" href="deleteprofile.php" role="button"><i class="fa fa-trash" aria-hidden="true"></i> Delete my account</a>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
