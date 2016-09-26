<?php
require('secrets.php');
try {
    $db = new PDO("pgsql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
} catch (PDOException $e) {
    die($e->getMessage());
}
?>
