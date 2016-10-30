<?php
require("include/auth.php");
require("include/functions.php");

$query = isset($_GET["q"]) ? neutralize_input($_GET["q"]) : "";
$has_query = isset($_GET["q"]);

if ($has_query) {
    $error = false;

    $str_array = preg_split('/\s+/', $query);

    if (count($str_array) > 10) {
        $error = true;
    } else {
        foreach ($str_array as $word) {
            if (strlen($word) > 256) {
                $error = true;
                break;
            }
        }
    }

    if (!$error) {
        $results = search_available_items($str_array);
    } else {
        $message = gen_alert('danger', "Maximum 10 search terms, each of length 256 characters or less");
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
                <div class="input-group" style="margin-bottom: 25px">
                    <input type="text" class="form-control" name="q" placeholder="Search for available stuff" value="<?=$query?>" />
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-primary" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                        </button>
                    </span>
                </div>
                </form>
                <?php if ($has_query and $error): ?><?=$message?><?php endif ?>
            </div>

            <?php if ($has_query and !$error): ?><?php foreach($results as $result): ?><div class="col-md-4 col-sm-6 portfolio-item">
                <a href="#"><img class="img-responsive" src="//placehold.it/700x400" alt=""></a>
                <h3><a href="#"><?=$result["name"]?></a></h3>
                <p><?=$result["description"]?></p>
                <div class="row">
                    <div class="col-xs-6">
                        <dl>
                            <dt>Pickup:</dt>
                            <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($result["pickup_date"]))?></dd>
                            <dd><i class="fa fa-fw fa-map-marker" aria-hidden="true"></i> <?=$result["pickup_locn"]?></dd>
                        </dl>
                    </div>
                    <div class="col-xs-6">
                        <dl>
                            <dt>Return:</dt>
                            <dd><i class="fa fa-fw fa-calendar-check-o" aria-hidden="true"></i> <?=date("D d M", strtotime($result["return_date"]))?></dd>
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

</html>
