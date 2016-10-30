<?php
require("include/auth.php");
require("include/functions.php");

$sid = isset($_GET["id"]) ? $_GET["id"] : "";

if (!ctype_digit($sid)) {
    die();
}

$item = get_item($sid);
if ($item == false) {
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

        <!-- Portfolio Item Heading -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$item["name"]?>
                    <small><?=$item["is_available"] ? "available" : "not available"?></small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Portfolio Item Row -->
        <div class="row">

            <div class="col-md-6">
                <img class="img-responsive" src="http://placehold.it/750x500" alt="">
            </div>

            <div class="col-md-6">
                <h3>Description</h3>
                <p><?=$item["description"]?></p>
                <dl>
                    <dt>Pickup:</dt>
                    <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($item["pickup_date"]))?></dd>
                    <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$item["pickup_locn"]?></dd>
                </dl>
                <dl>
                    <dt>Return:</dt>
                    <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($item["return_date"]))?></dd>
                    <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$item["return_locn"]?></dd>
                </dl>
            </div>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
