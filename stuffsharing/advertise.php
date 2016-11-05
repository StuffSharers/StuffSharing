<?php
require("include/auth.php");
require("include/functions.php");

if (!$is_authed) {
    header('Location: login.php?redirect=advertise');
    die();
}

$last_stuffname = "";
$last_stuffdesc = "";
$last_stuffprice = "0.00";
$last_pickupdate = NULL;
$last_pickuploc = "";
$last_returndate = NULL;
$last_returnloc = "";

$message = "";

$success = false;

$stuffname = $stuffdesc = $stuffprice = $pickupdate = $pickuploc = $returndate = $returnloc = "";

if (isset($_POST["stuff-name"], $_POST["stuff-desc"], $_POST["stuff-price"], $_POST["pickup-date"], $_POST["pickup-location"], $_POST["return-date"], $_POST["return-location"])) {

    $success = true;

    global $message;
    global $last_stuffname, $last_stuffdesc, $last_stuffprice, $last_pickupdate, $last_pickuploc, $last_returndate, $last_returnloc;

    $message = "";

    $stuffname = neutralize_input($_POST["stuff-name"]);
    $stuffdesc = neutralize_input($_POST["stuff-desc"]);
    $stuffprice = neutralize_input($_POST["stuff-price"]);
    $pickupdate = gen_date_from_datetime_local_str($_POST["pickup-date"]);
    $pickuploc = neutralize_input($_POST["pickup-location"]);
    $returndate = gen_date_from_datetime_local_str($_POST["return-date"]);
    $returnloc = neutralize_input($_POST["return-location"]);

    if (!is_valid_stuffname($stuffname)) {
        $message .= gen_alert('danger', "Invalid item name: Must be 1 - 255 characters");
        $success = false;
    } else {
        $last_stuffname = $stuffname;
    }

    $last_stuffdesc = $stuffdesc;

    if (!is_valid_price($stuffprice)) {
        $message .= gen_alert('danger', "Invalid price: Must be a valid positive numeric value");
        $success = false;
    } else {
        $last_stuffprice = $stuffprice;
    }

    if (!is_valid_pickup_location($pickuploc)) {
        $message .= gen_alert('danger', "Invalid pickup location: Must be 1 - 255 characters");
        $success = false;
    } else {
        $last_pickuploc = $pickuploc;
    }

    if (!is_valid_return_location($returnloc)) {
        $message .= gen_alert('danger', "Invalid return location: Must be 1 - 255 characters");
        $success = false;
    } else {
        $last_returnloc = $returnloc;
    }

    if (!is_valid_pickup_and_return_date($pickupdate, $returndate)) {
        $message .= gen_alert('danger', "Invalid pickup and return dates: return date must be later than pickup date");
        $success = false;
    } else {
        $last_pickupdate = $pickupdate;
        $last_returndate = $returndate;
    }

} else {
    //Nothing Posted
    $success = false;
}

if (!is_null($last_pickupdate) and !is_null($last_returndate)) {
    $pickupdatetimeobj = $last_pickupdate;
    $returndatetimeobj = $last_returndate;
} else {
    $pickupdatetimeobj = new DateTime('NOW');
    $returndatetimeobj = new DateTime('TOMORROW');
}

$pickupdatetime = htmlspecialchars($pickupdatetimeobj->format('Y-m-d\TH:i'));
$returndatetime = htmlspecialchars($returndatetimeobj->format('Y-m-d\TH:i'));

if ($success) {
    // Insert into Database
    // Adapted from: http://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php

    try {
        global $db;

        $stmt = $db->prepare('INSERT INTO ss_stuff(uid, name, description, pref_price, pickup_date, pickup_locn, return_date, return_locn)
                              VALUES (:curruid, :stuffname, :stuffdesc, :stuffprice, :pickupdate, :pickuploc, :returndate, :returnloc)
                              RETURNING sid');

        $stmt->bindParam(':curruid', $_SESSION["uid"], PDO::PARAM_INT);
        $stmt->bindParam(':stuffname', $stuffname, PDO::PARAM_STR, 256);
        $stmt->bindParam(':stuffdesc', $stuffdesc, is_null($stuffdesc) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':stuffprice', $stuffprice, PDO::PARAM_STR);
        $stmt->bindParam(':pickupdate', $pickupdate->format('Y-m-d H:i'), PDO::PARAM_STR);
        $stmt->bindParam(':pickuploc', $pickuploc, PDO::PARAM_STR, 256);
        $stmt->bindParam(':returndate', $returndate->format('Y-m-d H:i'), PDO::PARAM_STR);
        $stmt->bindParam(':returnloc', $returnloc, PDO::PARAM_STR, 256);

        $stmt->execute();

        $result = $stmt->fetch();
        if ($result == false) {
            throw new Exception("Could not get returning sid");
        }

        $sid = $result["sid"];

        $success = true;

        $message = gen_alert("success", "Your item has been advertised! You may view it here.");

    } catch (PDOException $e) {
        $message = gen_alert("danger", "We are unable to process your request. Please try again later.");
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
                <h1 class="page-header"></h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">

            <div class="col-lg-offset-2 col-lg-8 col-md-offset-1 col-md-10">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3 class="panel-title"><i class="fa fa-plus-circle" aria-hidden="true"></i> Advertise</h3>
                    </div>
                    <div class="panel-body">
<?php if ($success): ?>
                        <div class="alert alert-success" role="alert">Your item has been advertised! You may view it <a href="item.php?id=<?=$sid?>">here</a>.</div>
<?php else: ?>
                        <?=$message?>

                        <form method="post">

                            <div class="form-group">
                                <label for="stuff-name-input-form">Item name: *</label>
                                <input class="form-control" type="text" placeholder="Name" id="stuff-name-input-form" value="<?=$last_stuffname?>" name="stuff-name" maxlength="255" required="required">
                            </div>

                            <div class="form-group">
                                <label for="stuff-description-input-text-area">Item description:</label>
                                <textarea class="form-control" type="text" placeholder="Description" id="stuff-description-input-text-area" name="stuff-desc" rows="3" maxlength="1024"><?=$last_stuffdesc?></textarea>
                            </div>

                            <div class="form-group">
                                <label for="price-input-form"><i class="fa fa-usd" aria-hidden="true"></i> Starting price: *</label>
                                <input class="form-control" type="number" min="0" id="price-input-form" step="0.01" value="<?=$last_stuffprice?>" name="stuff-price" required="required">
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pickup-location-input-form"><i class="fa fa-map-marker" aria-hidden="true"></i> Pickup location: *</label>
                                        <input class="form-control" type="text" placeholder="Pickup location" id="pickup-location-input-form" value="<?=$last_pickuploc?>" name="pickup-location" maxlength="255" required="required">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pickup-date-input"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Pickup date: *</label>
                                        <div class='input-group' id='pickup-date-input'>
                                            <input class="form-control" type='datetime-local' id='pickup-date-input' name='pickup-date' value="<?=$pickupdatetime?>" required="required"/>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="return-location-input-form"><i class="fa fa-map-marker" aria-hidden="true"></i> Return location: *</label>
                                        <input class="form-control" type="text" placeholder="Return location" id="return-location-input-form" value="<?=$last_returnloc?>" name="return-location" maxlength="255" required="required">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="return-date-input"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> Return date: *</label>
                                        <div class='date input-group' id='return-date-input'>
                                            <input class="form-control" type='datetime-local' id='return-date-input' name='return-date' value="<?=$returndatetime?>" required="required"/>
                                            <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <em>*Required</em>
                                <button type="submit" class="btn btn-success pull-right"><i class="fa fa-plus-circle" aria-hidden="true"></i> Advertise!</button>
                            </div>

                        </form>
<?php endif ?>
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
