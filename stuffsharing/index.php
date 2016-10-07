<?php
require_once("include/db.php");
require("include/auth.php");

try {
    $results = $db->query("SELECT name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true;");

} catch (PDOException $e) {
    die("We are unable to process your request. Please try again later.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>StuffSharing | a place to share stuff</title>

    <!-- Bootstrap Core CSS -->
    <link href="resources/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="resources/css/3-col-portfolio.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="resources/css/font-awesome.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="./">StuffSharing</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="#">Browse</a>
                    </li>
                    <li>
                        <a href="#">Advertise</a>
                    </li>
                </ul>
                <ul class="nav navbar-nav navbar-right">
<?php if ($is_authed): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i> <?=$username?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="editprofile.php"><i class="fa fa-cog" aria-hidden="true"></i> Edit Profile</a></li>
                            <li><a href="logout.php?redirect=main"><i class="fa fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        </ul>
                    </li>
<?php else: ?>
                    <li>
                        <a href="login.php?redirect=main"><i class="fa fa-sign-in" aria-hidden="true"></i> Login</a>
                    </li>
<?php endif ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>

    <!-- Page Content -->
    <div class="container">

        <!-- Page Header -->
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">Available Stuff
                    <small><?=$results->rowCount()?> items</small>
                </h1>
            </div>
        </div>
        <!-- /.row -->

        <!-- Projects Row -->
        <div class="row">

            <?php foreach($results as $result): ?><div class="col-md-4 portfolio-item">
                <a href="#"><img class="img-responsive" src="http://placehold.it/700x400" alt=""></a>
                <h3><a href="#"><?=$result["name"]?></a></h3>
                <p><?=$result["description"]?></p>
                <p>Pickup: <?=$result["pickup_date"]?> at <?=$result["pickup_locn"]?><br />
                Return: <?=$result["return_date"]?> at <?=$result["return_locn"]?></p>
            </div>
            <?php endforeach; ?>

        </div>
        <!-- /.row -->

        <hr>

        <!-- Pagination -->
        <!-- <div class="row text-center">
            <div class="col-lg-12">
                <ul class="pagination">
                    <li>
                        <a href="#">&laquo;</a>
                    </li>
                    <li class="active">
                        <a href="#">1</a>
                    </li>
                    <li>
                        <a href="#">2</a>
                    </li>
                    <li>
                        <a href="#">3</a>
                    </li>
                    <li>
                        <a href="#">4</a>
                    </li>
                    <li>
                        <a href="#">5</a>
                    </li>
                    <li>
                        <a href="#">&raquo;</a>
                    </li>
                </ul>
            </div>
        </div> -->
        <!-- /.row -->

        <!-- <hr> -->

        <!-- Footer -->
        <footer>
            <div class="row">
                <div class="col-lg-12">
                    <p>Copyright &copy; StuffSharers 2016</p>
                </div>
            </div>
            <!-- /.row -->
        </footer>

    </div>
    <!-- /.container -->

    <!-- jQuery -->
    <script src="resources/js/jquery.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="resources/js/bootstrap.min.js"></script>

</body>

</html>
