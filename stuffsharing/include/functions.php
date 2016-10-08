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

?>