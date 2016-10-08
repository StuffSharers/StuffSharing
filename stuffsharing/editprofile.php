<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=editprofile');
    die();
}

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
                <h1 class="page-header">Edit Profile</h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">

            <form method="POST">
            <div class="col-md-9 col-sm-8">
                <div class="row">
                    <div class="col-md-7">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-asterisk" aria-hidden="true"></i> Account details</h3>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label for="username">Username:</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?=$username?>" placeholder="4-20 alphanumeric characters" maxlength="20" />
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?=$email?>" placeholder="Must be valid" maxlength="255" />
                                </div>
                                <div class="form-group">
                                    <label for="contact">Contact number:</label>
                                    <input type="number" class="form-control" id="contact" name="contact" value="<?=$contact?>" placeholder="8 digits" maxlength="8" />
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
                                    <input type="password" class="form-control" id="confirm_new_password" placeholder="Must match above" maxlength="20" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="pull-right">
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
                        <div class="panel panel-danger">
                            <div class="panel-heading">
                                <h3 class="panel-title"><i class="fa fa-trash" aria-hidden="true"></i> Delete account</h3>
                            </div>
                            <div class="panel-body">
                            <form action="deleteprofile.php" method="POST">
                                <div class="form-group">
                                    <label for="confirm_delete">Confirm:</label>
                                    <input type="text" class="form-control" id="confirm_delete" name="confirm_delete" placeholder="Please delete my account!" />
                                </div>
                                <button type="submit" class="btn btn-block btn-danger"><i class="fa fa-trash" aria-hidden="true"></i> Delete my account</button>
                            </form>
                            </div>
                        </div>
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
