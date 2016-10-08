<?php
// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html
session_start();
session_unset();
session_destroy();

require("include/auth.php");
require("include/functions.php");

setup_redirect();
if ($redirect != false) {
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
                        <h3 class="panel-title"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</h3>
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-success" role="alert">Logged out! <a href="login.php?redirect=main">Login</a></div>
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
