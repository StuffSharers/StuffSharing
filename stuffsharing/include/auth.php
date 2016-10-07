<?php
require_once("db.php");
session_start();

$is_authed = false;

if (isset($_SESSION["uid"])) {
    try {
        $stmt = $db->prepare("SELECT username, email, contact FROM ss_user WHERE uid = :uid;");

        $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false) {
            throw new Exception("Unknown uid in session");
        }

        $username = $result["username"];
        $email = $result["email"];
        $contact = $result["contact"];
        $is_authed = true;

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
