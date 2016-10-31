<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=editprofile');
    die();
}

get_profile();

$sid = $_GET['sid'];
try {
    $item = get_item($sid);
} catch (Exception $e) {
    die("We are unable to process your request. Please try again later.");
}

$message = "";

if (!$item) { // There is no item with such an SID
    $message = gen_alert("danger", "Invalid item number. Please try again.");
    $invalid_item = true;
}

else {
    $invalid_item = false;
    $all_bids = get_bids($sid);

    $bid_count = $all_bids->rowCount();
    $all_bids = $all_bids->fetchAll();
    $highest_bid = isset($all_bids[0]) ? $all_bids[0][1] : 0;
    
    foreach ($all_bids as $bid) {
        if ($bid[0] == $_SESSION["uid"]) {
            $my_current_bid = $bid[1];
        }
    }

    $bid_received = isset($_POST["bid_amt"]);

    if ($bid_received) {
        $bid_amt = $_POST["bid_amt"];
        if (isset($my_current_bid)) {
            try {
                update_bid($_SESSION["uid"], $sid, $bid_amt);
            } catch (Exception $e) {
                die("We are unable to process your request. Please try again later.");
            }

            $message = gen_alert("success", "Your bid has been successfully changed.");
        }
        else {
            try {
                insert_bid($_SESSION["uid"], $sid, $bid_amt);
            } catch (Exception $e) {
                die("We are unable to process your request. Please try again later.");
            }

            $message = gen_alert("success", "Your bid is successful.");
        }    
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

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Bid <small>on stuff</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
            <?=$message?>
            <div class="col-md-6">
            </div> 


            <form method="POST">
            <div class="col-md-6">
            
            </div>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
