<?php
include 'app/functions.php';
sess_start('$KlQyi!');

//check if user is loged in
if (!user_verify()) {
    header("Location: login.php");
}

define("UPLOAD_MAX_SIZE", 1024 * 1024 * 20); //2Gb
$ex = ["jpg", "jpeg", "png", "gif", "bmp", "pdf"];
$error = "";
$link = dbConnect();
$uid = $_SESSION['uid'];
$isAdmin = verify_role($link, $uid);

if (isset($_GET['id']) && is_numeric($_GET['id'])) { //is_numeric -> prevent from XSS attacks (user can input only numbers)
    //and we use SELECT in sql query by it we haven't code that will run to your web page
    $post_id = mysqli_real_escape_string($link, $_GET['id']); //prevent from sql injections
    $sql = "SELECT * FROM posts WHERE id = '$post_id' AND uid = '$uid'";
    $result = mysqli_query($link, $sql);
    if ($result && mysqli_num_rows($result) == 1) {
        $post = mysqli_fetch_all($result, MYSQLI_ASSOC); //return array assoc. inside array[0]
        $post = $post[0]; //to get ride using [0]
    }
    $oldImage = $post['image'];
}

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
            $error .= "Title field is required!";
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
                            if (move_uploaded_file($_FILES['image']['tmp_name'], "img/blog_img/" . $fileName)) {
                                $image = "image = '$fileName',";

                                #delete old file of image
                                if ($oldImage != 'default.png') {
                                    unlink("img/blog_img/$oldImage");
                                }

                            } else {
                                $image = "";
                            }
                        }
                    }
                }
            } else {
                $image = "";
            }

            $sql = "UPDATE posts SET title = '$title', content = '$content', $image created_at = NOW() WHERE id = '$post_id' AND uid = '$uid'";
            $result = mysqli_query($link, $sql);
            if ($result && mysqli_affected_rows($link) > 0) {
                header("Location: blog.php?sm=You post has been edited successfully.");
            }
        }
    }
}

$csrf = csrf_token(); //creating csrf token. Must to be in the end of php script, to provide right functionality
include 'template/header.php';
?>

<main role="main">

    <section class="jumbotron text-center">
        <div class="container">
            <h1 class="addPost-greeting">Hi! <?=$_SESSION['uname'];?>, here you can edit your post.</h1>
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
                            value="<?=$post['title'];?>"><br>
                        <label for="content">Post Content</label>
                        <textarea name="content" class="form-control" <?php if ($isAdmin): ?> <?='id="summernote"';?>
                            <?php endif;?> rows="5"><?=$post['content'];?></textarea><br>
                        <?php echo "<img class='oldImage' src='img/blog_img/$oldImage' alt='post_image'>"; ?>
                        <label for="image"">Post Image</label>
                        <input name=" image" type="file" id="image" class="form-control"><br>
                            <input name="csrf" type="hidden" value="<?=$csrf;?>"><br>
                            <!-- sending csrf-token to server to check, if user send it from our webpage -->
                            <input name="submit" id="edit-btn" class="btn btn-lg green btn-block" type="submit"
                                value="Edit Post">
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>


<?php include 'template/footer.php';?>