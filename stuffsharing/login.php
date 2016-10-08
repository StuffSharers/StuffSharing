<?php
require("include/auth.php");
require("include/functions.php");

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

if ($is_authed) {
    $success = true;

} else {
    $success = false;

    $username = isset($_POST["username"]) ? $_POST["username"] : "";
    $password = isset($_POST["password"]) ? $_POST["password"] : "";

    $last_username = "";

    if (!isset($_POST["login_token"])) {
        $message = "";

    } elseif ($_POST["login_token"] != $_SESSION["login_token"]) {
        $message = "<div class=\"alert alert-danger\" role=\"alert\">Invalid form submission</div>";

    } elseif (empty($username) || strlen($username) > 255 || strlen($password) > 20 || strlen($password) < 4) {
        $message = "<div class=\"alert alert-danger\" role=\"alert\">Access denied</div>";

    } else {
        $password = sha1($password);

        try {
            $stmt = $db->prepare("SELECT uid, username FROM ss_user
                                  WHERE (username = :username OR email = :username) AND password = :password;");

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result == false) {
                $message = "<div class=\"alert alert-danger\" role=\"alert\">Access denied</div>";
                $last_username = $username;

            } else {
                $_SESSION["uid"] = $result["uid"];
                $username = $result["username"];
                $success = true;
            }

        } catch (PDOException $e) {
            $message = "<div class=\"alert alert-danger\" role=\"alert\">We are unable to process your request. Please try again later.</div>";
        }

    }

    $login_token = md5(uniqid('auth', true));
    $_SESSION['login_token'] = $login_token;
}

setup_redirect();
if ($success && $redirect != false) {
    header('Location: '.$redirect);
    die();
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
                        <h3 class="panel-title"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</h3>
                    </div>
                    <div class="panel-body">
<?php if ($success): ?>
                        <div class="alert alert-success" role="alert">You are logged in as <b><?=$username?></b>. <a href="logout.php?redirect=main">Logout</a></div>
<?php else: ?>
                        <?=$message?>

                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Username/Email:</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?=$last_username?>" maxlength="255" />
                            </div>
                            <div class="form-group">
                                <label for="password">Password:</label>
                                <input type="password" class="form-control" id="password" name="password" value="" maxlength="20" />
                            </div>
                            <!-- <div class="form-group"> -->
                                <input type="hidden" name="login_token" value="<?=$login_token?>" />
                                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</button>
                            <!-- </div> -->
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
