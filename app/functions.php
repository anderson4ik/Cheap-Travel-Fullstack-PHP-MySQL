<?php
define("HOST", "localhost");
define("USER", "root");
define("PWD", "");
define("DB", "blog");

if (!function_exists('dbConnect')) {
    function dbConnect()
    {
        $link = mysqli_connect(HOST, USER, PWD, DB);
        return $link;
    }
}

if (!function_exists('old')) {
    function old($fn)
    {
        return $_REQUEST[$fn] ?? '';
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        $token = sha1(rand(1, 99999) . '$$' . time()) . time();
        $_SESSION['csrf'] = $token;
        return $token;
    }
}

if (!function_exists('email_exist')) {
    function email_exist($link, $email)
    {
        $valid = false;
        $sql = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($link, $sql);
        if ($result && mysqli_num_rows($result) == 1) {
            $valid = true;
        }
        return $valid;
    }
}

if (!function_exists('verify_role')) {
    function verify_role($link, $user_id)
    {
        $isAdmin = false;
        $sql = "SELECT role FROM users WHERE id = '$user_id'";
        $result = mysqli_query($link, $sql);
        if ($result && mysqli_num_rows($result) == 1) {
            $role = mysqli_fetch_all($result, MYSQLI_ASSOC); //return array assoc. inside array[0]

            if ($role[0]['role'] == 8) { //check if user is admin
                $isAdmin = true;
            }
        }
        return $isAdmin;
    }
}

if (!function_exists('user_verify')) {
    function user_verify()
    {
        $verify = false;
        if (isset($_SESSION['uid'])) {
            if (isset($_SESSION['user_ip']) && $_SESSION['user_ip'] == $_SERVER['REMOTE_ADDR']) {
                if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] == $_SERVER['HTTP_USER_AGENT']) {
                    $verify = true;
                }
            }
        }
        return $verify;
    }
}

if (!function_exists('sess_start')) {
    function sess_start($name = 'null')
    {
        if ($name) {
            session_name($name); //rename session
        }

        session_start();
        session_regenerate_id(); //change the session id every time we call to the function
    }
}