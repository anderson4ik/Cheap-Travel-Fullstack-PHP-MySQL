<?php
include_once 'app/functions.php';
sess_start('$KlQyi!');

//check if user is loged in
if (isset($_SESSION['uid']) && !empty($_SESSION['uid']) && is_numeric($_SESSION['uid'])) {
    header("Location: blog.php");
}

$error = "";

if (isset($_POST['submit'])) {
    if ($_POST['csrf'] === $_SESSION['csrf']) { //check csrf token

        $valid = true;

        if (empty($_POST['email'])) {
            $error .= "Email field is not valid. <br>";
            $valid = false;
        }

        if (empty($_POST['pwd'])) {
            $error .= "Password field is not valid. <br>";
            $valid = false;
        }

        if ($valid) {
            //sanitazing imputs from XSS atacks and validation to email
            $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
            $pwd = trim(filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING));

            $link = dbConnect();

            //cleaning string from quotes (') to prevent sql injection
            $email = mysqli_real_escape_string($link, $email);
            $pwd = mysqli_real_escape_string($link, $pwd);

            $sql = "SELECT * FROM users WHERE email = '$email'";
            $result = mysqli_query($link, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_all($result, MYSQLI_ASSOC); //return array assoc. inside array[0]
                $user = $user[0]; //to get ride using [0]

                if (password_verify($pwd, $user['pwd'])) { //verifying password from input with DB password
                    $_SESSION['uid'] = $user['id'];
                    $_SESSION['uname'] = $user['name'];
                    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
                    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

                    header("Location: blog.php");

                } else {
                    $error = "Email or Password are invalid!";
                }

            } else {
                $error = "Email or Password are invalid!";
            }
        }
    }
}

$csrf = csrf_token(); //creating csrf token. Must to be in the end of php script, to provide right functionality

include 'template/header.php';
?>
<link href="css/login.css" rel="stylesheet">

<div class="container">
    <div class="row">
        <div class="col-md-12 login">

            <form class="form-signin" action="" method="POST">
                <!--error message-->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?=$error;?>
                </div>
                <?php endif;?>
                <!--success message-->
                <?php if (isset($_GET['sm'])): ?>
                <div class="alert alert-success sm-box" role="alert">
                    <?=$_GET['sm'];?>
                </div>
                <?php endif;?>

                <h1 class="h3 mb-3 font-weight-normal">Please Log In</h1>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input name="email" type="text" id="inputEmail" class="form-control" placeholder="*Email address"><br>
                <label for="inputPassword" class="sr-only">Password</label>
                <input name="pwd" type="password" id="inputPassword" class="form-control" placeholder="*Password"><br>
                <input name="csrf" type="hidden" value="<?=$csrf;?>"><br>
                <!-- sending csrf-token to server to check, if user send it from our webpage -->

                <input name="submit" class="btn btn-lg btn-block" type="submit" value="Log In"
                    style="background:#07A068;">

                <p class="mt-5 mb-3">Don't have an account?<a href="signup.php" class="ml-2"><span
                            style="color:#07A068;">Sign Up</span></a></p>
            </form>

        </div>
    </div>
</div>


<?php include 'template/footer.php';?>