<?php
require_once("db.php");

function setup_redirect() {
    global $redirect;

    if (isset($_GET["redirect"])) {
        switch($_GET["redirect"]) {
            case "main":
            $redirect = "./";
            break;

            case "register":
            $redirect = "./register.php";
            break;

            case "editprofile":
            $redirect = "./editprofile.php";
            break;

            case "deleteprofile":
            $redirect = "./deleteprofile.php";
            break;

            default:
            $redirect = false;
        }
    } else {
        $redirect = false;
    }
}

function gen_alert($class, $message) {
    return "<div class=\"alert alert-${class}\" role=\"alert\">${message}</div>";
}

function is_valid_username($username) {
    return strlen($username) >= 4 && strlen($username) <= 20 && ctype_alnum($username);
}

function is_valid_password($password) {
    return strlen($password) >= 4 && strlen($password) <= 20;
}

function is_valid_email($email) {
    return strlen($email) > 0 && strlen($email) <= 255 && filter_var($email, FILTER_VALIDATE_EMAIL);
}

function is_valid_username_email($username_email) {
    return is_valid_username($username_email) || is_valid_email($username_email);
}

function is_valid_contact($contact) {
    return strlen($contact) == 8 && ctype_digit($contact);
}

function get_profile() {
    if (!isset($_SESSION["uid"])) {
        return;
    }

    global $db;

    global $email;
    global $contact;
    global $join_date;

    try {
        $stmt = $db->prepare("SELECT email, contact, join_date FROM ss_user WHERE uid = :uid;");

        $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false) {
            throw new Exception("Unknown uid in session");
        }

        $email = $result["email"];
        $contact = $result["contact"];
        $join_date = $result["join_date"];

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");

    } catch (Exception $e) {
        session_unset();
        session_destroy();
        session_start();
        die("We are unable to process your request. Please try logging in again.");
    }
}

function get_available_items() {
    global $db;

    try {
        return $db->query("SELECT name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true;");
    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

?>