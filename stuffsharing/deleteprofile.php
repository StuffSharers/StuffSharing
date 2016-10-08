<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=deleteprofile');
    die();
}

$success = false;

if (!isset($_POST["delete_token"])) {
    $message = "";

} elseif ($_POST["delete_token"] != $_SESSION["delete_token"]) {
    $message = "<div class=\"alert alert-danger\" role=\"alert\">Invalid form submission</div>";

} elseif ($_POST["confirm_delete"] != "Please delete my account!") {
    $message = "<div class=\"alert alert-danger\" role=\"alert\">If you really wish to delete your account, please follow the instructions <em>exactly</em></div>";

} else {
    try {
        $stmt = $db->prepare("DELETE FROM ss_user WHERE uid = :uid;");
        $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);
        $stmt->execute();

        session_unset();
        session_destroy();
        $is_authed = false;
        $success = true;

    } catch (PDOException $e) {
        $message = "<div class=\"alert alert-danger\" role=\"alert\">We are unable to process your request. Please try again later.</div>";
    }

}

$delete_token = md5(uniqid('auth', true));
$_SESSION['delete_token'] = $delete_token;

get_profile();

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
                <div class="panel panel-danger">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-trash" aria-hidden="true"></i> Delete account</h3>
                    </div>
                    <div class="panel-body">
<?php if ($success): ?>
                        <div class="alert alert-success" role="alert">Success! We are sad to see you go :(</div>
<?php else: ?>
                        <?=$message?>

                        <div class="row">
                            <div class="col-xs-6">
                                <dl>
                                    <dt>Username:</dt>
                                    <dd><?=$username?></dd>
                                </dl>
                            </div>
                            <div class="col-xs-6">
                                <dl>
                                    <dt>Account type:</dt>
                                    <dd><?=$is_admin ? "Admin" : "Normal"?></dd>
                                </dl>
                            </div>
                        </div>
                        <dl>
                            <dt>Join date:</dt>
                            <dd><?=date("D, d M Y g:ia", strtotime($join_date))?></dd>
                        </dl>
                        <form method="POST">
                            <div class="form-group">
                                <label for="confirm_delete">Confirm:</label>
                                <div class="alert alert-danger" role="alert">
                                    <p><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Warning</strong>: this action is <strong><em>irreversible</em></strong>!</p>
                                    <p>To confirm, enter "Please delete my account!" <em>exactly</em> below</p>
                                </div>
                                <input type="text" class="form-control" id="confirm_delete" name="confirm_delete" placeholder="Please delete my account!" required="required" />
                            </div>
                            <input type="hidden" name="delete_token" value="<?=$delete_token?>" />
                            <button type="submit" class="btn btn-block btn-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Delete my account</button>
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
