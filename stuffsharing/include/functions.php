<?php
require_once("db.php");

function setup_redirect() {
    global $redirect;

    if (isset($_GET["redirect"])) {
        switch($_GET["redirect"]) {
            case "main":
            $redirect = "./";
            break;

            case "editprofile":
            $redirect = "./editprofile.php";
            break;

            default:
            $redirect = false;
        }
    } else {
        $redirect = false;
    }
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

?>