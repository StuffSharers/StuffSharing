<?php 
	require("include/auth.php");
	require("include/functions.php");
?>

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
				<h1>Advertise your Items!</h1>
			</div>
			
			<hr>
			
			<form>
			
				<div class="row form-group">
					<label for="item-name-input-form" class="col-xs-2 col-form-label">Item Name</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" placeholder="Item Name Here" id="item-name-input-form">
					</div>
				</div>
				
				<div class="row form-group">
					<label for="item-description-input-text-area" class="col-xs-2 col-form-label">Item Description</label>
					<div class="col-xs-10">
						<textarea class="textarea-limit-width form-control" type="text" placeholder="Item Description Here" id="item-description-input-text-area" rows="3" maxlength="512"> </textarea>
					</div>
				</div>
				
				<div class="row form-group">
					<label for="price-input-form" class="col-xs-2 col-form-label">Price</label>
					<div class="col-xs-10">
						<input class="form-control" type="text" placeholder="Price" id="price-input-form">
					</div>
				</div>
				
				<?php
					$datetime = date('Y-m-d', time()).'T'.date('H:i:s', time());
				?>				
				
				<div class="row form-group">
					<label for="pickup-date-input" class="col-xs-2 col-form-label">Pickup Date</label>
					<div class="col-xs-10">
						<div class='date input-group' id='pickup-date-input'>
							<input class="form-control" type='datetime-local' id='pickup-date-input' name='pickup-date' value="<?php echo htmlspecialchars($datetime); ?>"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				
				<div class="row form-group">
					<label for="return-date-input" class="col-xs-2 col-form-label">Return Date</label>
					<div class="col-xs-10">
						<div class='date input-group' id='return-date-input'>
							<input class="form-control" type='datetime-local' id='return-date-input' name='return-date' value="<?php echo htmlspecialchars($datetime); ?>"/>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				
				<div class="row">
					<div class="col-xs-12">
						<button type="submit" class="btn btn-primary col-xs-4 col-xs-offset-1">Advertise!</button>
						<button type="cancel" class="btn btn-secondary col-xs-4 col-xs-offset-2">Cancel</button>
					</div>
				</div>	
				
			</form>
			

			
			<?php
				include("partials/footer.html");
			?>
			
		</div>
		
	</body>
	
</html>
	