<?php
require("include/db.php");
session_start();

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

$success = false;

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$contact = isset($_POST["contact"]) ? $_POST["contact"] : "";

if (!isset($_POST["register_token"])) {
    $message = "Please enter a valid username, password and email";

} elseif ($_POST["register_token"] != $_SESSION["register_token"]) {
    $message = "Invalid form submission";

} else {
    $message = "<ul>";

    if (strlen($username) > 20 || strlen($username) < 4 || !ctype_alnum($username)) {
        $message .= "<li>Invalid username: must be 4-20 alphanumeric characters</li>";
        $username = NULL;
    }

    if ((strlen($password) > 20 || strlen($password) < 4)) {
        $message .= "<li>Invalid password: must be 4-20 characters</li>";
        $password = NULL;
    }

    if (empty($email) || strlen($email) > 255 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message .= "<li>Invalid email</li>";
        $email = NULL;
    }

    if (!empty($contact) && (strlen($contact) != 8 || !ctype_digit($contact))) {
        $message .= "<li>Invalid contact number: must be 8 digits\n</li>";
        $contact = NULL;
    }

    $message .= "</ul>";

    if (!is_null($username) && !is_null($password) && !is_null($email) && !is_null($contact)) {
        $password = sha1($password);
        $contact = empty($contact) ? NULL : $contact;

        try {
            $stmt = $db->prepare("INSERT INTO ss_user (username, password, email, contact) VALUES (:username, :password, :email, :contact);");

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':contact', $contact, is_null($contact) ? PDO::PARAM_NULL : PDO::PARAM_INT);

            $stmt->execute();

            $success = true;

        } catch (PDOException $e) {
            if ($e->getCode() == 23505) {
                $message = "Username or email already exists";
            } else {
                $message = "We are unable to process your request. Please try again later.";
            }
        }
    }

}

$register_token = md5(uniqid('auth', true));
$_SESSION['register_token'] = $register_token;

?>
<html>
<head>
    <title>Register</title>
</head>
<body>
<?php if ($success): ?>
    <p>Success!</p>
<?php else: ?>
    <p><?=$message?></p>
    <form method="POST">
        <fieldset>
            <p>
                <label for="username">Username</label>
                <input type="text" name="username" value="<?=$username?>" maxlength="20" />
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" value="" maxlength="20" />
            </p>
            <p>
                <label for="email">Email</label>
                <input type="email" name="email" value="<?=$email?>" maxlength="255" />
            </p>
            <p>
                <label for="contact">Contact number</label>
                <input type="number" name="contact" value="<?=$contact?>" maxlength="8" />
            </p>
            <p>
                <input type="hidden" name="register_token" value="<?=$register_token?>" />
                <input type="submit" value="Register" />
            </p>
        </fieldset>
    </form>
<?php endif ?>
</body>
</html>
