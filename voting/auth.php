<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>