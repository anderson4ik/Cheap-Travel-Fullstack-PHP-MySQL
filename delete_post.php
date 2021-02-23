<?php
include 'app/functions.php';
sess_start('$KlQyi!');

//check if user is loged in properly
if (!user_verify()) {
    header("Location: login.php");
}

$link = dbConnect();

$pid = trim(filter_input(INPUT_GET, "id", FILTER_SANITIZE_STRING));
$pid = mysqli_real_escape_string($link, $pid);
if ($pid && is_numeric($pid)) {
    $uid = $_SESSION['uid'];

    $sqlToGetImageName = "SELECT image FROM posts WHERE id = '$pid' AND uid = '$uid' LIMIT 1";
    $resultImageName = mysqli_query($link, $sqlToGetImageName);
    if ($resultImageName && mysqli_num_rows($resultImageName) == 1) {
        $imageName = mysqli_fetch_all($resultImageName, MYSQLI_ASSOC); //return array assoc. inside array[0]
        $imageName = $imageName[0]['image'];
        #delete file of image
        unlink("img/blog_img/$imageName");
    }

    $sql = "DELETE FROM posts WHERE id = '$pid' AND uid = '$uid'";
    $result = mysqli_query($link, $sql);
    if ($result && mysqli_affected_rows($link) == 1) {
        header("Location: blog.php?sm=Your post deleted.");
        exit;
    }
}

header("Location: blog.php");
exit;