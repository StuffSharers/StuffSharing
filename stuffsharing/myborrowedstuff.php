<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=mystuff');
    die();
}

$results = get_items_borrowed_by($_SESSION["uid"]);

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
                <h1 class="page-header">My Borrowed Stuff
                    <small><?=count($results)?> items</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
<?php if (count($results) == 0): ?>
            <div class="col-lg-12">Looks like you haven't borrowed any stuff yet.</div>

<?php else: ?>

<?php include("partials/results.php") ?>

<?php endif; ?>

        </div>
        <!-- /.row -->

        <!-- <hr> -->

        <!-- Pagination -->
        <!-- <div class="row text-center">
            <div class="col-lg-12">
                <ul class="pagination">
                    <li>
                        <a href="#">&laquo;</a>
                    </li>
                    <li class="active">
                        <a href="#">1</a>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li>
                        <a href="#">5</a>
                    </li>
                    <li>
                        <a href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </div> -->
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
