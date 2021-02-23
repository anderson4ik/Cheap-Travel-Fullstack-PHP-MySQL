<?php
include 'app/functions.php';
sess_start('$KlQyi!');

//check if user is loged in
if (!isset($_SESSION['uid']) && empty($_SESSION['uid'])) {
    header("Location: login.php");
}

define("UPLOAD_MAX_SIZE", 1024 * 1024 * 20); //2Gb
$ex = ["jpg", "jpeg", "png", "gif", "bmp", "pdf"];
$error = "";
$link = dbConnect();
$uid = $_SESSION['uid'];
$isAdmin = verify_role($link, $uid);

if (isset($_POST['submit'])) {
    if ($_POST['csrf'] === $_SESSION['csrf']) { //check csrf token
        $valid = true;
        //trim - to delete spaces
        //filter_input - to delete dangerous symbols (Gets a specific external variable by name and optionally filters it)
        $title = trim(filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING));

        if ($isAdmin) {
            $content = trim($_POST['content']);
        } else {
            $content = trim(filter_input(INPUT_POST, 'content', FILTER_SANITIZE_STRING));
        }

        if (empty($title)) {
            $error .= "Title field is required!<br>";
            $valid = false;
        }
        if (empty($content)) {
            $error .= "Content field is required!";
            $valid = false;
        }

        if ($valid) {
            $title = mysqli_real_escape_string($link, $title);
            $content = mysqli_real_escape_string($link, $content);

            if (!empty($_FILES['image']['name'])) {
                if (is_uploaded_file($_FILES['image']['tmp_name'])) { //check if file isn't corrupted
                    if ($_FILES['image']['size'] <= UPLOAD_MAX_SIZE && $_FILES['image']['error'] == 0) { //check file size and errors
                        $file_info = pathinfo($_FILES['image']['name']); //Returns information about a file path => Array ( [dirname] => . [basename] => foto1.jpeg [extension] => jpeg [filename] => foto1 )
                        $file_ex = strtolower($file_info['extension']);
                        if (in_array($file_ex, $ex)) { //check if extention exist in array $ex
                            $fileName = time() . "_" . $_FILES['image']['name'];
                            //use time() to prevent overwriting the file with the same name
                            move_uploaded_file($_FILES['image']['tmp_name'], "img/blog_img/" . $fileName);
                        }
                    }
                }
            } else {
                $fileName = 'default.png';
            }

            $sql = "INSERT INTO posts VALUES (null, '$uid', '$title', '$content', '$fileName', NOW())";
            $result = mysqli_query($link, $sql);
            if ($result && mysqli_affected_rows($link) > 0) {
                header("Location: blog.php?sm=You post has been save successfully.");
            }
        }
    }
}

$csrf = csrf_token(); //creating csrf token. Must to be in the end of php script, to provide right functionality
include 'template/header.php';
?>

<section class="jumbotron text-center">
    <div class="container">
        <h1 class="addPost-greeting">Hi! <?=$_SESSION['uname'] . ',';?> here you can add new post.</h1>
        <p>
            <a href="blog.php" class="btn green my-2">Back to Blog</a>
        </p>
    </div>
</section>

<div class="album py-3 bg-light">
    <div class="container">

        <div class="row">
            <div class="col-10">
                <form class="form-signin" action="" method="POST" enctype="multipart/form-data">
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?=$error;?>
                    </div>
                    <?php endif;?>

                    <label for="title">Post Title</label>
                    <input name="title" type="text" id="title" class="form-control"
                        value="<?php echo old('title'); ?>"><br>
                    <label for="content">Post Content</label>
                    <textarea name="content" class="form-control" rows="5" <?php if ($isAdmin): ?>
                        <?='id="summernote"';?> <?php endif;?>> <?php echo old('content'); ?>
                        </textarea><br>
                    <label for="image"">Post Image</label>
                        <input name=" image" type="file" id="image" class="form-control"><br>
                        <input name="csrf" type="hidden" value="<?=$csrf;?>"><br>
                        <!-- sending csrf-token to server to check, if user send it from our webpage -->
                        <input name="submit" id="edit-btn" class="btn btn-lg green btn-block" type="submit"
                            value="Add Post">
                </form>
            </div>
        </div>
    </div>
</div>


<?php include 'template/footer.php';?>