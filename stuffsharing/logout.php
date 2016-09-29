<?php
// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html
session_start();
session_unset();
session_destroy();

if (isset($_GET["redirect"])) {
    switch($_GET["redirect"]) {
        case "main":
        $redirect = "./";
        break;

        default:
        $redirect = false;
    }
} else {
    $redirect = false;
}

if ($redirect != false) {
    header('Location: '.$redirect);
    die();
}

?>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <p>Logged out! <a href="login.php">Login</a></p>
</body>
</html>
