<?php
// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html
session_start();
session_unset();
session_destroy();
?>
<html>
<head>
    <title>Logout</title>
</head>
<body>
    <p>Logged out! <a href="login.php">Login</a></p>
</body>
</html>
