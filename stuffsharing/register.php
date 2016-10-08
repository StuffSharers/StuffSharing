<?php
require("include/auth.php");
require("include/functions.php");

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html


if (!$is_authed) {
    $success = false;

    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";
    $email = isset($_POST["email"]) ? $_POST["email"] : "";
    $contact = isset($_POST["contact"]) ? $_POST["contact"] : "";

    $last_username = "";
    $last_email = "";
    $last_contact = "";

    if (!isset($_POST["register_token"])) {
        $message = "";

    } elseif ($_POST["register_token"] != $_SESSION["register_token"]) {
        $message = "<div class=\"alert alert-danger\" role=\"alert\">Invalid form submission</div>";

    } else {
        $message = "";

        if (strlen($username) > 20 || strlen($username) < 4 || !ctype_alnum($username)) {
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">Invalid username: must be 4-20 alphanumeric characters</div>";
            $username = NULL;
        } else {
            $last_username = $username;
        }

        if ((strlen($password) > 20 || strlen($password) < 4)) {
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">Invalid password: must be 4-20 characters</div>";
            $password = NULL;
        }

        if (empty($email) || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">Invalid email</div>";
            $email = NULL;
        } else {
            $last_email = $email;
        }

        if (!empty($contact) && (strlen($contact) != 8 || !ctype_digit($contact))) {
            $message .= "<div class=\"alert alert-danger\" role=\"alert\">Invalid contact number: must be 8 digits\n</div>";
            $contact = NULL;
        } else {
            $last_contact = $contact;
        }

        if (!is_null($username) && !is_null($password) && !is_null($email) && !is_null($contact)) {
            $password = sha1($password);
            $contact = empty($contact) ? NULL : $contact;

            try {
                $stmt = $db->prepare("INSERT INTO ss_user (username, password, email, contact)
                                      VALUES (:username, :password, :email, :contact);");

                $stmt->bindParam(':username', $username, PDO::PARAM_STR);
                $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':contact', $contact, is_null($contact) ? PDO::PARAM_NULL : PDO::PARAM_INT);

                $stmt->execute();

                $success = true;

            } catch (PDOException $e) {
                if ($e->getCode() == 23505) {
                    $message = "<div class=\"alert alert-danger\" role=\"alert\">Username or email already exists</div>";
                } else {
                    $message = "<div class=\"alert alert-danger\" role=\"alert\">We are unable to process your request. Please try again later.</div>";
                }
            }
        }

    }

    $register_token = md5(uniqid('auth', true));
    $_SESSION['register_token'] = $register_token;
}

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
                <h1 class="page-header"></h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">

            <div class="col-lg-offset-4 col-lg-4 col-md-offset-3 col-md-6 col-sm-offset-2 col-sm-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</h3>
                    </div>
                    <div class="panel-body">
<?php if ($is_authed): ?>
                        <div class="alert alert-danger" role="alert">Please <a href="logout.php?redirect=register">logout</a> before continuing!</div>
<?php elseif ($success): ?>
                        <div class="alert alert-success" role="alert">Success! You may proceed to <a href="login.php?redirect=main">login</a></div>
<?php else: ?>
                        <?=$message?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Username*</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?=$last_username?>" placeholder="4-20 alphanumeric characters" maxlength="20" />
                            </div>
                            <div class="form-group">
                                <label for="password">Password*</label>
                                <input type="password" class="form-control" id="password" name="password" placeholder="4-20 characters" maxlength="20" />
                            </div>
                            <div class="form-group">
                                <label for="email">Email*</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?=$last_email?>" placeholder="Must be valid" maxlength="255" />
                            </div>
                            <div class="form-group">
                                <label for="contact">Contact number</label>
                                <input type="number" class="form-control" id="contact" name="contact" value="<?=$last_contact?>" placeholder="8 digits" maxlength="8" />
                            </div>
                            <div class="form-group">
                                <em>*Required</em>
                                <input type="hidden" name="register_token" value="<?=$register_token?>" />
                                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-user-plus" aria-hidden="true"></i> Register</button>
                            </div>
                        </form>
<?php endif ?>
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
