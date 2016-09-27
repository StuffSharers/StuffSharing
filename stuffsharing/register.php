<?php
require("include/db.php");
session_start();

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

if (!isset($_POST["username"], $_POST["password"], $_POST["form_token"])) {
    $message = "Please enter a username, password and email";

} elseif ($_POST["form_token"] != $_SESSION["form_token"]) {
    $message = "Invalid form submission";

} elseif (strlen($_POST["username"]) > 20 || strlen($_POST["username"]) < 4) {
    $message = "Incorrect Length for Username";

} elseif ((strlen($_POST["password"]) > 20 || strlen($_POST["password"]) < 4)) {
    $message = "Incorrect Length for Password";

} elseif (!empty($_POST["contact"]) && strlen($_POST["contact"]) != 8) {
    $message = "Incorrect Length for Contact number";

} elseif (!ctype_alnum($_POST["username"])) {
    $message = "Username must be alphanumeric";

} elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    $message = "Invalid Email";

} else {
    $username = $_POST["username"];
    $password = sha1($_POST["password"]);
    $email = $_POST["email"];
    $contact = empty($_POST["contact"]) ? NULL : $_POST["contact"];

    try {
        $stmt = $db->prepare("INSERT INTO ss_user (username, password, email, contact) VALUES (:username, :password, :email, :contact);");

        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':contact', $contact, is_null($contact) ? PDO::PARAM_NULL : PDO::PARAM_INT);

        $stmt->execute();

        $message = "Success!";

    } catch (PDOException $e) {
        $message = "Could not register: ".$e->errorInfo[0];
        if ($e->getCode() == 23505) {
            $message = "Username or email already exists";
        } else {
            $message = "We are unable to process your request. Please try again later.";
        }
    }
}

$form_token = md5(uniqid('auth', true));
$_SESSION['form_token'] = $form_token;
?>
<html>
<head>
    <title>Register</title>
</head>
<body>
    <p><?=$message?></p>
    <form method="POST">
        <fieldset>
            <p>
                <label for="username">Username</label>
                <input type="text" name="username" value="<?=$_POST["username"]?>" maxlength="20" />
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" value="" maxlength="20" />
            </p>
            <p>
                <label for="email">Email</label>
                <input type="email" name="email" value="<?=$_POST["email"]?>" maxlength="255" />
            </p>
            <p>
                <label for="contact">Contact number</label>
                <input type="number" name="contact" value="<?=$_POST["contact"]?>" maxlength="8" />
            </p>
            <p>
                <input type="hidden" name="form_token" value="<?=$form_token?>" />
                <input type="submit" value="Register" />
            </p>
        </fieldset>
    </form>
</body>
</html>
