<?php
include 'app/functions.php';
sess_start('$KlQyi!');
session_destroy();
header("Location: login.php");
exit;