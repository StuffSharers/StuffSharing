<?php
require("include/auth.php");
require("include/functions.php");

$has_query = isset($_GET["q"]);

if ($has_query) {
    $results = search_available_items($_GET["q"]);
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
                <h1 class="page-header"><?=$has_query ? "Search Results" : "Search"?>
                    <small><?=$has_query ? $results->rowCount()." items found" : "available stuff"?></small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
            <div class="col-lg-12">
                <form>
                <div class="input-group" style="margin-bottom: 25px">
                    <input type="text" class="form-control" name="q" placeholder="Search for available stuff" <?php if ($has_query) { echo "value=\"".$_GET["q"]."\"";} ?>/>
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div>
                </form>
            </div>

            <?php if ($has_query): ?><?php foreach($results as $result): ?><div class="col-md-4 col-sm-6 portfolio-item">
                <a href="#"><img class="img-responsive" src="//placehold.it/700x400" alt=""></a>
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
            <?php endforeach; ?><?php endif ?>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
