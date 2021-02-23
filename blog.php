<?php
include 'app/functions.php';
sess_start('$KlQyi!');

$link = dbConnect();
$sql = "SELECT posts.*, users.name, users.profile_image FROM `posts` JOIN users ON posts.uid = users.id ORDER BY posts.created_at DESC";
$result = mysqli_query($link, $sql);
if ($result && mysqli_num_rows($result) > 0) {
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC); //return array assoc. inside array[0]
}

include 'template/header.php';
?>

<section class="jumbotron text-center">
    <div class="container">
        <!--success message-->
        <?php if (isset($_GET['sm'])): ?>
        <div class="alert alert-success sm-box" role="alert">
            <?=$_GET['sm'];?>
        </div>
        <?php endif;?>

        <h1 class="greeting">Hi!
            <?php if (user_verify()): ?>
            <?=$_SESSION['uname'] . ',';?>
            <?php else: ?>
            <?='Guest,';?>
            <?php endif;?>
            welcome to the blog!</h1>
        <p class="lead text-muted p-heading">With us you can learn to travel smarter, cheaper, longer.
            You can also share your personal travel experiences. </p>
        <p>
            <?php if (user_verify()): ?>
            <a href="add_post.php" class="btn green my-2">+ Add new post</a>
            <?php else: ?>
        <p class="text-muted">If you want to add your post...</p>
        <a href="login.php" class="btn green my-2" style="width:8.75rem;">Log In</a>
        <?php endif;?>
        </p>
    </div>
</section>

<div class="album py-5">
    <div class="container">
        <div class="row">

            <?php if (!isset($posts)): ?>
            <h2>No Posts.</h2>
            <?php else: ?>
            <?php foreach ($posts as $post): ?>

            <div class="col-md-4">
                <div class="card mb-4 shadow-sm">
                    <!--if not image as default.png-->
                    <img src="img/blog_img/<?php echo $post['image']; ?>" class="bd-placeholder-img card-img-top"
                        width="100%" height="225"></img>
                    <div class="card-body">
                        <!-- turns only dangerous characters into HTML entity(<,>, </, ...), to prevent XSS atack-->
                        <h4><?=htmlspecialchars($post['title']);?></h4>
                        <p class="card-text">
                            <!-- if post created by admin as we render without "htmlspecialchars" -->
                            <?php if (verify_role($link, $post['uid'])): ?>
                            <?=$post['content'];?>
                            <?php else: ?>
                            <?=htmlspecialchars($post['content']);?>
                            <?php endif;?>
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btns-group">
                                <!-- data, we get from DB and created by DB -->
                                <div class="dbData">
                                    <img class="profileImage"
                                        src="img/profile_img/<?php echo $post['profile_image']; ?>" width="40"
                                        height="40"></img>
                                    <small class="text-muted middle"><?=$post['created_at'];?></small>
                                    <small class="text-muted"><?=htmlspecialchars($post['name']);?></small>
                                </div>
                                <div class='buttons'>
                                    <?php if (user_verify()): ?>
                                    <?php if ($post['uid'] == $_SESSION['uid']): ?>
                                    <a href="delete_post.php?id=<?=$post['id'];?>">
                                        <input type="button" class="btn btn-sm green delete_btn" value="Delete">
                                    </a>
                                    <a href="edit_post.php?id=<?=$post['id'];?>">
                                        <input type="button" class="btn btn-sm green" value="Edit">
                                    </a>
                                    <?php endif;?>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php endforeach;?>
            <?php endif;?>

        </div>
    </div>
</div>
</main>


<?php include 'template/footer.php';?>