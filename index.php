<?php
include_once 'app/functions.php';
sess_start('$KlQyi!');

include 'template/header.php';
?>

<!-- START OF CAROUSEL -->
<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1"></li>
        <li data-target="#myCarousel" data-slide-to="2"></li>
    </ol>
    <div class="carousel-inner">
        <div class="carousel-item active" data-interval="5000">
            <img src="./appImages/slider/slider.jpg" class="d-block w-100" alt="image1">
            <div class="container">
                <div class="carousel-caption text-left">
                    <h1>How to save for any trip?</h1>
                    <p>Here, you’ll find a plethora of resources, tips from other travelers, and secret ninja hacks that
                        will jump-start your travel fund!</p>
                    <p><a class="btn btn-lg green" href="signup.php" role="button">Sign up today</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item" data-interval="5000">
            <img src="./appImages/slider/slider1.jpg" class="d-block w-100" alt="image2">
            <div class="container">
                <div class="carousel-caption">
                    <h1>How to plan your trip?</h1>
                    <p>Planning your trip can be a lot of work! This blog will give you all the best resources I have
                        for planning your trip from start to finish.</p>
                    <p><a class="btn btn-lg green" href="blog.php" role="button">Learn more</a></p>
                </div>
            </div>
        </div>
        <div class="carousel-item" data-interval="5000">
            <img src="./appImages/slider/slider2.jpg" class="d-block w-100" alt="image3">
            <div class="container">
                <div class="carousel-caption text-right">
                    <h1>Get the right gear, insurance, tech and more...</h1>
                    <p>This blog will teach you how to buy the right backpack, what to pack, protect your data and
                        teach, and give you some other advanced tips and tricks for the road!</p>
                    <p><a class="btn btn-lg green" href="#" role="button">Sign up now</a></p>
                </div>
            </div>
        </div>
    </div>
    <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
    </a>
</div>

<!-- /END OF CAROUSEL -->


<!-- Marketing messaging and featurettes-->
<!-- Wrap the rest of the page in another container to center all the content. -->

<div class="container marketing">

    <!-- Three columns of text below the carousel -->
    <div class="row">


        <?php
//code to get 3 random posts to index.php
$link = dbConnect();
$get_posts = "SELECT * FROM posts ORDER BY RAND() LIMIT 0,3";
$run_get_posts = mysqli_query($link, $get_posts);

if (mysqli_num_rows($run_get_posts) == 0) {
    echo '<h2>No Posts.</h2>';
} else {
    while ($row_post = mysqli_fetch_array($run_get_posts)) {

        $post_title = htmlspecialchars($row_post['title']);

        //if post was created by admin, we don't use 'htmlspecialchars'
        if (verify_role($link, $row_post['uid'])) {
            $post_content = $row_post['content'];
        } else {
            $post_content = htmlspecialchars($row_post['content']);
        }

        $post_image = $row_post['image'];

        echo '
                        <div class="col-lg-4">
                                    <img src="img/blog_img/' . $post_image . '" class="rounded-circle" width="200" height="200">
                                    <h2 class="postTitle">' . $post_title . '</h2>
                                    <p class="postContent">' . $post_content . '</p>
                                    <p><a class="btn green" href="blog.php" role="button">View details &raquo;</a></p>
                        </div>
                    ';

    }
}
?>
    </div>


    <!-- START THE FEATURETTES -->

    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading">The ultimale guide to finding cheap flights in 2020.</h2>
            <p class="lead">For most trips, airfare is the most expensive part of the trip. While prices for
                transatlantic flights have gone down in recent years, they can still put a sizeable dent in any
                travel
                budget. Whether you’re a budget solo traveler or a family looking to vacation abroad, finding a
                cheap
                flight deal can be what makes or breaks your trip.

                After all, if your flight is too expensive, you’re likely going to keep putting the trip off. I’ve
                seen
                it happen time and time again.</p>
        </div>
        <div class="col-md-5">
            <img src="appImages/cheapflight.jpg" class="featurette-image img-fluid mx-auto">
        </div>
    </div>

    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7 order-md-2">
            <h2 class="featurette-heading">How to get cheap accomodation.</h2>
            <p class="lead">Accommodation will be one of your biggest daily expenses – and lowering that cost can lead
                to huge savings. To a lot of people, the choice seems to be either expensive hotels or cheap hostel
                dorms. But there are many other options available to travelers – whether you are a solo traveler,
                couple, or family. These articles will help choose the right accommodation for you, find the best deals,
                avoid being scammed, and break out of the hotel/hostel mold.</p>
        </div>
        <div class="col-md-5 order-md-1">
            <img src="appImages/accommodation.jpg" class="featurette-image img-fluid mx-auto">
        </div>
    </div>

    <hr class="featurette-divider">

    <div class="row featurette">
        <div class="col-md-7">
            <h2 class="featurette-heading">How to save money on the road.</h2>
            <p class="lead">
                If you want to travel cheaper, longer, and better, you need to make your money last on the road. Every
                penny adds up and, even if you are only traveling for two weeks, being smart with your money will make a
                huge difference in the quality and length of your trip. But how do you save money on the road? How do
                you avoid the mistakes that send people home early? I got ya. These articles have my twelve years of
                experience in them and will tell you how to save money when you’re traveling and get the best value for
                your spending!</p>
        </div>
        <div class="col-md-5">
            <img src="appImages/savemoney.jpg" class="featurette-image img-fluid mx-auto">
        </div>
    </div>

    <hr class="featurette-divider">

    <!-- /END THE FEATURETTES -->

</div><!-- /.container -->


<?php include 'template/footer.php';?>