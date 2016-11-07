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

            case "edititem":
            $redirect = isset($_GET["id"]) ? "./edititem.php?id=".$_GET["id"] : false;
            break;

            case "mystuff":
            $redirect = "./mystuff.php";
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

function is_pickup_date_within_range($pickup_date) {
	return $pickup_date >= new DateTime();
}

function is_return_date_within_range($return_date) {
	return $return_date->format('Y') <= '9999';
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
        $stmt = $db->prepare("SELECT s.sid, s.name, s.description, s.pickup_date, s.pickup_locn, s.return_date, s.return_locn, s.is_available, s.pref_price, u.uid, u.username, u.email, u.contact FROM ss_stuff s, ss_user u WHERE s.sid = :sid AND s.uid = u.uid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function close_item($sid) {
    global $db;

    try {
        $stmt = $db->prepare("UPDATE ss_stuff SET is_available = FALSE WHERE sid = :sid");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_available_items() {
    global $db;

    try {
        return $db->query("SELECT sid, name, description, pickup_date, pickup_locn, return_date, return_locn FROM ss_stuff WHERE is_available = true ORDER BY sid DESC;");
    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_items_owned_by($uid) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT sid, name, description, pickup_date, pickup_locn, return_date, return_locn, is_available FROM ss_stuff WHERE uid = :uid ORDER BY is_available DESC, sid DESC;");
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function search_available_items($str_array, $min_price, $max_price, $pickup_start, $pickup_end, $return_start, $return_end, $no_bids) {
    global $db;

    try {
        $statement = "SELECT sid, name, description, pickup_date, pickup_locn, return_date, return_locn FROM available_stuff WHERE true";
        $words = array();
        $i = 0;
        foreach ($str_array as $word) {
            $word = "%".strtolower($word)."%";
            $word_i = "word".$i;
            $statement .= " AND ((LOWER(name) LIKE :".$word_i.") OR (LOWER(description) LIKE :".$word_i.") OR (LOWER(pickup_locn) LIKE :".$word_i.") OR (LOWER(return_locn) LIKE :".$word_i."))";
            $words[$word_i] = $word;
            $i++;
        }
        $statement .= empty($min_price) ? "" : " AND pref_price >= :min_price";
        $statement .= empty($max_price) ? "" : " AND pref_price <= :max_price";
        $statement .= empty($pickup_start) ? "" : " AND pickup_date >= :pickup_start";
        $statement .= empty($pickup_end) ? "" : " AND pickup_date <= :pickup_end";
        $statement .= empty($return_start) ? "" : " AND return_date >= :return_start";
        $statement .= empty($return_end) ? "" : " AND return_date <= :return_end";
        $statement .= ($no_bids === "on") ? " AND NOT EXISTS (SELECT 1 FROM available_stuff s, ss_bid b WHERE s.sid = b.sid)" : "";
        $statement .= " ORDER BY sid DESC;";
        $stmt = $db->prepare($statement, array(PDO::ATTR_EMULATE_PREPARES=>true));
        foreach ($words as $word_i=>$word) {
            $stmt->bindParam(':'.$word_i, $word, PDO::PARAM_STR, 256);
        }
        if (!empty($min_price))
            $stmt->bindParam(":min_price", $min_price, PDO::PARAM_STR);
        if (!empty($max_price))
            $stmt->bindParam(":max_price", $max_price, PDO::PARAM_STR);
        if (!empty($pickup_start))
            $stmt->bindParam(":pickup_start", $pickup_start, PDO::PARAM_STR);
        if (!empty($pickup_end))
            $stmt->bindParam(":pickup_end", $pickup_end, PDO::PARAM_STR);
        if (!empty($return_start))
            $stmt->bindParam(":return_start", $return_start, PDO::PARAM_STR);
        if (!empty($return_end))
            $stmt->bindParam(":return_end", $return_end, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_bids($sid) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT u.username, b.bid_amount FROM ss_bid b, ss_user u
                              WHERE b.sid = :sid AND b.uid = u.uid ORDER BY b.bid_amount DESC;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function upsert_bid($uid, $sid, $bid_amount) {
    global $db;

    try {
        $statement = "INSERT INTO ss_bid (sid, uid, bid_amount) VALUES (:sid, :uid, :bid_amount)
                      ON CONFLICT (sid, uid) DO UPDATE SET bid_amount = :bid_amount";
        $stmt = $db->prepare($statement, array(PDO::ATTR_EMULATE_PREPARES=>true));
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':bid_amount', $bid_amount, PDO::PARAM_STR);

        $stmt->execute();
        return true;

    } catch (PDOException $e) {
        if ($e->getCode() == 23505) {
            return false;
        } else {
            die("We are unable to process your request. Please try again later.");
        }
    }
}

function delete_bid($uid, $sid) {
    global $db;

    try {
        $stmt = $db->prepare("DELETE FROM ss_bid WHERE uid = :uid AND sid = :sid;");
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_max_bid($sid) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT MAX(bid_amount) as max_bid FROM ss_bid WHERE sid = :sid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result == false ? false : $result["max_bid"];

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_bid_amount_for_user($sid, $uid) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT bid_amount FROM ss_bid WHERE sid = :sid AND uid = :uid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':uid', $uid, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result == false ? false : $result["bid_amount"];

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}

function get_username_for_bid($sid, $bid_amount) {
    global $db;

    try {
        $stmt = $db->prepare("SELECT u.username FROM ss_bid b, ss_user u
                              WHERE b.sid = :sid AND b.bid_amount = :bid_amount AND u.uid = b.uid;");
        $stmt->bindParam(':sid', $sid, PDO::PARAM_INT);
        $stmt->bindParam(':bid_amount', $bid_amount, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result == false ? false : $result["username"];

    } catch (PDOException $e) {
        die("We are unable to process your request. Please try again later.");
    }
}
?>