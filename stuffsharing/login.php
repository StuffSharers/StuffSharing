<?php
require_once("include/db.php");
require("include/auth.php");

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

$success = false;

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if (isset($_GET["redirect"])) {
    switch($_GET["redirect"]) {
        case "main":
        $redirect = "./";
        break;

        case "editprofile":
        $redirect = "./editprofile.php";
        break;

        default:
        $redirect = false;
    }
} else {
    $redirect = false;
}

if ($is_authed) {
    $success = true;

} else {
    if (!isset($_POST["login_token"])) {
        $message = "Please enter a valid username/email and password";

    } elseif ($_POST["login_token"] != $_SESSION["login_token"]) {
        $message = "Invalid form submission";

    } elseif (empty($username) || strlen($username) > 255 || strlen($password) > 20 || strlen($password) < 4) {
        $message = "Access denied";

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
                $message = "Access denied";

            } else {
                $_SESSION["uid"] = $result["uid"];
                $username = $result["username"];
                $success = true;
            }

        } catch (PDOException $e) {
            $message = "We are unable to process your request. Please try again later.";
        }

    }
}

if ($success && $redirect != false) {
    header('Location: '.$redirect);
    die();
}

$login_token = md5(uniqid('auth', true));
$_SESSION['login_token'] = $login_token;

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
                <h1 class="page-header">Authentication required</h1>
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
                        <div class="well well-sm">
<?php if ($success): ?>
                            You are already logged in as <b><?=$username?></b>. <a href="logout.php?redirect=main">Logout</a>
<?php else: ?>
                            <?=$message?>
                        </div>
                        <form method="POST">
                            <div class="form-group">
                                <label for="username">Username/Email</label>
                                <input type="text" class="form-control" id="username" name="username" value="<?=$username?>" maxlength="255" />
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" value="" maxlength="20" />
                            </div>
                            <!-- <div class="form-group"> -->
                                <input type="hidden" name="login_token" value="<?=$login_token?>" />
                                <button type="submit" class="btn btn-default pull-right">Login</button>
                            <!-- </div> -->
                        </form>
                    </div>
<?php endif ?>
                </div>
            </div>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
