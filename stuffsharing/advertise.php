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

if ($_POST) {

    $success = true;

    global $message;
    global $last_stuffname, $last_stuffdesc, $last_stuffprice, $last_pickupdate, $last_pickuploc, $last_returndate, $last_returnloc;

    $message = "";

    //All neccessary Form Inputs filled in due to required field.

    $stuffname = neutralize_input($_POST["stuff-name"]);
    $stuffdesc = neutralize_input($_POST["stuff-desc"]);
    $stuffprice = neutralize_input($_POST["stuff-price"]);
    $pickupdate = gen_date_from_datetime_local_str($_POST["pickup-date"]);
    $pickuploc = neutralize_input($_POST["pickup-location"]);
    $returndate = gen_date_from_datetime_local_str($_POST["return-date"]);
    $returnloc = neutralize_input($_POST["return-location"]);

    if (!is_valid_stuffname($stuffname)) {
        $message .= gen_alert('danger', "Invalid Stuff Name: Must be 1 - 255 alphanumeric characters");
        $success = false;
    } else {
        $last_stuffname = $stuffname;
    }

    $last_stuffdesc = $stuffdesc;

    if (!is_valid_price($stuffprice)) {
        $message .= gen_alert('danger', "Invalid Stuff Price: Must be a valid numeric value");
        $success = false;
    } else {
        $last_stuffprice = $stuffprice;
    }

    if (!is_valid_pickup_location($pickuploc)) {
        $message .= gen_alert('danger', "Invalid Pickup Location: Must be 1 - 255 alphanumeric characters");
        $success = false;
    } else {
        $last_pickuploc = $pickuploc;
    }

    if (!is_valid_return_location($returnloc)) {
        $message .= gen_alert('danger', "Invalid Return Location: Must be 1 - 255 alphanumeric characters");
        $success = false;
    } else {
        $last_returnloc = $returnloc;
    }

    if (!is_valid_pickup_and_return_date($pickupdate, $returndate)) {
        $message .= gen_alert('danger', "Invalid Pickup and Return Dates: Pickup Date must be earlier than Return Date \n");
        $success = false;
    } else {
        $last_pickupdate = $pickupdate;
        $last_returndate = $returndate;
    }

} else {
    //Nothing Posted
    $success = false;
}

if ($success) {
    // Insert into Database
    // Adapted from: http://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php

    $curruid = $_SESSION["uid"];
    get_profile();

    $availability = true;

    try {
        global $db;

        $stmt = $db->prepare('INSERT INTO ss_stuff(uid, name, description, is_available, pref_price, pickup_date, pickup_locn, return_date, return_locn)
                               VALUES (:curruid, :stuffname, :stuffdesc, :availability, :stuffprice, :pickupdate, :pickuploc, :returndate, :returnloc)');

        $stmt->bindParam(':curruid', $curruid, PDO::PARAM_INT);
        $stmt->bindParam(':stuffname', $stuffname, PDO::PARAM_STR, 256);
        $stmt->bindParam(':stuffdesc', $stuffdesc, is_null($stuffdesc) ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->bindParam(':availability', $availability, PDO::PARAM_BOOL);
        $stmt->bindParam(':stuffprice', $stuffprice, PDO::PARAM_STR);
        $stmt->bindParam(':pickupdate', $pickupdate->format('Y-m-d H:i'), PDO::PARAM_STR);
        $stmt->bindParam(':pickuploc', $pickuploc, PDO::PARAM_STR, 256);
        $stmt->bindParam(':returndate', $returndate->format('Y-m-d H:i'), PDO::PARAM_STR);
        $stmt->bindParam(':returnloc', $returnloc, PDO::PARAM_STR, 256);

        $stmt->execute();

        $success = true;

        $message = gen_alert("success", "Stuff Advertised!");

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
                <h1 class="page-header">Advertise your Stuff!</h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
            <div class="col-lg-12">

                <?=$message?>

                <form method="post">

                    <div class="form-group">
                        <label for="stuff-name-input-form">Name: *</label>
                        <input class="form-control" type="text" placeholder="Name" id="stuff-name-input-form" value="<?=$last_stuffname?>" name="stuff-name" maxlength="255" required="required">
                    </div>

                    <div class="form-group">
                        <label for="stuff-description-input-text-area">Description:</label>
                        <textarea class="form-control" type="text" placeholder="Description" id="stuff-description-input-text-area" name="stuff-desc" rows="3" maxlength="1024"><?=$last_stuffdesc?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="price-input-form">Price: </label>
                        <input class="form-control" type="text" id="price-input-form" value="<?=$last_stuffprice?>" name="stuff-price">
                    </div>

                    <?php
                        if (!is_null($last_pickupdate) and !is_null($last_returndate)) {
                            $pickupdatetimeobj = $last_pickupdate;
                            $returndatetimeobj = $last_returndate;
                        } else {
                            $pickupdatetimeobj = new DateTime('NOW');
                            $returndatetimeobj = new DateTime('TOMORROW');
                        }

                        $pickupdatetime = $pickupdatetimeobj->format('Y-m-d\TH:i');
                        $returndatetime = $returndatetimeobj->format('Y-m-d\TH:i');
                    ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="pickup-location-input-form">Pickup Location: *</label>
                                <input class="form-control" type="text" placeholder="Pickup Location Here" id="pickup-location-input-form" value="<?=$last_pickuploc?>" name="pickup-location" maxlength="255" required="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pickup-date-input">Pickup Date: *</label>
                                <div class='input-group' id='pickup-date-input'>
                                    <input class="form-control" type='datetime-local' id='pickup-date-input' name='pickup-date' value="<?=htmlspecialchars($pickupdatetime)?>" required="required"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="return-location-input-form">Return Location: *</label>
                                <input class="form-control" type="text" placeholder="Return Location Here" id="return-location-input-form" value="<?=$last_returnloc?>" name="return-location" maxlength="255" required="required">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="return-date-input">Return Date: *</label>
                                <div class='date input-group' id='return-date-input'>
                                    <input class="form-control" type='datetime-local' id='return-date-input' name='return-date' value="<?=htmlspecialchars($returndatetime)?>" required="required"/>
                                    <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-1">
                            <em>*Required</em>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right">
                                <a class="btn btn-danger" href="./" role="button"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>
                                <button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> Advertise!</button>
                            </div>
                        </div>
                    </div>

                </form>

            </div>
        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>

</html>
