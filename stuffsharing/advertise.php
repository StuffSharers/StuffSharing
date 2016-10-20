<?php 
	require("include/auth.php");
	require("include/functions.php");
?>

<?php 
	
	$message = "";
	
	function has_posted(){
		
		$success = false;
		
		$stuffname = $stuffdesc = $stuffprice = $pickupdate = $pickuploc = $returndate = $returnloc = "";
		
		if ($_POST) {
			
			$success = true;
			
			global $message;
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
			} 
			
			if (!is_valid_price($stuffprice)) {
				$message .= gen_alert('danger', "Invalid Stuff Price: Must be a valid numeric value");
				$success = false;
			} 
			
			if (!is_valid_pickup_location($pickuploc)) {
				$message .= gen_alert('danger', "Invalid Pickup Location: Must be 1 - 255 alphanumeric characters");
				$success = false;
			}
			
			if (!is_valid_return_location($returnloc)) {
				$message .= gen_alert('danger', "Invalid Return Location: Must be 1 - 255 alphanumeric characters");
				$success = false;				
			}
			
			if (!is_valid_pickup_and_return_date($pickupdate, $returndate)) {
				$message .= gen_alert('danger', "Invalid Pickup and Return Dates: Pickup Date must be earlier than Return Date \n");
				$success = false;
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
	}
?>

<?php if (!$is_authed): header('Location: login.php?redirect=advertise');?>
	
<?php else: ?>

<!DOCTYPE html>
	<html lang="en">
	
	<head>
		<title>StuffSharing | a place to share stuff</title>
		
		<style>
			.textarea-limit-width{
				width:100%;
				resize:vertical;
			}
			
			.buffer-s{
				margin-top:25px;
				margin-bottom:25px;
			}	
			
			.buffer-m{
				margin-top:50px; 
				margin-bottom:50px;
			}	
			
			.buffer-l{
				margin-top:75px;
				margin-bottom:75px;
			}				
		</style>
	</head>
	
	<?php
		include("partials/head.html");
		include("partials/navigation.php");
	?>	
	
	<body>
		
		<div class="container">
		
			<div class="row text-center">
				<h1>Advertise your Stuff!</h1>
			</div>
			
			<hr>
			
			<?php 
				has_posted();
				echo($message); 
			?>
			
			<form method="post">
			
				<div class="row form-group">
					<label for="stuff-name-input-form" class="col-xs-2 col-form-label">Stuff Name: *</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" placeholder="Stuff Name Here" id="stuff-name-input-form" name="stuff-name" maxlength="255" required="required">
					</div>
				</div>
				
				<div class="row form-group">
					<label for="stuff-description-input-text-area" class="col-xs-2 col-form-label">Stuff Description: </label>
					<div class="col-xs-10">
						<textarea class="textarea-limit-width form-control" type="text" placeholder="Stuff Description Here" id="stuff-description-input-text-area" name="stuff-desc" rows="3" maxlength="1024"></textarea>
					</div>
				</div>
				
				<div class="row form-group">
					<label for="price-input-form" class="col-xs-2 col-form-label">Price: </label>
					<div class="col-xs-10">
						<input class="form-control" type="text" value="0.00" id="price-input-form" name="stuff-price">
					</div>
				</div>
				
				<?php				
					$nowdatetimeobj = new DateTime('NOW');
					$tomorrowdatetimeobj = new DateTime('TOMORROW');
					
					$nowdatetime = $nowdatetimeobj->format('Y-m-d'). 'T' .$nowdatetimeobj->format('H:i');
					$tomorrowdatetime = $tomorrowdatetimeobj->format('Y-m-d'). 'T' .$tomorrowdatetimeobj->format('H:i');
				?>
				
				<div class="row form-group">
					<label for="pickup-date-input" class="col-xs-2 col-form-label">Pickup Date: *</label>
					<div class="col-xs-10">
						<div class='date input-group' id='pickup-date-input'>
							<input class="form-control" type='datetime-local' id='pickup-date-input' name='pickup-date' value="<?php echo htmlspecialchars($nowdatetime); ?>" required="required"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				
				<div class="row form-group">
					<label for="pickup-location-input-form" class="col-xs-2 col-form-label">Pickup Location: *</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" placeholder="Pickup Location Here" id="pickup-location-input-form" name="pickup-location" maxlength="255" required="required">
					</div>
				</div>				
				
				<div class="row form-group">
					<label for="return-date-input" class="col-xs-2 col-form-label">Return Date: *</label>
					<div class="col-xs-10">
						<div class='date input-group' id='return-date-input'>
							<input class="form-control" type='datetime-local' id='return-date-input' name='return-date' value="<?php echo htmlspecialchars($tomorrowdatetime); ?>" required="required"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				
				<div class="row form-group">
					<label for="return-location-input-form" class="col-xs-2 col-form-label">Return Location: *</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" placeholder="Return Location Here" id="return-location-input-form" name="return-location" maxlength="255" required="required">
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-1 buffer-s">
						<em>*Required</em>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12 buffer-s">
						<button type="submit" class="btn btn-primary col-xs-4 col-xs-offset-1">Advertise!</button>
						<button type="button" onclick="window.location='index.php';return false;" class="btn btn-secondary col-xs-4 col-xs-offset-2">Cancel</button>
					</div>
				</div>	
				
			</form>
			
			<?php
				include("partials/footer.html");
			?>
			
		</div>
		
	</body>
	
</html>

<?php endif ?>