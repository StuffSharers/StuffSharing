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

            case "advertise":
            $redirect = "./advertise.php";
            break;

            case "item":
            $redirect = isset($_GET["id"]) ? "./item.php?id=".$_GET["id"] : false;
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

function gen_date_from_datetime_local_str($datetime_local_str) {
    return new DateTime($datetime_local_str);
}

function neutralize_input($data) {
    //Taken from http://www.w3schools.com/php/php_form_validation.asp
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
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

function is_valid_stuffname($stuffname) {
    return strlen($stuffname) >= 1 && strlen($stuffname) <= 255;
}

function is_valid_price($price) {
    return is_numeric($price) and $price >= 0;
}

function is_valid_pickup_location($pickup_location) {
    return strlen($pickup_location) >= 1 && strlen($pickup_location) <= 255;
}

function is_valid_return_location($return_location) {
    return strlen($return_location) >= 1 && strlen($return_location) <= 255;
}

function is_valid_pickup_and_return_date($pickup_date, $return_date) {
    return $pickup_date < $return_date;
}

function get_profile() {
    if (!isset($_SESSION["uid"])) {
        return;
    }

    global $db;

    global $username;
    global $email;
    global $contact;
    global $join_date;

    try {
        $stmt = $db->prepare("SELECT username, email, contact, join_date FROM ss_user WHERE uid = :uid;");

        $stmt->bindParam(':uid', $_SESSION["uid"], PDO::PARAM_INT);

        $stmt->execute();
        $result = $stmt->fetch();

        if ($result == false) {
            throw new Exception("Unknown uid in session");
        }

        $username = $result["username"];
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

function get_item($sid) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT s.sid, s.name, s.description, s.pickup_date, s.pickup_locn, s.return_date, s.return_locn, s.is_available, s.pref_price, u.username, u.email, u.contact FROM ss_stuff s, ss_user u WHERE s.sid = :sid AND s.uid = u.uid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_available_items() {
    global $db;

    try {
        return $db->query("SELECT sid, name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true;");
    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function search_available_items($str_array) {
    global $db;

    try {
        $statement = "SELECT sid, name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true";
        $words = array();
        $i = 0;
        foreach ($str_array as $word) {
            $word = "%".strtolower($word)."%";
            $word_i = "word".$i;
            $statement .= " AND ((LOWER(name) LIKE :".$word_i.") OR (LOWER(description) LIKE :".$word_i.") OR (LOWER(pickup_locn) LIKE :".$word_i.") OR (LOWER(return_locn) LIKE :".$word_i."))";
            $words[$word_i] = $word;
            $i++;
        }
        $statement .= ";";

        $stmt = $db->prepare($statement, array(PDO::ATTR_EMULATE_PREPARES=>true));
        foreach ($words as $word_i=>$word) {
            $stmt->bindParam(':'.$word_i, $word, PDO::PARAM_STR, 256);
        }
        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

?>