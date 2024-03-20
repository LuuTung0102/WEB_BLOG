<?php

include 'connect.php';

session_start();
$user_id = $_SESSION['id'];
unset($_SESSION['id' . $user_id]);
session_destroy();

header('location:../Home.php');

?>