<?php
require_once("include/db.php");
require("include/auth.php");

if (!$is_authed) {
    header('Location: login.php?redirect=editprofile');
    die();
}

// try {
//     $db
// }

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

        <form method="POST">

        <!-- Input Row -->
        <div class="row">

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Account details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" class="form-control" id="username" name="username" value="<?=$username?>" maxlength="20" />
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?=$email?>" maxlength="255" />
                        </div>
                        <div class="form-group">
                            <label for="contact">Contact number:</label>
                            <input type="number" class="form-control" id="contact" name="contact" value="<?=$contact?>" maxlength="8" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Change password</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="password">Current password:</label>
                            <input type="password" class="form-control" id="password" name="password" maxlength="20" />
                        </div>
                        <div class="form-group">
                            <label for="new_password">New password:</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" maxlength="20" />
                        </div>
                        <div class="form-group">
                            <label for="confirm_new_password">Confirm new password:</label>
                            <input type="password" class="form-control" id="confirm_new_password" maxlength="20" />
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.row -->

        <!-- Button Row -->
        <div class="row">
            <div class="col-lg-12">
                <button type="submit" class="btn btn-default pull-right"><i class="fa fa-check" aria-hidden="true"></i> Save</button>
            </div>
        </div>
        <!-- /.row -->

        </form>

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
