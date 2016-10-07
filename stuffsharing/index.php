<?php
require_once("include/db.php");
require("include/auth.php");

try {
    $results = $db->query("SELECT name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true;");

} catch (PDOException $e) {
    die("We are unable to process your request. Please try again later.");
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
                <h1 class="page-header">Available Stuff
                    <small><?=$results->rowCount()?> items</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Projects Row -->
        <div class="row">

            <?php foreach($results as $result): ?><div class="col-md-4 col-sm-6 portfolio-item">
                <a href="#"><img class="img-responsive" src="http://placehold.it/700x400" alt=""></a>
                <h3><a href="#"><?=$result["name"]?></a></h3>
                <p><?=$result["description"]?></p>
                <div class="row">
                    <div class="col-xs-6">
                        <dl>
                            <dt>Pickup:</dt>
                            <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($result["pickup_date"]))?></dd>
                            <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$result["pickup_locn"]?></dd>
                        </dl>
                    </div>
                    <div class="col-xs-6">
                        <dl>
                            <dt>Return:</dt>
                            <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($result["return_date"]))?></dd>
                            <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$result["return_locn"]?></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

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
