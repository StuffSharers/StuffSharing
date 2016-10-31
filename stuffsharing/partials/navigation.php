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
                        <a href="./search.php">Search</a>
                    </li>
                    <li>
                        <a href="./advertise.php">Advertise</a>
                    </li>
<?php if ($is_authed): ?>
                    <li>
                        <a href="./mystuff.php">My Stuff</a>
                    </li>
<?php endif ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
<?php if ($is_authed): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user" aria-hidden="true"></i> <?=$username?> <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="editprofile.php"><i class="fa fa-fw fa-cog" aria-hidden="true"></i> Edit Profile</a></li>
                            <li><a href="logout.php?redirect=main"><i class="fa fa-fw fa-sign-out" aria-hidden="true"></i> Logout</a></li>
                        </ul>
                    </li>
<?php else: ?>
                    <li>
                        <a href="login.php?redirect=mystuff"><i class="fa fa-fw fa-sign-in" aria-hidden="true"></i> Login</a>
                    </li>
                    <li>
                        <a href="register.php"><i class="fa fa-fw fa-user-plus" aria-hidden="true"></i> Register</a>
                    </li>
<?php endif ?>
                </ul>
            </div>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>
