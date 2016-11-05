<?php
require("include/auth.php");
require("include/functions.php");

$sid = isset($_GET["id"]) ? $_GET["id"] : "";
$to_close = isset($_POST["close"]) && $_POST["close"] == "1";

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

    $is_owner = $item["uid"] == $_SESSION["uid"];
    if ($is_owner) {
        $current_bids = get_bids($sid);
        if ($to_close) {
            close_item($sid);
            $item["is_available"] = false;
        }
    } else {
        $your_bid = get_bid_amount_for_user($sid, $_SESSION["uid"]);
    }
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
                    <small><?=$item["is_available"] ? "available" : "sold"?><?php if ($is_owner): ?> <a href="edititem.php?id=<?=$sid?>">Edit item</a><?php endif ?></small>
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
                    <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M g:ia", strtotime($item["pickup_date"]))?></dd>
                    <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$item["pickup_locn"]?></dd>
                </dl>
                <dl>
                    <dt>Return:</dt>
                    <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M g:ia", strtotime($item["return_date"]))?></dd>
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
    <?php if ($is_owner): ?>

                <dl>
                    <dt><?=$item["is_available"] ? "Current bids" : "Winning bid"?></dt>
        <?php if ($current_bids == false): ?>

                    <dd>None</dd>
        <?php else: ?>

                    <?php foreach($current_bids as $bid): ?><dd><?=$bid["bid_amount"]?> (by <i class="fa fa-user" aria-hidden="true"></i> <?=$bid["username"]?>)</dd><?php if (!$item["is_available"]) break; ?><?php endforeach; ?>
        <?php endif ?>

                </dl>
                <?php if ($item["is_available"] and $current_bids != false): ?><form method="POST"><input type="hidden" name="id" value="<?=$sid?>" /><input type="hidden" name="close" value="1" /><button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Accept &amp; Close</button></form><?php endif; ?>
    <?php else: ?>

                <dl>
                    <dt>Current highest bid:</dt>
                    <dd><?=$max_bid == false ? "None" : $max_bid?><?php if ($max_bid != false and $max_bid_username != $username): ?> (by <i class="fa fa-user" aria-hidden="true"></i> <?=$max_bid_username?>)<?php elseif ($max_bid != false and $max_bid_username == $username): ?> (by you)<?php endif ?></dd>
                </dl>
                <dl>
                    <dt>Your bid:</dt>
                    <dd><?=$your_bid == false ? "None" : $your_bid?></dd>
                </dl>
                <div class="row"><div class="col-xs-8"><form method="POST"><div class="input-group input-group-sm"><input class="form-control" type="number" min="0" value="0.00" name="bid_amount" required="required"><span class="input-group-btn"><input type="hidden" name="id" value="<?=$sid?>" /><input type="hidden" name="close" value="1" /><button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Bid</button></span></div></form></div></div>
    <?php endif ?>
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
