<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=mystuff');
    die();
}

$results = get_items_owned_by($_SESSION["uid"]);

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
                <h1 class="page-header">My Stuff
                    <small><?=count($results)?> items</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
<?php if (count($results) == 0): ?>
            <div class="col-lg-12">Looks like you haven't advertised any stuff yet. Click <a href="advertise.php">here</a> to get started!</div>

<?php else: ?>

                <?php foreach($results as $result): ?><div class="col-md-4 col-sm-6 portfolio-item">
                    <a href="item.php?id=<?=$result["sid"]?>"><img class="img-responsive" src="//placehold.it/700x400" alt=""></a>
                    <h3><a href="item.php?id=<?=$result["sid"]?>"><?=$result["name"]?><?=$result["is_available"] ? "" : " (sold)"?></a></h3>
                    <p><?=$result["description"]?></p>
                    <div class="row">
                        <div class="col-xs-6">
                            <dl>
                                <dt>Pickup:</dt>
                                <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M Y g:ia", strtotime($result["pickup_date"]))?></dd>
                                <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$result["pickup_locn"]?></dd>
                            </dl>
                        </div>
                        <div class="col-xs-6">
                            <dl>
                                <dt>Return:</dt>
                                <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M Y g:ia", strtotime($result["return_date"]))?></dd>
                                <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$result["return_locn"]?></dd>
                            </dl>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

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
