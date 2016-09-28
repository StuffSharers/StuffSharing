<?php
require("include/db.php");
session_start();

// Adapted from https://www.phpro.org/tutorials/Basic-Login-Authentication-with-PHP-and-MySQL.html

$success = false;

$username = isset($_POST["username"]) ? $_POST["username"] : "";
$password = isset($_POST["password"]) ? $_POST["password"] : "";

if (isset($_SESSION["uid"])) {
    try {
        $stmt = $db->prepare("SELECT username FROM ss_user WHERE uid = :uid;");

        $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false) {
            throw new Exception("Unknown uid in session");
        }

        $username = $result["username"];
        $success = true;

    } catch (Exception $e) {
        $message = "We are unable to process your request. Please try again later.";
        session_unset();
        session_destroy();
        session_start();
    }

} else {
    if (!isset($_POST["login_token"])) {
        $message = "Please enter a valid username/email and password";

    } elseif ($_POST["login_token"] != $_SESSION["login_token"]) {
        $message = "Invalid form submission";

    } elseif (empty($username) || strlen($username) > 255 || strlen($password) > 20 || strlen($password) < 4) {
        $message = "Access denied";

    } else {
        $password = sha1($password);

        try {
            $stmt = $db->prepare("SELECT uid, username FROM ss_user
                                  WHERE (username = :username OR email = :username) AND password = :password;");

            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR, 40);

            $stmt->execute();
            $result = $stmt->fetch();

            if ($result == false) {
                $message = "Access denied";

            } else {
                $_SESSION["uid"] = $result["uid"];
                $username = $result["username"];
                $success = true;
            }

        } catch (PDOException $e) {
            $message = "We are unable to process your request. Please try again later.";
        }

    }
}

$login_token = md5(uniqid('auth', true));
$_SESSION['login_token'] = $login_token;

?>
<html>
<head>
    <title>Login</title>
</head>
<body>
<?php if ($success): ?>
    <p>Success! You are logged in as <b><?=$username?></b>. <a href="logout.php">Logout</a></p>
<?php else: ?>
    <p><?=$message?></p>
    <form method="POST">
        <fieldset>
            <p>
                <label for="username">Username/Email</label>
                <input type="text" name="username" value="<?=$username?>" maxlength="255" />
            </p>
            <p>
                <label for="password">Password</label>
                <input type="password" name="password" value="" maxlength="20" />
            </p>
            <p>
                <input type="hidden" name="login_token" value="<?=$login_token?>" />
                <input type="submit" value="Login" />
            </p>
        </fieldset>
    </form>
<?php endif ?>
</body>
</html>