<?php
require("include/auth.php");
require("include/functions.php");

$query = isset($_GET["q"]) ? neutralize_input($_GET["q"]) : "";
$min_price = isset($_GET["min_price"]) ? neutralize_input($_GET["min_price"]) : "";
$max_price = isset($_GET["max_price"]) ? neutralize_input($_GET["max_price"]) : "";
$pickup_start = isset($_GET["pickup_start"]) ? neutralize_input($_GET["pickup_start"]) : "";
$pickup_end = isset($_GET["pickup_end"]) ? neutralize_input($_GET["pickup_end"]) : "";
$return_start = isset($_GET["return_start"]) ? neutralize_input($_GET["return_start"]) : "";
$return_end = isset($_GET["return_end"]) ? neutralize_input($_GET["return_end"]) : "";
$has_query = isset($_GET["q"]);


if ($has_query) {
	$pickup_startdate = empty($pickup_start) ? "" : DateTime::createFromFormat("d/m/Y", $pickup_start);
	$pickup_enddate = empty($pickup_end) ? "" : DateTime::createFromFormat("d/m/Y", $pickup_end);
	$return_startdate = empty($return_start) ? "" : DateTime::createFromFormat("d/m/Y", $return_start);
	$return_enddate = empty($return_end) ? "" : DateTime::createFromFormat("d/m/Y", $return_end);

	$pickup_start = empty($pickup_startdate) ? "" : $pickup_startdate->format("Y-m-d");
	$pickup_end = empty($pickup_enddate) ? "" : $pickup_enddate->format("Y-m-d");
	$return_start = empty($return_startdate) ? "" : $return_startdate->format("Y-m-d");
	$return_end = empty($return_enddate) ? "" : $return_enddate->format("Y-m-d");

    $str_array = preg_split('/\s+/', $query);
    $str_len_not_exceeded = !empty($query) ? (bool) array_product(array_map(function($w) {return strlen($w) < 256;}, $str_array)) : true;

    $error = true;
    if (count($str_array) > 10) {
        $message = gen_alert('danger', "Maximum 10 search terms");
    } else if (!$str_len_not_exceeded) {
    	$message = gen_alert('danger', "Each search term must be 256 characters or less");
    } else if (!empty($pickup_start) && !empty($pickup_end) && $pickup_startdate > $pickup_enddate) {
    	$message = gen_alert('danger', "Earliest date for pickup is later than latest date");
    } else if (!empty($return_start) && !empty($return_end) && $return_startdate > $return_enddate) {
        $message = gen_alert('danger', "Earliest date for return is later than latest date");
	} else {
		$error = false;
        $results = search_available_items($str_array, $min_price, $max_price, $pickup_start, $pickup_end, $return_start, $return_end);
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<link href="resources/css/bootstrap-datepicker3.css" rel="stylesheet">

<?php include("partials/head.html") ?>

<body>

<?php include("partials/navigation.php") ?>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header"><?=$has_query ? "Search Results" : "Search"?>
                    <small><?=$has_query ? $error ? "error" : count($results)." items found" : "available stuff"?></small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Main Row -->
        <div class="row">
            <div class="col-lg-12">
            	<form>
            		<!-- Input group -->
                	<div class="input-group" style="margin-bottom: 25px">
	                    <input type="text" class="form-control" name="q" placeholder="Search for available stuff" value="<?=$query?>" />
	                	<span class="input-group-btn">
	                    	<button type="button" class="btn btn-default dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius:0; margin-left:-1px"><i class="fa fa-filter" aria-hidden="true"></i> 
	                    		<span class="caret"></span>
					        </button>
					        <!-- Dropdown menu -->
					        <div class="dropdown-menu dropdown-menu-right">
					        	<div class="form-horizontal" role="form">
						 			<span style="min-width: 400px" class="dropdown-header"><i class="fa fa-usd" aria-hidden="true"></i> Preferred price</span>
					 				<div class="form-group clearfix" style="margin-left: 0; margin-right: 0">
						 				<label for="min-price" class="control-label col-sm-2">Min:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="min_price" class="form-control input-sm">
						 				</div>
						 				<label for="max-price" class="control-label col-sm-2">Max:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="max_price" class="form-control input-sm">
						 				</div>
						 			</div>
						 		</div>	
						 		<div class="form-horizontal" role="form">
						 			<span class="dropdown-header"><i class="fa fa-calendar" aria-hidden="true"></i> Pickup Date</span>
						 			<div class="form-group input-daterange clearfix" style="margin-left: 0; margin-right: 0">
						 				<label for="pickup-start" class="control-label col-sm-2">Earliest:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="pickup_start" class="form-control input-sm">
						 				</div>
						 				<label for="pickup-end" class="control-label col-sm-2">Latest:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="pickup_end" class="form-control input-sm">
						 				</div>
						 			</div>
						 		</div>
						 		<div class="form-horizontal" role="form">
						 			<span class="dropdown-header"><i class="fa fa-calendar" aria-hidden="true"></i> Return Date</span>
						 			<div class="form-group input-daterange clearfix" style="margin-left: 0; margin-right: 0">
						 				<label for="return-start" class="control-label col-sm-2">Earliest:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="return_start" class="form-control input-sm">
						 				</div>
						 				<label for="return-end" class="control-label col-sm-2">Latest:</label>
						 				<div class="col-sm-4">
						 					<input type="text" name="return_end" class="form-control input-sm">
						 				</div>
						 			</div>
						 		</div>
					        </div>
					        <!-- /.dropdown-menu -->   
		                </span>
		                <!-- Submit button -->
	                    <span class="input-group-btn">
		                    <button class="btn btn-primary" type="submit">
		                    	<!-- Added a &nbsp; to properly align the input box and search button -->
		                    	<i class="fa fa-search" aria-hidden="true"></i>&nbsp;
		                    </button>
		                </span>
                	</div>
                	<!-- /.input-group -->
                </form>
                <?php if ($has_query and $error): ?><?=$message?><?php endif ?>
            </div>

            <?php if ($has_query and !$error): ?><?php foreach($results as $result): ?><div class="col-md-4 col-sm-6 portfolio-item">
                <a href="item.php?id=<?=$result["sid"]?>"><img class="img-responsive" src="//placehold.it/700x400" alt=""></a>
                <h3><a href="item.php?id=<?=$result["sid"]?>"><?=$result["name"]?></a></h3>
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
            <?php endforeach; ?><?php endif ?>

        </div>
        <!-- /.row -->

<?php include("partials/footer.html") ?>

    </div>
    <!-- /.container -->

</body>
<script type="text/javascript" src="resources/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript">
	(function($) {
	    $('.input-daterange').datepicker({
	        format: 'dd/mm/yyyy',
	        autoclose: true,
    		todayHighlight: true
	    });
	})(jQuery);
</script>
</html>
