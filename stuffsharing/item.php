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

if ($is_authed) {
    $max_bid = get_max_bid($sid);
    if ($max_bid != false) {
        $max_bid_username = get_username_for_bid($sid, $max_bid);
        if ($max_bid_username == false) {
            die();
        }
    }
    $your_bid = get_bid_amount_for_user($sid, $_SESSION["uid"]);
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

            <div class="col-md-3 col-sm-6">
                <?php if (!empty($item["description"])): ?><h3>Description</h3>
                <p><?=$item["description"]?></p><?php endif ?>

                <h3>Details</h3>
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
                <dl>
                    <dt>Owner:</dt>
                    <dd><i class="fa fa-fw fa-user" aria-hidden="true"></i> <?=$item["username"]?></dd>
                    <dd><i class="fa fa-fw fa-envelope-o" aria-hidden="true"></i> <a href="mailto:<?=$item["email"]?>"><?=$item["email"]?></a></dd>
                    <?php if (!empty($item["contact"])): ?><dd><i class="fa fa-fw fa-phone" aria-hidden="true"></i> <a href="tel:<?=$item["contact"]?>"><?=$item["contact"]?></a></dd><?php endif ?>

                </dl>
            </div>

            <div class="col-md-3 col-sm-6">
                <h3>Bidding</h3>
<?php if ($is_authed): ?>
                <dl>
                    <dt>Starting price:</dt>
                    <dd><?=$item["pref_price"]?></dd>
                </dl>
                <dl>
                    <dt>Current highest bid:</dt>
                    <dd><?=$max_bid == false ? "None" : $max_bid?><?php if ($max_bid != false and $max_bid_username != $username): ?> (by <i class="fa fa-user" aria-hidden="true"></i> <?=$max_bid_username?>)<?php elseif ($max_bid != false and $max_bid_username == $username): ?> (by you)<?php endif ?></dd>
                </dl>
                <dl>
                    <dt>Your bid:</dt>
                    <dd><?=$your_bid == false ? "None" : $your_bid?></dd>
                </dl>
<?php else: ?>
                <p><a href="login.php?redirect=item&id=<?=$sid?>">Login</a> to view bidding details</p>
<?php endif ?>
            </div>
        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
