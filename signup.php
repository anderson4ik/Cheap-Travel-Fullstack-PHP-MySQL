<?php
include_once 'app/functions.php';
sess_start('$KlQyi!');

//check if user is loged in
if (isset($_SESSION['uid']) && !empty($_SESSION['uid']) && is_numeric($_SESSION['uid'])) {
    header("Location: blog.php");
}

define("UPLOAD_MAX_SIZE", 1024 * 1024 * 20); //2Gb
$ex = ["jpg", "jpeg", "png", "gif", "bmp", "pdf"];
$error = "";

if (isset($_POST['submit'])) {
    if ($_POST['csrf'] === $_SESSION['csrf']) { //check csrf token

        $valid = true;

        if (empty($_POST['name'])) {
            $error .= "Name field is not valid. <br>";
            $valid = false;
            //validation of name
        } elseif (!preg_match("/^[a-zA-Z-' ]{3,}$/", $_POST['name'])) {
            $error .= "Invalid name format. <br>";
            $valid = false;
        }

        if (empty($_POST['email'])) {
            $error .= "Email field is not valid. <br>";
            $valid = false;
            //validation of email, if the e-mail address is not well-formed, then create an error message
        } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error .= "Invalid email format. <br>";
            $valid = false;
        }

        $link = dbConnect();

        //sanitazing an email from XSS atacks and validation to email
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL));
        //cleaning string from quotes (') to prevent sql injection
        $email = mysqli_real_escape_string($link, $email);
        if (email_exist($link, $email)) {
            $error .= "This email is already taken. <br>";
            $valid = false;
        }

        if (empty($_POST['pwd'])) {
            $error .= "Password field is not valid. <br>";
            $valid = false;
        } elseif (strlen($_POST['pwd']) < 5) {
            $error .= "Password length should be minimum 5 chars. <br>";
            $valid = false;
        }

        if ($valid) {
            //sanitazing imputs from XSS atacks
            $pwd = trim(filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING));
            $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));

            //cleaning string from quotes (') to prevent sql injection
            $pwd = mysqli_real_escape_string($link, $pwd);
            $pwd = password_hash($pwd, PASSWORD_BCRYPT); //to encode password
            $name = mysqli_real_escape_string($link, $name);

            if (!empty($_FILES['profile_image']['name'])) {
                if (is_uploaded_file($_FILES['profile_image']['tmp_name'])) { //check if file isn't corrupted
                    if ($_FILES['profile_image']['size'] <= UPLOAD_MAX_SIZE && $_FILES['profile_image']['error'] == 0) { //check file size and errors
                        $file_info = pathinfo($_FILES['profile_image']['name']); //Returns information about a file path => Array ( [dirname] => . [basename] => foto1.jpeg [extension] => jpeg [filename] => foto1 )
                        $file_ex = strtolower($file_info['extension']);
                        if (in_array($file_ex, $ex)) { //check if extention exist in array $ex
                            $fileName = time() . "_" . $_FILES['profile_image']['name'];

                            //use time() to prevent overwriting the file with the same name
                            move_uploaded_file($_FILES['profile_image']['tmp_name'], "img/profile_img/" . $fileName);
                        }
                    }
                }
            } else {
                $fileName = 'default_profile_image.jpg';
            }

            $sql = "INSERT INTO users VALUES (null, '$email', '$pwd', '$name', '$fileName', 7)";

            $result = mysqli_query($link, $sql);
            if ($result && mysqli_affected_rows($link) > 0) {

                header("Location: login.php?sm=You signup successfully, now you can sign in with your account.");

            } else {
                $error = "Something go wrong, try again later!";
            }

        }
    }
}

$csrf = csrf_token(); //creating csrf token. Must to be in the end of php script, to provide right functionality

include 'template/header.php';
?>
<link href="css/signUp.css" rel="stylesheet">

<div class="container">
    <div class="row">
        <div class="col-md-12 sign_up">
            <form class="form-signin" action="" method="POST" enctype="multipart/form-data">
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?=$error;?>
                </div>
                <?php endif;?>
                <h1 class="h3 mb-3 font-weight-normal">Please Sign Up</h1>
                <label for="inputName" class="sr-only">Full name</label>
                <input name="name" type="text" id="inputName" class="form-control" placeholder="*Full name"><br>
                <label for="inputEmail" class="sr-only">Email address</label>
                <input name="email" type="text" id="inputEmail" class="form-control" placeholder="*Email address"><br>
                <label for="inputPassword" class="sr-only">Password</label>
                <input name="pwd" type="password" id="inputPassword" class="form-control" placeholder="*Password"><br>
                <label for="profile_image">Browse Profile Image...</label>
                <input name="profile_image" type="file" id="profile_image" class="form-control">
                <input name="csrf" type="hidden" value="<?=$csrf;?>"><br>
                <!-- sending csrf-token to server to check, if user send it from our webpage -->
                <input name="submit" class="btn btn-lg btn-block" type="submit" value="Sign Up"
                    style="background:#07A068;">
                <p class="mt-5 mb-3">Already have an account?<a href="login.php" class="ml-2"><span
                            style="color:#07A068;">Log In</span></a></p>
            </form>

        </div>
    </div>
</div>


<?php include 'template/footer.php';?>