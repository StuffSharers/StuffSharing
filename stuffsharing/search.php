<?php
require("include/auth.php");
require("include/functions.php");

?>
<!DOCTYPE html>
<html lang="en">

<?php include("partials/head.html") ?>

<body>

<?php include("partials/navigation.php") ?>

<?php
	$has_query = isset($_GET['query']);

	function generate_results($results) {
		$results_array = $results->fetchAll();
		foreach ($results_array as $result) {
			/* Search result row start */
			echo "<div class=\"row\">";

			/* Thumbnail of item start */
			echo "<div class=\"col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-0 col-md-4 col-md-offset-0\">";
			echo "<img src=\"http://placehold.it/700x400\" class=\"img-thumbnail\"/>";
			echo "</div>";
			/* Thumbnail of item end */

			/* Name, Description, and Owner+Pickup+Return info start */
			echo "<div class=\"col-xs-8 col-xs-offset-2 col-sm-6 col-sm-offset-0 col-md-8 col-md-offset-0\">";
			echo "<h4>".$result["name"]."</h4>";
			echo "<p>".$result["description"]."</p>";

			/* Owner, Pickup and Return info start */
			echo "<div class \"row\">";

			/* Owner info start */
			echo "<div class=\"col-xs-4 col-sm-4 col-md-4\">";
			echo "<dl><dt>Owner:</dt>";
			echo "<dd><i class=\"fa fa-fw fa-user\" aria-hidden=\"true\"></i><a href=\"user.php?id=\"".$result["owner_id"].">"
					.$result["owner_name"]."</a></dd>";
			echo "</dl></div>";
			/* Owner info end */

			/* Pickup info start */
			echo "<div class=\"col-xs-4 col-sm-4 col-md-4\">";
			echo "<dl><dt>Pickup:</dt>";
			echo "<dd><i class=\"fa fa-fw fa-calendar-check-o\" aria-hidden=\"true\"></i>".date("D, d M Y", strtotime($result["pickup_date"]))."</dd>";
			echo "<dd><i class=\"fa fa-fw fa-map-marker\" aria-hidden=\"true\"></i>".$result["pickup_locn"]."</dd>";
			echo "</dl></div>";
			/* Pickup info end */

			/* Return info start */
			echo "<div class=\"col-xs-4 col-sm-4 col-md-4\">";
			echo "<dl><dt>Return:</dt>";
			echo "<dd><i class=\"fa fa-fw fa-calendar-check-o\" aria-hidden=\"true\"></i>".date("D, d M Y", strtotime($result["return_date"]))."</dd>";
			echo "<dd><i class=\"fa fa-fw fa-map-marker\" aria-hidden=\"true\"></i>".$result["return_locn"]."</dd>";
			echo "</dl></div>";
			/* Return info end */

			echo "</div>";
			/* Owner, Pickup and Return info end */

			echo "</div>";
			/* Name, Description and Owner+Pickup+Return info end */

			echo "</div>";
			/* Search result row end */
 
		}
		return;
	}
?> 

    <!-- Page Content -->
<div class="container">
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <form action="search.php"> 
            	<?php
            	if (!$has_query) {
            		echo "<p>Look for stuff that other users have offered!</p>";
            	}
            	?>
                <div class="input-group" id="adv-search">
                    <input type="text" class="form-control" name="query" placeholder="Search for stuff" <?php if ($has_query) { echo "value=\"".$_GET["query"]."\"";} ?>/>
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-8 col-lg-offset-2">


            <?php
            if ($has_query) {
                try {
                    global $db;
                    $query = $_GET['query'];
                    $str_array = explode(" ", $query);
                    $statement = "SELECT u.username AS owner_name, u.uid AS owner_id, s.name AS name, s.description AS description, s.pickup_date AS pickup_date, s.pickup_locn AS pickup_locn, s.return_date AS return_date, s.return_locn AS return_locn FROM available_stuff s, ss_user u  WHERE s.is_available = true AND s.uid = u.uid";
                    foreach ($str_array as $word) {
                        $word = strtolower($word);
                        $statement = $statement . " AND ((LOWER(s.name) LIKE '%" . $word . "%') OR (LOWER(s.description) LIKE '%" . $word . "%') OR (LOWER(s.pickup_locn) LIKE '%" . $word . "%') OR (LOWER(s.return_locn) LIKE '%" . $word . "%'))";
                    }
                    $statement = $statement . ";";
                    $results = $db->query($statement);

                    echo "<h2>Search Results</h2>";
                    echo "<h3>" . $results->rowCount() . " result(s) found</h3>";
                    generate_results($results);

                    
                }
                catch (Exception $e) {
                    die($e->getMessage());
                }

            }

            ?>
        </div>
    </div>

    <?php include("partials/footer.html") ?>
</div>
    <!-- /.container -->

</body>

</html>
