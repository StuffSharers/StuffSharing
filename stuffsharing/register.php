<?php
require("include/db.php");
session_start();

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

if (!isset($_POST["username"], $_POST["password"], $_POST["form_token"])) {
    $message = "Please enter a valid username and password";
} elseif ($_POST["form_token"] != $_SESSION["form_token"]) {
    $message = "Invalid form submission";
} elseif (strlen($_POST["username"]) > 20 || strlen($_POST["username"]) < 4) {
    $message = "Incorrect Length for Username";
} elseif ((strlen($_POST["password"]) > 20 || strlen($_POST["password"]) < 4)) {
    $message = "Incorrect Length for Password";
} else {
    $message = "Success! Username: ".$_POST["username"]." Password: ".$_POST["password"];
    // TODO: try adding user to database and handle exceptions
}

$form_token = md5(uniqid('auth', true));
$_SESSION['form_token'] = $form_token;
?>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <p><?php echo $message; ?></p>
    <form method="POST">
        <fieldset>
            <p>
                <label for="username">Username</label>
                <input type="text" name="username" value="" maxlength="20" />
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" value="" maxlength="20" />
            </p>
            <p>
                <input type="hidden" name="form_token" value="<?=$form_token?>" />
                <input type="submit" value="Register" />
            </p>
        </fieldset>
    </form>
</body>
</html>
