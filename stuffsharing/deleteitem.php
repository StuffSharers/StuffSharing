<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    die();
} else if (!isset($_GET["id"]) && !isset($_POST["sid"])) {
    die();
}

$sid = isset($_GET["id"]) ? $_GET["id"] : $_POST["sid"];
$item = get_item($sid);
$success = false;
$message = "";

$confirm_delete = (isset($_POST["delete"]) && $_POST["delete"] === "1");
$is_owner = $is_admin || $item["uid"] == $_SESSION["uid"];


if ($is_owner && $confirm_delete) {
    try {
        $stmt = $db->prepare("DELETE FROM ss_stuff WHERE sid = :sid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();

        $success = true;

    } catch (PDOException $e) {
        $message = gen_alert("danger", "We are unable to process your request. Please try again later.");
    }

} else if ($is_owner) {
    $delete_token = md5(uniqid('auth', true));
    $_SESSION['delete_token'] = $delete_token;
}

?>
<!DOCTYPE html>
<html lang="en">

<?php include("partials/head.html") ?>
<?php 
    if ($success)
        header('Refresh: 3; URL=mystuff.php');
?>

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
                        <h3 class="panel-title"><i class="fa fa-trash" aria-hidden="true"></i> Delete item</h3>
                    </div>
                    <div class="panel-body">
<?php if ($success): ?>
                        <div class="alert alert-success" role="alert">
                            <p>Item has been deleted.</p>
                            <p>Redirecting you in 3 seconds...</p>
                        </div>
<?php else: ?>
                        <?=$message?>

                        <div class="row">
                            <div class="col-xs-6">
                                <dl>
                                    <dt>Name:</dt>
                                    <dd><?=$item["name"]?></dd>
                                </dl>
                            </div>
                            <div class="col-xs-6">
                                <dl>
                                    <dt>Owner:</dt>
                                    <dd><?=$item["username"]?></dd>
                                </dl>
                            </div>
                        </div>
                        <form method="POST" class="btn-group-justified" role="group">
                            <input type="hidden" name="delete_token" value="<?=$delete_token?>" />
                            <input type="hidden" name="sid" value="<?=$sid?>" />
                            <input type="hidden" name="delete" value="<?=true?>" />
                            <div class="btn-group">
                                <button type="submit" class="btn btn-danger"><i class="fa fa-exclamation-triangle" aria-hidden="true" role="group"></i> Delete item</button></div>
                            <div class="btn-group">
                                <a class="btn btn-info" role="group" href='<?="item.php?id=".$sid?>'>
                                    <i class="fa fa-undo" aria-hidden="true"></i> Back
                                </a>
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
